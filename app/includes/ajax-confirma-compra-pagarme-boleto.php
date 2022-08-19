<?php

// Pedidos::transaction(function() {
// global $UA_INFO, $CONFIG, $POST, $str, $settings;
$connection = ActiveRecord\ConnectionManager::get_connection();
$connection->transaction();
try {

  $PagarMe = new PagarMe\Client($CONFIG['pagamentos']['pagarme_api_key']);

  $Clientes = Clientes::first(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]);

  $Cart = new stdClass();
  $Carrinho = $CONFIG['carrinho_all'];
  foreach ($Carrinho as $cart) {
    $Cart->valorcompra += $cart->preco_promo * $cart->quantidade;
    $Cart->frete_valor = $cart->frete_valor;
    $Cart->cupom_value = $cart->cupom_value;
    $Cart->cupom_desconto = $cart->cupom_desconto;
    $Cart->frete_tipo = $cart->frete_tipo;
    $Cart->id_cupom = $cart->id_cupom;
    $Cart->pedidos_id = $cart->pedidos_id;
    $Cart->jadlog_pudoid = $cart->jadlog_pudoid;
    $Cart->frete_prazo = $cart->frete_prazo;
  }

  $Pagamento = $POST['pagamento'];
  $PagamentoData = date('Y-m-d H:i:s');
  $PagamentoCodVenda = Pedidos::getCodidoVenda('ALF');

  $TOTAL = valor_pagamento($Cart->valorcompra, $Cart->frete_valor, $Cart->cupom_value, $Cart->cupom_desconto, $DecBol);

  $items = [];
  $ItemsClearSale = [];
  $TotalItens = 0;
  foreach ($Carrinho as $rr) {
    array_push($items, [
      'id' => CodProduto($rr->nome_produto, $rr->id_produto, $rr->codigo_produto),
      'title' => $rr->nome_produto,
      'unit_price' => number_format($rr->preco_promo, 2, '', ''),
      'quantity' => $rr->quantidade,
      'tangible' => true
    ]);
    $TotalItens += $rr->preco_promo * $rr->quantidade;
  }

  // Cria um novo pedido
  $InserirPedido = Pedidos::gerarPedido(
    $PagamentoData,
    $PagamentoCodVenda,
    $Clientes->id,
    retornaIpReal(),
    $Cart->frete_tipo,
    $Cart->frete_valor,
    $Cart->frete_prazo,
    $Cart->valorcompra,
    $TOTAL['TOTAL_CUPOM_REAL'],
    $CONFIG['desconto_boleto'],
    'Boleto',
    '',
    '',
    $UA_INFO['platform'],
    $UA_INFO['browser'],
    $UA_INFO['version'],
    $Cart->id_cupom,
    $Cart->pedidos_id,
    $Cart->jadlog_pudoid
  );

  // retorna os dados do carrinho de compras
  foreach ($Carrinho as $rs) {
    PedidosVendas::gerarVendas($InserirPedido->id, $rs->id_produto, $rs->preco_venda, $rs->preco_promo, $rs->quantidade, $rs->personalizado);

    // Adiciona items aos kits se houver
    if (count($rs->carrinho_prod->grid_kits)) {
      foreach ($rs->carrinho_prod->grid_kits as $rws) {
        PedidosVendas::gerarVendas($InserirPedido->id, $rws->produto->id, $rws->produto->preco_venda, $rws->produto->preco_promo, $rws->qtde, '');
      }
    }
  }

  // verificar se existe a necessidade de endereco para o sistema
  if ($STORE['config']['endereco']['configure']['status'] == true) {
    PedidosEnderecos::gerarEnderecos(
      $InserirPedido->id,
      $Clientes->id,
      $Clientes->endereco->nome,
      $Clientes->endereco->endereco,
      $Clientes->endereco->numero,
      $Clientes->endereco->bairro,
      $Clientes->endereco->complemento,
      $Clientes->endereco->referencia,
      $Clientes->endereco->cidade,
      $Clientes->endereco->uf,
      $Clientes->endereco->cep
    );
  }

  $transaction = $PagarMe->transactions()->create([
    'amount' => (number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, '', '') * 1),
    'payment_method' => 'boleto',
    'soft_descriptor' => $PagamentoCodVenda,
    'boleto_instructions' => 'Não receber após o vencimento',
    'expiration_date' => date('d-m-Y', strtotime("{$CONFIG['pagamentos']['boleto_venc']} days", strtotime($Pedidos['data_venda']))),
    'async' => false,
    'postback_url' => URL_BASE . 'boleto-status-pagarme',
    'customer' => [
      'external_id' => (string)$Clientes->id,
      'name' => $Clientes->nome,
      'email' => $Clientes->email,
      'phone_numbers' => [implode('+55', [null, soNumero($Clientes->telefone)])],
      'type' => (soNumero($Clientes->cpfcnpj, true) <= 11 ? 'individual' : 'corporation'),
      'country' => 'br',
      'documents' => [
        [
          'type' => (soNumero($Clientes->cpfcnpj, true) <= 11 ? 'cpf' : 'cnpj'),
          'number' => soNumero($Clientes->cpfcnpj)
        ]
      ],
    ],
    'items' => $items,
    'billing' => [
      'name' => 'Endereço de Entrega',
      'address' => [
        'country' => 'br',
        'street' => $Clientes->endereco->endereco,
        'street_number' => $Clientes->endereco->numero,
        'state' => $Clientes->endereco->uf,
        'city' => $Clientes->endereco->cidade,
        'neighborhood' => $Clientes->endereco->bairro,
        'zipcode' => soNumero($Clientes->endereco->cep)
      ]
    ],
  ]);

  // Inseri dados das transações dos pedidos
  $PedidosTransacoes = new PedidosTransacoes();
  $PedidosTransacoes->pedidos_id = $InserirPedido->id;
  $PedidosTransacoes->pagarme_id = $transaction->id;
  $PedidosTransacoes->boleto_link = $transaction->boleto_url . '?format=pdf';
  $PedidosTransacoes->save();

  // Inserir a indicacao do cliente, nesse caso e ele é um cliente novo
  $indicacao = ClientesIndicacoes::first(['conditions' => ['id_session=?', session_id()]]);
  if (isset($indicacao->id) && $indicacao->id > 0) {
    $indicacao->id_session = '';
    $indicacao->id_pedido = $InserirPedido->id;
    $indicacao->save();
  }

  // Alterar o status do pedido
  $PedidosStatus = Pedidos::find($InserirPedido->id);
  $PedidosStatus->status = 1;
  $PedidosStatus->obs = $Cart->cliente_obs;
  $PedidosStatus->save();

  // adiciona um novo logs de pedidos
  PedidosLogs::logs($InserirPedido->id, 0, 'Pedido realizado', 1);

  Carrinho::delete_all(array('conditions' => array('id_session=?', session_id())));

  $str['mensagem'] = ''
    . 'Finalizando pagamento, aguarde...'
    . sprintf('<script>window.location.href="/identificacao/finalizado?pedidos_id=%s"</script>', $InserirPedido->id);

  $connection->commit();
} catch (\Exception $exception) {
  //$str['mensagem'] = $exception->getMessage(); // . @var_export($ItemsClearSale, true);
  // $str['mensagem'] = 'Erro ao tentar finalizar o pagamento';
  // $connection->rollback();

  $str['mensagem'] = ''
    . sprintf('<script>console.log(JSON.string(%s))</script>', print_r($exception, 1))
    . 'Pedido finalizado com sucesso!'
    . sprintf('<script>window.location.href="/identificacao/meus-pedidos"</script>');

  // https://www.detalhespequenos.com.br/identificacao/meus-pedidos

  $connection->commit();
}

exit(json_encode($str, JSON_UNESCAPED_UNICODE));
