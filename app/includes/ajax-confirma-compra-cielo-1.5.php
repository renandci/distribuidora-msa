<?php

use Cielo\Cielo,
  Cielo\CieloException,
  Cielo\Transaction,
  Cielo\Holder,
  Cielo\PaymentMethod;

$connection = ActiveRecord\ConnectionManager::get_connection();
$connection->transaction();
try {
  // Pedidos::transaction(function() {
  // $Carrinho = Carrinho::cart();
  // global $UA_INFO, $CONFIG, $str, $settings;

  // $Cart = current($Carrinho);
  // $Carrinho = $CONFIG['carrinho_all'];
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

  $TOTAL = 0;
  $TOTAL = valor_pagamento($Cart->valorcompra, $Cart->frete_valor, $Cart->cupom_value, $Cart->cupom_desconto, 0.00);

  $PagamentoData = date('Y-m-d H:i:s');
  $Pagamento = $POST['pagamento'];
  $PagamentoCodVenda = Pedidos::getCodidoVenda('ALF');

  $HolderCardName = trim(substr($Pagamento['cardholderName'], 0, 50));
  $HolderCardNumber = soNumero($Pagamento['cardNumber']);
  $HolderCardExpireMes = substr(soNumero($Pagamento['cardExpiration']), 0, 2);
  $HolderCardExpireAno = substr(soNumero($Pagamento['cardExpiration']), 2);
  $HolderCardSecurityCode = soNumero($Pagamento['securityCode']);
  $HolderCardInstallments = soNumero($Pagamento['installments']);

  $HolderCardBrand = CardBrand::test($HolderCardNumber);

  try {
    $Cielo = new Cielo($CONFIG['pagamentos']['cielo_merchantid'], $CONFIG['pagamentos']['cielo_merchantkey'], ($CONFIG['pagamentos']['cielo_mode'] == 1 ? Cielo::PRODUCTION : Cielo::TEST));

    $Holder = $Cielo->holder($HolderCardNumber, $HolderCardExpireAno, $HolderCardExpireMes, Holder::CVV_INFORMED, $HolderCardSecurityCode);
    $Holder->setName($HolderCardName);
    $Order = $Cielo->order($PagamentoCodVenda, (int)number_format($TOTAL['TOTAL_COMPRA'], 2, '', ''));
    $PaymentMethod = $Cielo->paymentMethod(strtolower($HolderCardBrand), PaymentMethod::PARCELADO_LOJA, $HolderCardInstallments);
    $transaction = $Cielo->transaction(
      $Holder,
      $Order,
      $PaymentMethod,
      URL_BASE . 'indentificacao/finalizado',
      Transaction::AUTHORIZE_WITHOUT_AUTHENTICATION,
      true
    );
    $Transaction = $Cielo->transactionRequest($transaction);

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
      '',
      'Cartão',
      $HolderCardBrand,
      $HolderCardInstallments,
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

    $PedidosCielo = new PedidosCielo();
    $PedidosCielo->pedido_id = $InserirPedido->id;
    $PedidosCielo->tid = $Transaction->getTid();
    if (!$PedidosCielo->save()) {
      $str['mensagem'] .= 'Não foi possivel salvar sua transação!';
    }

    $str['mensagem'] .= ''
      . 'Pedido finalizado com sucesso!'
      . '<script>window.location.href="/identificacao/finalizado"</script>'
      . '';

    /**
     * Inserir a indicacao do cliente, nesse caso e ele é um cliente novo
     */
    $indicacao = current(ClientesIndicacoes::find('all', array('conditions' => array('id_session=?', session_id()))));
    if (isset($indicacao->id) && $indicacao->id > 0) {
      $indicacao->id_session = '';
      $indicacao->id_pedido = $InserirPedido->id;
      $indicacao->save();
    }

    Carrinho::delete_all(array('conditions' => array('id_session=?', session_id())));

    $UpStatus = Pedidos::find($InserirPedido->id);
    $UpStatus->status = 1;
    $UpStatus->obs = $Cart->cliente_obs;
    $UpStatus->save();

    PedidosLogs::logs($InserirPedido->id, 0, 'Pedido realizado', 1);
  } catch (Exception $e) {
    $str['error'] = 1;
    $str['mensagem'] = $e->getMessage();
  }
  // return true;
  // });
  $connection->commit();
} catch (\Exception $exception) {
  $str['mensagem'] = $exception->getMessage(); // . @var_export($ItemsClearSale, true);
  // $str['mensagem'] = 'Erro ao tentar finalizar o pagamento';
  $connection->rollback();
}

exit(json_encode($str, JSON_UNESCAPED_UNICODE));