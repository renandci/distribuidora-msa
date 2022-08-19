<?php

use Picpay\Payment;
use Picpay\Buyer;
use Picpay\Seller;
use Picpay\Request\PaymentRequest;
use Picpay\Exception\RequestException;

$connection = ActiveRecord\ConnectionManager::get_connection();
$connection->transaction();
try {
  // // $Carrinho = Carrinho::cart();
  // $Carrinho = $CONFIG['carrinho_all'];
  // $Cart = current($Carrinho);

  // /**
  //  * Nota: O endereco de push do cadastro de cliente
  //  * Retorna o endereco em que o status esteja ativo
  //  */
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


  $telefone = soNumero($Clientes->telefone);
  $telefone = mask($telefone, (strlen($telefone) >= 11 ? '+55 ## #####-####' : '+55 ## ####-####'));

  $TOTAL = valor_pagamento($Cart->valorcompra, $Cart->frete_valor, $Cart->cupom_value, $Cart->cupom_desconto, 0);

  $Pagamento = $POST['pagamento'];
  $PagamentoData = date('Y-m-d H:i:s');
  $PagamentoCodVenda = Pedidos::getCodidoVenda('ALF');

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
    '',
    'PicPay',
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

  // Dados da loja (PicPay Token e Seller Token)
  $Seller = new Seller($CONFIG['pagamentos']['picpay_token'], $CONFIG['pagamentos']['picpay_seller']);

  $nome = explode(' ', $Clientes->nome);
  $first_name = $nome[0];
  $last_name = end($nome);

  // Dados do comprador
  $Buyer = new Buyer($first_name, $last_name, $Clientes->cpfcnpj, $Clientes->email, $telefone);

  // Dados do pedido
  $Payment = new Payment($PagamentoCodVenda, URL_BASE . '/identificacao/picpay', $TOTAL['TOTAL'], $Buyer, sprintf('%s/identificacao/finalizado?pedidos_id=?', URL_BASE, $pe));

  // Cria uma nova requisição de pagamento com os dados da loja e do pagamento
  $PaymentRequest = new PaymentRequest($Seller, $Payment);

  // O retorno tem a url de pagamento no PicPay, o qrcode, data de expiracao e seu id do pedido
  $paymentResponse = $PaymentRequest->execute();

  // Inseri dados das transações dos pedidos
  $PedidosTransacoes = new PedidosTransacoes();
  $PedidosTransacoes->pedidos_id = $InserirPedido->id;
  $PedidosTransacoes->picpay_link = $paymentResponse->paymentUrl;
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

  // // Alterar o status do pedido apos o status da cielo
  // $PedidosStatusAfter = Pedidos::find( $InserirPedido->id );
  // $PedidosStatusAfter->cartao = $transaction->card->brand;
  // $PedidosStatusAfter->parcelas = $transaction->installments;
  // $PedidosStatusAfter->status = $str['status'];
  // $PedidosStatusAfter->save();

  // Limpa o carrinho
  // Carrinho::delete_all(['conditions' => ['id_session=?', session_id()]]);

  $str['mensagem'] = ''
    . 'Pedido finalizado com sucesso!'
    . sprintf('<img src="%s" class="img-responsive">', $paymentResponse->qrcode->base64)
    // . sprintf('<script>window.location.href="%s"</script>', $paymentResponse->paymentUrl);
    . sprintf('<a href="%s" class="btn btn-primary center-block" target="_blank">clique aqui para pagar online</a>', $paymentResponse->qrcode->content);

  // $connection->rollback();
  $connection->commit();
} catch (\Exception $exception) {
  $str['mensagem'] = $exception->getMessage(); // . @var_export($ItemsClearSale, true);
  $connection->rollback();
}

exit(json_encode($str, JSON_UNESCAPED_UNICODE));