<?php

/**
 * Finalizar pedido via mercado pago
 */

$connection = ActiveRecord\ConnectionManager::get_connection();
$connection->transaction();
try {
  // // Pedidos::transaction(function() {

  // // global $UA_INFO, $CONFIG, $POST, $str, $settings;

  // // $Carrinho = Carrinho::cart();
  // $Carrinho = $CONFIG['carrinho_all'];
  // $Cart = current($Carrinho);
  // $Clientes = Clientes::first(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]);

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

  $TOTAL = valor_pagamento($Cart->valorcompra, $Cart->frete_valor, $Cart->cupom_value, $Cart->cupom_desconto, $CONFIG['desconto_boleto']);

  $PagamentoData = date('Y-m-d H:i:s');
  $PagamentoCodVenda = Pedidos::getCodidoVenda('ALF');

  /**
   * Listagem dos itens no carrinho de compra
   */
  foreach ($Carrinho as $r) {
    $items[] = [
      'id' => CodProduto($r->nome_produto, $r->id_produto, $r->codigo_produto),
      'title' => $r->nome_produto,
      'picture_url' => Imgs::src($r->imagem, 'medium'),
      'description' => $r->subnome_produto,
      'category_id' => 'others', // Available categories at https://api.mercadopago.com/item_categories
      'quantity' => $r->quantidade,
      'unit_price' => $r->preco_promo
    ];
  }

  $nome = explode(' ', $Clientes->nome);
  $first_name = reset($nome);
  $last_name = end($nome);

  $cpfreplace = soNumero($Clientes->cpfcnpj);
  if (strlen($cpfreplace) <= 11) {
    $Type = 'CPF';
    $CpfCnpj = substr($cpfreplace, 0, 9) . '-' . substr($cpfreplace, 9);
  } else {
    $Type = 'CNPJ';
    $CpfCnpj = substr($cpfreplace, 0, 12) . '/' . substr($cpfreplace, 12);
  }

  $cartao = $POST['paymentMethodId'] != '' ? $POST['paymentMethodId'] : false;

  $payment_data = [
    'transaction_amount'    => (float)number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, '.', ''),
    'external_reference'   => $PagamentoCodVenda,
    'description'           => 'Compra Boleto - ' . $PagamentoCodVenda,
    'payment_method_id'     => 'bolbradesco',
    'payer'                 => [
      'email'             => $Clientes->email,
      'first_name'     => htmlspecialchars(addslashes((string)$first_name), ENT_COMPAT | ENT_XHTML, 'UTF-8'),
      'last_name'   => htmlspecialchars(addslashes((string)$last_name), ENT_COMPAT | ENT_XHTML, 'UTF-8'),
      'identification'   => array(
        'type' => $Type,
        'number' => $CpfCnpj
      ),
      'address' => array(
        'zip_code' => preg_replace('/\D/', '', $Clientes->endereco->cep),
        'street_name' => $Clientes->endereco->endereco,
        'street_number' => (int)$Clientes->endereco->numero,
        'neighborhood' => $Clientes->endereco->bairro,
        'city' => $Clientes->endereco->cidade,
        'federal_unit' => $Clientes->endereco->uf,
      )
    ],
    'external_reference'    => $PagamentoCodVenda,
    'statement_descriptor'  => 'Ped.: #' . $PagamentoCodVenda,
    'notification_url'      => URL_BASE . 'identificacao/finalizado',
    'additional_info'       => [
      'items' => $items,
      'payer' => [
        'first_name' => htmlspecialchars(addslashes((string)$first_name), ENT_COMPAT | ENT_XHTML, 'UTF-8'),
        'last_name' => htmlspecialchars(addslashes((string)$last_name), ENT_COMPAT | ENT_XHTML, 'UTF-8'),
        'registration_date' => date('c', strtotime($PagamentoData)),
        'phone' => [
          'area_code' => soNumero(substr($Clientes->telefone, 1, 2)),
          'number' => soNumero(substr($Clientes->telefone, 5))
        ],
        'address' => [
          'street_name' => htmlspecialchars(addslashes((string)$Clientes->endereco->endereco), ENT_COMPAT | ENT_XHTML, 'UTF-8'),
          'street_number' => (int)$Clientes->endereco->numero,
          'zip_code' => soNumero($Clientes->endereco->cep)
        ]
      ],
      'shipments' => [
        'receiver_address' => [
          'zip_code' => soNumero($Clientes->endereco->cep),
          'street_name' => htmlspecialchars(addslashes((string)$Clientes->endereco->endereco), ENT_COMPAT | ENT_XHTML, 'UTF-8'),
          'street_number' => (int)$Clientes->endereco->numero
        ]
      ]
    ]
  ];

  try {
    $mp = new MP($CONFIG['pagamentos']['mp_access_token']);
    $mp->sandbox_mode(($CONFIG['pagamentos']['mp_mode'] == 0 ? false : true));
    $return = $mp->post('/v1/payments', $payment_data);

    /**
     * Cria um novo pedido
     */
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
      0,
      $UA_INFO['platform'],
      $UA_INFO['browser'],
      $UA_INFO['version'],
      $Cart->id_cupom,
      $Cart->pedidos_id,
      $Cart->jadlog_pudoid
    );

    foreach ($Carrinho as $rs) {
      PedidosVendas::gerarVendas($InserirPedido->id, $rs->id_produto, $rs->preco_venda, $rs->preco_promo, $rs->quantidade, $rs->personalizado);
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

    $PedidosStatus = Pedidos::find($InserirPedido->id);
    $PedidosStatus->status = 1;
    $PedidosStatus->save();

    // Inseri dados das transações dos pedidos
    $PedidosTransacoes = new PedidosTransacoes();
    $PedidosTransacoes->pedidos_id     = $InserirPedido->id;
    $PedidosTransacoes->mercadopg_id  = $return['response']['collector_id'];
    $PedidosTransacoes->boleto_code   = $return['response']['barcode']['content'];
    $PedidosTransacoes->boleto_date   = date('d/m/Y', strtotime($return['response']['date_created']));
    $PedidosTransacoes->boleto_number = $return['response']['transaction_details']['verification_code'];
    $PedidosTransacoes->boleto_link   = $return['response']['transaction_details']['external_resource_url'];
    $PedidosTransacoes->save();

    // // Inicia e busca o new
    // $payment = $mp->get("/v1/payments/{$return['response']['id']}");
    // $HTTPStatusMP = StatusPagtoMP($payment['response']);
    //
    if ($payment['status'] > 0) {
      $PedidosStatusNew = Pedidos::find($InserirPedido->id);
      $PedidosStatusNew->status = 1;
      $PedidosStatus->obs = $Cart->cliente_obs;
      $PedidosStatusNew->save();

      $PedidosLogs = new PedidosLogs();
      $PedidosLogs->id_adm = 0;
      $PedidosLogs->id_pedido = $InserirPedido->id;
      $PedidosLogs->descricao = 'Pagamento Via Boleto';
      $PedidosLogs->data_envio = date('Y-m-d H:i:s');
      $PedidosLogs->status = 1;
      $PedidosLogs->save();
    }

    $str['mensagem'] .= ''
      . 'Pedido finalizado com sucesso!'
      . '<script>window.location.href="/identificacao/obrigado/?pedidos_id=' . $InserirPedido->id . '";</script>'
      . '';

    // Inserir a indicacao do cliente, nesse caso e ele é um cliente novo
    $ClientesIndicacoes = ClientesIndicacoes::first(['conditions' => ['id_session=?', session_id()]]);
    if (isset($ClientesIndicacoes->id) && $ClientesIndicacoes->id > 0) {
      $ClientesIndicacoes->id_session = '';
      $ClientesIndicacoes->id_pedido = $InserirPedido->id;
      $ClientesIndicacoes->save();
    }

    Carrinho::delete_all(['conditions' =>  ['id_session=?', session_id()]]);
  } catch (Exception $ex) {
    $str['erros'] = 1;
    $str['mensagem'] = 'Por favor, revise os dados ou tente novamente mais tarde!';
    //        $str['mensagem'] .= $ex;
    //        $str['mensagem'] .= soNumero($ex->getMessage());
    exit(json_encode($str, JSON_UNESCAPED_UNICODE));
  }
  $connection->commit();
} catch (\Exception $exception) {
  $str['mensagem'] = $exception->getMessage(); // . @var_export($ItemsClearSale, true);
  // $str['mensagem'] = 'Erro ao tentar finalizar o pagamento';
  $connection->rollback();
}

exit(json_encode($str, JSON_UNESCAPED_UNICODE));
