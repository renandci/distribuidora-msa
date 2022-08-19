<?php

// Pedidos::transaction(function() {
// global $UA_INFO, $CONFIG, $POST, $str, $settings;
$connection = ActiveRecord\ConnectionManager::get_connection();
$connection->transaction();
try {

  $PagarMe = new PagarMe\Client($CONFIG['pagamentos']['pagarme_api_key']);

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

  $Pagamento = $POST['pagamento'];
  $PagamentoData = date('Y-m-d H:i:s');
  $PagamentoCodVenda = Pedidos::getCodidoVenda('ALF');

  $HolderCardName = trim(substr($Pagamento['cardholderName'], 0, 50));
  $HolderCardNumber = soNumero($Pagamento['cardNumber']);
  $HolderCardExpire = soNumero($Pagamento['cardExpiration']);
  $HolderCardExpireMes = substr(soNumero($Pagamento['cardExpiration']), 0, 2);
  $HolderCardExpireAno = substr(date('Y'), 0, 2) . substr(soNumero($Pagamento['cardExpiration']), 2);
  $HolderCardSecurityCode = soNumero($Pagamento['securityCode']);
  $HolderCardInstallments = soNumero($Pagamento['installments']);

  // Se $STORE['cartao_em_1x'] estiver ativa, recebe o desconto do boleto
  $DecBol = (isset($STORE['cartao_em_1x']) && $STORE['cartao_em_1x']) && $HolderCardInstallments == 1 ? $CONFIG['desconto_boleto'] : 0;

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

    array_push(
      $ItemsClearSale,
      new \ClearSale\Item([
        'code' => CodProduto($rr->nome_produto, $rr->id_produto, $rr->codigo_produto),
        'name' => $rr->nome_produto,
        'value' => $rr->quantidade,
        'amount' => number_format($rr->preco_promo, 2, '.', ''),
        // 'categoryID' => 1,
        // 'categoryName' => 'Item category name',
      ])
    );
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
    $DecBol,
    'Pagar Me',
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


  $TOTAL_COMPRA = (isset($STORE['cartao_em_1x']) && $STORE['cartao_em_1x']) && $HolderCardInstallments == 1 ? $TOTAL['TOTAL_COMPRA_C_BOLETO'] : $TOTAL['TOTAL_COMPRA'];

  $transaction = $PagarMe->transactions()->create([
    'amount' => (number_format($TOTAL_COMPRA, 2, '', '') * 1),
    // 'capture' => (isset($CONFIG['clearsale']['conf']) && $CONFIG['clearsale']['conf'] == true ? false : true),
    'capture' => false,
    'payment_method' => 'credit_card',
    'card_holder_name' => $HolderCardName,
    'card_cvv' => $HolderCardSecurityCode,
    'card_number' => $HolderCardNumber,
    'card_expiration_date' => $HolderCardExpire,
    'installments' => $HolderCardInstallments,
    'customer' => [
      'external_id' => (string)$Clientes->id,
      'name' => $Clientes->nome,
      'email' => $Clientes->email,
      'type' => (soNumero($Clientes->cpfcnpj, true) <= 11 ? 'individual' : 'corporation'),
      'country' => 'br',
      'documents' => [
        [
          'type' => (soNumero($Clientes->cpfcnpj, true) <= 11 ? 'cpf' : 'cnpj'),
          'number' => soNumero($Clientes->cpfcnpj)
        ]
      ],
      'phone_numbers' => [implode('+55', [null, soNumero($Clientes->telefone)])],
    ],
    'billing' => [
      // 'name' => $Clientes->nome,
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
    // 'shipping' => [
    // 'name' => 'Nome de quem receberá o produto',
    // 'fee' => 1020,
    // 'delivery_date' => '2018-09-22',
    // 'expedited' => false,
    // 'address' => [
    // 'country' => 'br',
    // 'street' => 'Avenida Brigadeiro Faria Lima',
    // 'street_number' => '1811',
    // 'state' => 'sp',
    // 'city' => 'Sao Paulo',
    // 'neighborhood' => 'Jardim Paulistano',
    // 'zipcode' => '01451001'
    // ]
    // ],
    'items' => $items
  ]);

  // Inseri dados das transações dos pedidos
  $PedidosTransacoes = new PedidosTransacoes();
  $PedidosTransacoes->pedidos_id = $InserirPedido->id;
  $PedidosTransacoes->pagarme_id = $transaction->id;
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

  $transaction = $PagarMe->transactions()->get(['id' => $transaction->id]);

  $status = $transaction->status;

  switch ($status) {
    case 'processing':
      $str['status'] = 11;
      $str['mensagem'] = 'O pagamento estão em revisão.';
      break;
    case 'authorized':
      $str['status'] = 11;
      $str['mensagem'] = 'O pagamento foi autorizado, mas ainda não capturado.';
      break;
    case 'waiting_payment':
      $str['status'] = 2;
      $str['mensagem'] = 'O pagamento foi aprovado e acreditado.';
      break;
    case 'paid':
      $str['status'] = 3;
      $str['mensagem'] = 'O pagamento foi aprovado e acreditado.';
      break;
    default:
      $str['status'] = 4;
      $str['mensagem'] = 'O pagamento foi rejeitado. O usuário pode tentar novamente.';
      break;
  }

  // // ClearSale AntiFraude
  // if( isset($CONFIG['clearsale']['conf']) && $CONFIG['clearsale']['conf'] == true ) {

  // 	switch($transaction->card->brand)
  // 	{
  // 		case 'visa' 		: $CARD_BRAND = 3; break;
  // 		case 'mastercard' 	: $CARD_BRAND = 2; break;
  // 		case 'amex' 		: $CARD_BRAND = 5; break;
  // 		case 'aura' 		: $CARD_BRAND = 7; break;
  // 		case 'elo' 			: $CARD_BRAND = 10; break;
  // 		// case 'discover' 	: $CARD_BRAND = 0; break;
  // 		// case 'jcb' 		: $CARD_BRAND = 0; break;
  // 		case 'hipercard' 	: $CARD_BRAND = 6; break;
  // 		default				: $CARD_BRAND = 4; break;
  // 	}

  // 	$ClearSale = new ClearSale\Service\Orders(
  // 		(empty($CONFIG['clearsale']['ambient']) ? new \ClearSale\Ambient\Sandbox() : new \ClearSale\Ambient\Production()),
  // 		new \ClearSale\Auth\Login($CONFIG['clearsale']['login'], $CONFIG['clearsale']['pass'])
  // 	);

  // 	$OrderClearSale = new \ClearSale\Order([
  // 		'code' => $PagamentoCodVenda,
  // 		'sessionID' => $Pagamento['SessionId'],
  // 		'date' => $PagamentoData,
  // 		'email' => $Clientes->email,
  // 		'totalValue' => number_format($TOTAL['TOTAL_COMPRA'], 2, '.', ''),
  // 		'numberOfInstallments' => $HolderCardInstallments,
  // 		'ip' => retornaIpReal(),
  // 		'b2bB2c' => 'B2C',
  // 		'isGift' => false,
  // 		'itemValue' => number_format($TotalItens, 2, '.', ''),
  // 		'ShippingPrice' => (!empty($Cart->frete_valor)?$Cart->frete_valor:'GRÁTIS'),
  // 		'deliveryTime' => $Cart->frete_prazo,
  // 		'origin' => ($MobileDetect->isMobile() || $MobileDetect->isTablet() ? 'Mobile':'Site'),
  // 		// 'country' => 'Brasil',
  // 		// 'nationality' => 'Brasileiro',
  // 		'billing' => new \ClearSale\Billing([
  // 			'clientID' => sprintf('Client%u', $Clientes->id),
  // 			'type' => (strlen(soNumero($Clientes->cpfcnpj)) <= 11 ? 1 : 2),
  // 			'name' => $Clientes->nome,
  // 			'email' => $Clientes->email,
  // 			'primaryDocument' => soNumero($Clientes->cpfcnpj),
  // 			// 'secondaryDocument' => '',
  // 			// 'birthDate' => (string)implode('-', array_reverse(explode('/', preg_replace('/\s+/', '', $Clientes->data_nascimento)))),
  // 			// 'gender' => \ClearSale\Gender::MALE,
  // 			// 'gender' => false,
  // 			'address' => new \ClearSale\Address([
  // 				'street' => $Clientes->endereco->endereco,
  // 				'number' => $Clientes->endereco->numero,
  // 				'additionalInformation' => $Clientes->endereco->complemento,
  // 				'county' => $Clientes->endereco->bairro,
  // 				'city' => $Clientes->endereco->cidade,
  // 				'state' => $Clientes->endereco->uf,
  // 				'zipcode' => soNumero($Clientes->endereco->cep),
  // 				'country' => 'Brasil',
  // 				'reference' => $Clientes->endereco->referencia
  // 			]),
  // 			'phones' => [
  // 				new \ClearSale\Phone([
  // 					'type' => \ClearSale\Phone::HOME,
  // 					'ddi' => 55,
  // 					'ddd' => substr(soNumero($Clientes->telefone), 0, 2),
  // 					'number' => substr(soNumero($Clientes->telefone), 2)
  // 				])
  // 			]
  // 		]),
  // 		'shipping' => new \ClearSale\Shipping([
  // 			'clientID' => sprintf('Client%u', $Clientes->id),
  // 			'type' => (strlen(soNumero($Clientes->cpfcnpj)) <= 11 ? 1 : 2),
  // 			'primaryDocument' => soNumero($Clientes->cpfcnpj),
  // 			// 'secondaryDocument' => '',
  // 			'name' => $Clientes->nome,
  // 			// 'birthDate' => implode('-', array_reverse(explode('/', preg_replace('/\s+/', '', $Clientes->data_nascimento)))),
  // 			'email' => $Clientes->email,
  // 			// 'gender' => \ClearSale\Gender::MALE,
  // 			'address' => new \ClearSale\Address([
  // 				'street' => $Clientes->endereco->endereco,
  // 				'number' => $Clientes->endereco->numero,
  // 				'additionalInformation' =>  $Clientes->endereco->complemento,
  // 				'county' => $Clientes->endereco->bairro,
  // 				'city' => $Clientes->endereco->cidade,
  // 				'state' => $Clientes->endereco->uf,
  // 				'zipcode' => soNumero($Clientes->endereco->cep),
  // 				'country' => 'Brasil',
  // 				'reference' => $Clientes->endereco->referencia
  // 			]),
  // 			'phones' => [
  // 				new \ClearSale\Phone([
  // 					'type' => 1,
  // 					'ddi' => 55,
  // 					'ddd' => substr(soNumero($Clientes->telefone), 0, 2),
  // 					'number' => substr(soNumero($Clientes->telefone), 2)
  // 				])
  // 			],
  // 			'deliveryType' => \ClearSale\Delivery::MAIL,
  // 			'deliveryTime' => $Pedidos->frete_prazo,
  // 			'price' => (!empty($Cart->frete_valor)?$Cart->frete_valor:'GRÁTIS'),
  // 			'pickUpStoreDocument' => ''
  // 		]),
  // 		'payments' => [
  // 			new \ClearSale\Payment([
  // 				'type' => 1,
  // 				'sequential' => 1,
  // 				'date' => $PagamentoData,
  // 				'value' => number_format($TOTAL['TOTAL_COMPRA'], 2, '.', ''),
  // 				'installments' => $HolderCardInstallments,
  // 				'interestRate' => 0,
  // 				'interestValue' => 0,
  // 				'currency' => 986,
  // 				// 'voucherOrderOrigin' => '123456',
  // 				'card' => new \ClearSale\Card([
  // 					'bin' => substr($HolderCardNumber, 0, 6),
  // 					'end' => substr($HolderCardNumber, -4),
  // 					// 'type' => \ClearSale\Card::$CARD_BRAND,
  // 					'type' => (int)$CARD_BRAND,
  // 					'validityDate' => (substr($Pagamento['cardExpiration'], 0, 3) . substr(date('Y'), 0, 2) . substr($Pagamento['cardExpiration'], 3)),
  // 					'ownerName' => $HolderCardName,
  // 					'document' => soNumero($Clientes->cpfcnpj)
  // 				]),
  // 				'address' => new \ClearSale\Address([
  // 					'street' => $Clientes->endereco->endereco,
  // 					'number' => $Clientes->endereco->numero,
  // 					'additionalInformation' => '',
  // 					'county' => $Clientes->endereco->bairro,
  // 					'city' => $Clientes->endereco->cidade,
  // 					'state' => $Clientes->endereco->uf,
  // 					'zipcode' => soNumero($Clientes->endereco->cep),
  // 					'country' => 'Brasil',
  // 					'reference' => $Clientes->endereco->referencia
  // 				])
  // 			])
  // 		],
  // 		'items' => $ItemsClearSale
  // 	]);
  // 	$SendClearSale = $ClearSale->send($OrderClearSale);
  // }

  // Alterar o status do pedido apos o status da cielo
  $PedidosStatusAfter = Pedidos::find($InserirPedido->id);
  $PedidosStatusAfter->cartao = $transaction->card->brand;
  $PedidosStatusAfter->parcelas = $transaction->installments;
  $PedidosStatusAfter->status = $str['status'];
  $PedidosStatusAfter->save();

  // Adiciona um novo logs de pedidos
  PedidosLogs::logs($InserirPedido->id, 0, $str['mensagem'], $str['status']);

  // Limpa o carrinho
  Carrinho::delete_all(['conditions' => ['id_session=?', session_id()]]);

  $str['mensagem'] = ''
    . 'Finalizando pagamento, aguarde...'
    . sprintf('<script>window.location.href="/identificacao/finalizado?pedidos_id=%s"</script>', $InserirPedido->id);

  $connection->commit();
} catch (\Exception $exception) {
  //$str['mensagem'] = $exception->getMessage(); // . @var_export($ItemsClearSale, true);
  // $str['mensagem'] = 'Erro ao tentar finalizar o pagamento';
  // $connection->rollback();

  $str['mensagem'] = ''
    . sprintf('<script>console.log(%s)</script>', print_r($exception, 1))
    . 'Pedido finalizado com sucesso!'
    . sprintf('<script>window.location.href="/identificacao/meus-pedidos"</script>');

  // https://www.detalhespequenos.com.br/identificacao/meus-pedidos

  $connection->commit();
}

exit(json_encode($str, JSON_UNESCAPED_UNICODE));
