<?php

/**
 * Finaliza pagamaneto somente para transferencia
 */
$connection = ActiveRecord\ConnectionManager::get_connection();
$connection->transaction();
try {

  $pgto = $POST['pagamento'];

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


  $TOTAL = valor_pagamento($Cart->valorcompra, $Cart->frete_valor, $Cart->cupom_value, $Cart->cupom_desconto, 0.00);

  $PagamentoData = date('Y-m-d H:i:s');
  $PagamentoCodVenda = Pedidos::getCodidoVenda('ALF');

  // Total de Pagamento no parcelamento do PagSeguro
  $InstallmentsAmount = ($POST['InstallmentsAmount'] * $pgto['installments']) - $Cart->frete_valor;

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
    $InstallmentsAmount,
    $TOTAL['TOTAL_CUPOM_REAL'],
    '',
    'PagSeguro',
    '',
    '',
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

  PedidosLogs::logs($InserirPedido->id, 0, 'Pedido realizado', 1);

  \PagSeguro\Library::initialize();
  \PagSeguro\Library::cmsVersion()->setName($CONFIG['nome_fantasia'])->setRelease('1.0.0');
  \PagSeguro\Library::moduleVersion()->setName($CONFIG['nome_fantasia'])->setRelease('1.0.0');
  \PagSeguro\Configuration\Configure::setEnvironment(empty($CONFIG['pagamentos']['pagseguro_mode']) ? 'sandbox' : 'production');
  \PagSeguro\Configuration\Configure::setAccountCredentials($CONFIG['pagamentos']['pagseguro_email'], $CONFIG['pagamentos']['pagseguro_token']);
  \PagSeguro\Configuration\Configure::setLog(true, PATH_ROOT .  '/cache/logFilename.log');
  \PagSeguro\Configuration\Configure::setCharset('UTF-8');

  if ($CONFIG['pagamentos']['pagseguro_mode'] == 'sandbox') {
    list($email, $dominio) = explode('@', trim($Clientes->email));
    $Clientes->email = $email . '@sandbox.pagseguro.com.br';
  }

  $CreditCard = new \PagSeguro\Domains\Requests\DirectPayment\CreditCard();
  $CreditCard->setReceiverEmail($CONFIG['pagseguro_email']);
  $CreditCard->setReference($PagamentoCodVenda);
  $CreditCard->setCurrency('BRL');

  foreach ($Carrinho as $items) {
    $CreditCard->addItems()->withParameters(
      CodProduto($items->nome_produto, $items->id_produto, $items->codigo_produto),
      $items->nome_produto,
      $items->quantidade,
      $items->preco_promo
    );
  }

  if (!empty($TOTAL['TOTAL_CUPOM_REAL'])) {
    $desconto_n = $TOTAL['TOTAL_CUPOM_REAL'] * (-1);
    $CreditCard->setExtraAmount($desconto_n);
  }

  $CreditCard->setShipping()->setCost()->withParameters($Cart->frete_valor);
  $CreditCard->setShipping()->setType()->withParameters(\PagSeguro\Enum\Shipping\Type::NOT_SPECIFIED);

  $CreditCard->setSender()->setName($Clientes->nome);
  $CreditCard->setSender()->setEmail($Clientes->email);

  $CreditCard->setSender()->setPhone()->withParameters(
    substr(soNumero($Clientes->telefone), 0, 2),
    substr(soNumero($Clientes->telefone), 2)
  );

  $CreditCard->setSender()->setDocument()->withParameters(
    (strlen(soNumero($Clientes->cpfcnpj)) <= 11 ? 'CPF' : 'CNPJ'),
    $Clientes->cpfcnpj
  );

  $CreditCard->setToken($POST['token']);
  $CreditCard->setSender()->setHash($POST['HashPagSeguro']);
  $CreditCard->setSender()->setIp(retornaIpReal());

  $CreditCard->setShipping()->setAddress()->withParameters(
    $Clientes->endereco->endereco,
    $Clientes->endereco->numero,
    $Clientes->endereco->bairro,
    soNumero($Clientes->endereco->cep),
    $Clientes->endereco->cidade,
    $Clientes->endereco->uf,
    'BRA'
  );

  $CreditCard->setBilling()->setAddress()->withParameters(
    $Clientes->endereco->endereco,
    $Clientes->endereco->numero,
    $Clientes->endereco->bairro,
    soNumero($Clientes->endereco->cep),
    $Clientes->endereco->cidade,
    $Clientes->endereco->uf,
    'BRA'
  );

  $CreditCard->setInstallment()->withParameters($pgto['installments'], number_format($POST['InstallmentsAmount'], 2, '.', ''));
  $CreditCard->setHolder()->setName($pgto['cardholderName']);
  $CreditCard->setHolder()->setBirthdate(preg_replace('/\s+/', '', (!empty($Clientes->data_nascimento) ? $Clientes->data_nascimento : '27/10/1987')));

  $CreditCard->setHolder()->setPhone()->withParameters(
    substr(soNumero($Clientes->telefone), 0, 2),
    substr(soNumero($Clientes->telefone), 2)
  );

  $CreditCard->setHolder()->setDocument()->withParameters(
    (strlen(soNumero($Clientes->cpfcnpj)) <= 11 ? 'CPF' : 'CNPJ'),
    $Clientes->cpfcnpj
  );

  $CreditCard->setMode('DEFAULT');

  /**
   * https://github.com/pagseguro/pagseguro-sdk-php/blob/master/public/DirectPayment/usingCreditCard.php
   * @var \PagSeguro\Domains\AccountCredentials | \PagSeguro\Domains\ApplicationCredentials $credential
   */
  $result = $CreditCard->register(\PagSeguro\Configuration\Configure::getAccountCredentials());

  $Pedidos = Pedidos::find($InserirPedido->id);
  $Pedidos->parcelas = $pgto['installments'];
  $Pedidos->cartao = $pgto['cardBrand'];
  $Pedidos->status = 1;
  $Pedidos->obs = $Cart->cliente_obs;
  $Pedidos->save();

  // Inseri dados das transações dos pedidos
  $PedidosTransacoes = new PedidosTransacoes();
  $PedidosTransacoes->pedidos_id = $InserirPedido->id;
  $PedidosTransacoes->pagseguro_checkout = $result->getCode();
  $PedidosTransacoes->save();

  // Remove o carrinho de compras
  Carrinho::delete_all(['conditions' =>  ['id_session=?', session_id()]]);

  $str['mensagem'] = ''
    . 'Finalizando pagamento, aguarde...'
    . sprintf('<script>window.location.href="/identificacao/finalizado?pedidos_id=%s"</script>', $InserirPedido->id);

  $connection->commit();
} catch (\Exception $e) {

  $msg = $e->getMessage();

  $xml = simplexml_load_string($msg);

  $json = pagseguro_object2array($xml);

  if (!empty($json['error'])) {
    foreach ($json['error'] as $k => $msg) {
      if (is_array($msg))
        $message = pagseguro_errors($msg['code']);
      else if (ctype_digit($msg))
        $message = pagseguro_errors($msg);
    }
  }

  $str['error'] = 1;
  $str['mensagem'] = 'Erro ao tentar finalizar o pagamento';
  $str['mensagem'] = $message;
  // $str['mensagem'] = print_r($e, 1);
  $connection->rollback();
}


exit(json_encode($str, JSON_UNESCAPED_UNICODE));