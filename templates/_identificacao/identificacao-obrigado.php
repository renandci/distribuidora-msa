<?php
$pedidos_id = filter_input(INPUT_GET, 'pedidos_id', FILTER_SANITIZE_NUMBER_INT) ?? 0;
$id_pagamento = filter_input(INPUT_GET, 'id_pagamento', FILTER_SANITIZE_STRING) ?? null;

$Pedidos = Pedidos::find($pedidos_id);
$TOTAL = valor_pagamento($Pedidos->valor_compra, $Pedidos->frete_valor, $Pedidos->desconto_cupom, '$', $Pedidos->desconto_boleto);

try {
  // Pedidos feitos no PagSeguro
  if (isset($id_pagamento) && $id_pagamento != '') {

    \PagSeguro\Library::initialize();
    \PagSeguro\Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
    \PagSeguro\Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");

    \PagSeguro\Configuration\Configure::setEnvironment($CONFIG['pagamentos']['pagseguro_mode']); //production or sandbox
    \PagSeguro\Configuration\Configure::setAccountCredentials($CONFIG['pagamentos']['pagseguro_email'], $CONFIG['pagamentos']['pagseguro_token']);

    $response = \PagSeguro\Services\Transactions\Search\Code::search(\PagSeguro\Configuration\Configure::getAccountCredentials(), $id_pagamento);

    if (!is_object($response) && empty($response->getCode()))
      throw new Exception("Não foi possivel realizar a transação do pag seguro, tente novamente mais tarde!");

    // Inseri dados das transações dos pedidos
    $PedidosTransacoes = new PedidosTransacoes();
    $PedidosTransacoes->pedidos_id = $Pedidos->id;
    $PedidosTransacoes->pagseguro_checkout = $response->getCode();
    $PedidosTransacoes->save();

    PedidosLogs::logs($Pedidos->id, 0, 'Aguardando pagamento', 2);

    // Ganha um novo id
    $pedidos_id = $Pedidos->id;

    $Pedidos->cartao = current(PagSeguro::getPaymentMethodsBrands($response->getPaymentMethod()->getCode()));
    $Pedidos->parcelas = $response->getInstallmentCount();
    $Pedidos->status = 2;
    $Pedidos->save();
  }

  // Somente para PagarMe
  if (!empty($Pedidos->pedido_transacao->pagarme_id)) {
    $PagarMe = new PagarMe\Client($CONFIG['pagamentos']['pagarme_api_key']);

    $PagarMeCapture = $PagarMe->transactions()->capture([
      'id' => $Pedidos->pedido_transacao->pagarme_id,
      'amount' => (number_format($TOTAL['TOTAL_COMPRA'], 2, '', '') * 1)
    ]);

    $status = $PagarMeCapture->status;

    switch ($status) {
      case 'processing':
      case 'authorized':
        $Pedidos->status = 11;
        $Pedidos->save();
        break;
      case 'waiting_payment':
        $Pedidos->status = 2;
        $Pedidos->save();
        break;
      case 'paid':
        $Pedidos->status = 3;
        $Pedidos->save();
        break;
      default:
        $Pedidos->status = 4;
        $Pedidos->save();
        break;
    }
  }
} catch (Exception $e) {
  echo '<h2>Desculpe! Hove um erro ao processar seu pagamento.</h2>';
}

// $ebit = [];
// $ebit['storeId'] 		= '103255';
// $ebit['platform'] 		= $Pedidos->platform;
// $ebit['email'] 			= $Pedidos->cliente->email;
// $ebit['gender'] 		= '';
// $ebit['birthday'] 		= $Pedidos->cliente->data_nascimento;
// $ebit['zipCode'] 		= $Pedidos->pedido_endereco->cep;
// $ebit['parcels'] 		= $Pedidos->parcelas;
// $ebit['deliveryTax']	= number_format($Pedidos->frete_valor, 2, '.', '');
// $ebit['deliveryTime'] 	= $Pedidos->frete_prazo;
// $ebit['mktSaleId'] 		= '0';

// $data = [];
// foreach( $Pedidos->pedidos_vendas as $rws ) :
// 	$data['value'][] = number_format($rws->valor_pago, 2, '.', '');
// 	$data['quantity'][] = $rws->quantidade;
// 	$data['sku'][] = CodProduto($rws->produto->id, $rws->produto->nome_produto, $rws->produto->codigo_produto);
// 	$data['productName'][] = $rws->produto->nome_produto;
// endforeach;

// $ebit['value'] 			= implode('|', $data['value']);
// $ebit['quantity'] 		= implode('|', $data['quantity']);
// $ebit['productName'] 	= implode('|', $data['productName']);
// $ebit['sku'] 			= implode('|', $data['sku']);
// $ebit['ean'] 			= '';

// $ebit['transactionId'] 	= $Pedidos->codigo;
// $ebit['paymentType'] 	= $Pedidos->forma_pagamento;
// $ebit['cardFlag'] 		= $Pedidos->cartao;
// $ebit['invoiceEmissor'] = '';


// $rs = $Pedidos->to_array();
// $TOTAL_COMPRA = (($Pedido['desconto_boleto'] ? ($Pedido['valor_compra'] -($Pedido['desconto_boleto']/100) * $Pedido['valor_compra']) : $Pedido['valor_compra']) + $Pedido['frete_valor']);

// $ebit['totalSpent'] = number_format($TOTAL_COMPRA['TOTAL_COMPRA_C_BOLETO'], 2, '.', '');

// // use Cielo\API30\Merchant;
// // use Cielo\API30\Ecommerce\Environment;
// // // use Cielo\API30\Ecommerce\Sale;
// // use Cielo\API30\Ecommerce\CieloEcommerce;
// // // use Cielo\API30\Ecommerce\Payment;
// // // use Cielo\API30\Ecommerce\Request\CieloRequestException;

// // // Configure o ambiente
// // $environment = $CONFIG['cielo_mode'] == '1' ? Environment::production() : Environment::sandbox();

// // // Configure seu merchant
// // $merchant = new Merchant($CONFIG['cielo_merchantid'], $CONFIG['cielo_merchantkey']);

// // $CieloEcommerceStatus = (new CieloEcommerce($merchant, $environment))->getSale( $Pedidos->pedido_transacao->cielo_paymentid );

// // echo '<pre>';
// // print_r($CieloEcommerceStatus);
// // echo '</pre>';

// // // recarrega uma pasta temp do servidor
// // $dir_temp = URL_VIEWS_BASE_PUBLIC_UPLOAD . 'temp/';
// // $file_temp = glob( $dir_temp . session_id() . '.{jpg,jpeg,png,gif}', GLOB_BRACE );
// // // Verifica se existe alguma imagem de personalização.
// // if( count( $file_temp ) > 0 ) {
//     // session_regenerate_id();
// // }

// $TOTAL = valor_pagamento( $Pedido['valor_compra'], $Pedido['frete_valor'], $Pedido['desconto_cupom'], '$', $Pedido['desconto_boleto'] );

$Pedido = $Pedidos->to_array([
  'include' => [
    'pedido_cliente',
    'pedido_endereco',
    'pedidos_vendas',
    'pedido_transacoes',
    'pedido_log',
    'pedidos_logs',
  ]
]);

?>

<div class="clearfix">

  <?php if (!empty($STORE['config']['ebit']['id_ebit'])) : ?>
    <a id="bannerEbit"><img src="https://www.ebitempresa.com.br/bitrate/banners/<?php echo $STORE['config']['ebit']['id_ebit'] ?> 5.gif"></a>
    <param id="ebitParam" value="storeId=<?php echo $STORE['config']['ebit']['id_ebit'] ?>&platform=<?php echo $ebit['platform'] ?>&email=<?php echo $ebit['email'] ?>&gender=&birthday=&zipCode=<?php echo $ebit['zipCode'] ?>&parcels=<?php echo $ebit['parcels'] ?>&deliveryTax=<?php echo $ebit['deliveryTax'] ?>&deliveryTime=<?php echo $ebit['deliveryTime'] ?>&mktSaleId=&totalSpent=<?php echo $ebit['totalSpent'] ?>&value=<?php echo $ebit['value'] ?>&quantity=<?php echo $ebit['quantity'] ?>&productName=<?php echo $ebit['productName'] ?>&transactionId=<?php echo $ebit['transactionId'] ?>&invoiceEmissor=&paymentType=<?php echo $ebit['paymentType'] ?>&cardFlag=<?php echo $ebit['cardFlag'] ?>&ean=&sku=<?php echo $ebit['sku'] ?>" />
  <?php endif; ?>

  <h1>Compra Realizada com Sucesso!</h1>
  <h3 class="mb10 mt0">Obrigado por Comprar na <?php echo $CONFIG['nome_fantasia'] ?>.</h3>
  <h5 class="mb5 mt0">Código da Venda: <?php echo $Pedido['codigo'] ?></h5>
  <h5 class="mb5 mt0">Data da Venda: <?php echo date('d/m/Y H:i', strtotime($Pedido['data_venda'])) ?></h5>
  <h5 class="mb5 mt0">Pagamento Via: <?php echo str_replace(['PagarMe', 'PagSeguro'], 'Cartão', $Pedido['forma_pagamento']) ?></h5>
  <h5 class="mb15 mt0">Valor a Pagar: <span class="ft16px">R$: <?php echo number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.') ?></span></h5>

  <?php /* if($Pedido['forma_pagamento'] == 'Pix') { ?>
	<h5 class="mb15 mt0">Pagamento para: <span class="ft16px"><?php echo $CONFIG['pagamentos']['pix_name']?></span></h5>
	<?php } */ ?>

  <!-- Somente para as formas de pagamento de Transferência -->
  <?php if ($Pedido['forma_pagamento'] == 'Transferência') { ?>
    <ul class="row">
      <?php
      $iCount = 1;
      foreach ($CONFIG['transferencias'] as $trans) { ?>
        <li class="col-md-6 col-xs-12 ft12px">
          <div class="panel panel-default" id="print_<?php echo $iCount ?>">
            <div class="panel-body">
              <div class="row">
                <div class="col-md-4 col-xs-12 mb15">
                  <img src="<?php echo Imgs::src(sprintf('imagens-bancos-%s', $trans['banco_logo']), 'public') ?>" class="img-responsive center-block" />
                </div>
                <div class="col-md-8 col-xs-12 text-left">
                  <b>Valor:</b>
                  <span class="ft20px">R$: <?php echo number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.') ?></span>
                  <span class="show ft16px"><?php echo $trans['banco_titulo'] ?></span>
                  <span class="show"><b>AGÊNCIA:</b> <b><?php echo $trans['banco_ag'] ?></b></span>
                  <span class="show"><b class="mb15 text-uppercase"><?php echo $trans['banco_tipocc']; ?>:</b> <b><?php echo $trans['banco_cc'] ?></b></span>
                  <span class="<?php echo $trans['banco_operacao'] == '' ? 'hidden' : 'show' ?>">
                    <b class="mb15 text-uppercase">operaçao:</b> <b><?php echo $trans['banco_operacao'] ?></b>
                  </span>
                  <span class="show"><b class="mb5">NOME:</b> <?php echo $trans['banco_razaosocial']; ?></span>
                  <span class="<?php echo $trans['banco_cpfcnpj'] == '' ? 'hidden' : 'show' ?>">
                    <b class="mb5"><?php echo ((strlen($trans['banco_cpfcnpj']) > 14) ? 'CNPJ' : 'CPF') ?>:</b>
                    <?php echo $trans['banco_cpfcnpj']; ?>
                  </span>
                  <p class="ft11px">
                    <?php
                    $endereco = !empty($CONFIG['endereco']) ? $CONFIG['endereco'] . ', ' : '';
                    $endereco .= !empty($CONFIG['numero']) ? $CONFIG['numero'] . ', ' : '';
                    $endereco .= !empty($CONFIG['cidade']) ? $CONFIG['cidade'] . '/' . $CONFIG['uf'] . ', ' : '';
                    $endereco .= !empty($CONFIG['cep']) ? mask(soNumero($CONFIG['cep']), '#####-###') : '';
                    echo trim($endereco, ',');
                    ?>
                  </p>
                </div>
                <div class="col-md-12 col-xs-12 text-left">
                  <span class="show" style="line-height: 18px">Para agilizar o processo de postagem, favor enviar a cópia do comprovante de pagamento pelo WhatsApp <?php echo $CONFIG['telefone'] ?> ou pelo e-mail <?php echo $CONFIG['email_contato'] ?></span>
                  <span class="show ft12px">Obs: Após 2 dias úteis sem o pagamento, o pedido será cancelado</span>
                </div>
                <div class="col-md-12 mb15 mt5 col-xs-12 text-center">
                  <a href="/transferencia/index.php?id=<?php echo $Pedido['id']; ?>" class="btn btn-block btn-primary" target="_blank">
                    imprimir<br />transferência
                  </a>
                </div>
              </div>
            </div>
          </div>
        </li>
      <?php $iCount++;
      } ?>
    </ul>
  <?php } ?>

  <?php if ($Pedido['forma_pagamento'] == 'Pix') { ?>

    <ul class="row" style="background-color: #f1f1f1;">
      <li class="col-md-12 mt15 mb15 col-xs-12<?php echo $MobileDetect->isMobile() || $MobileDetect->isTablet() ? ' text-center' : ''; ?>">
        <small class="ft14px show">1. Abra seu app de pagamentos ou Internet Banking.</small>
        <small class="ft14px show">2. Escolha a opção de pagamento ou transferência via PIX.</small>
        <small class="ft14px show mb15">3. Informe os dados Abaixo Quando solicitado:</small>

        <h3>Chave PIx: <?php echo $CONFIG['pagamentos']['pix_key'] ?></h3>
        <?php
        $BANCO_PIX = current(array_filter($CONFIG['transferencias'], function ($rws) {
          return $rws['banco_pix'] == '1' ? $rws : [];
        }));
        if (!empty($BANCO_PIX)) {
        ?>
          <h5 class="mt0 mb5">Banco: <?php echo $BANCO_PIX['banco_titulo'] ?></h5>
          <h5 class="mt0 mb5">Agencia: <?php echo $BANCO_PIX['banco_ag'] ?></h5>
          <h5 class="mt0 mb5">Banco: <?php echo $BANCO_PIX['banco_titulo'] ?></h5>
          <h5 class="mt0 mb5"><?php echo $BANCO_PIX['banco_tipocc'] . ': ' . $BANCO_PIX['banco_cc'] ?></h5>
        <?php } ?>
        <h5 class="mt0 mb5">Tipo de Chave: <?php echo chaves_tipo($CONFIG['pagamentos']['pix_tipo']) ?></h5>
        <h5 class="mb0 mt5">Valor a Pagar: <span class="ft16px">R$: <?php echo number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.') ?></span></h5>
        <h5 class="mb15 mt0">Nome da Conta: <?php echo $CONFIG['pagamentos']['pix_name'] ?></h5>
      </li>
    </ul>
  <?php } ?>
  <?php /* if( $Pedido['forma_pagamento'] == 'Pix' ) {
		//INSTANCIA PRINCIPAL DO PAYLOAD PIX
		$obPayload = (new \App\Pix\Payload)->setPixKey($CONFIG['pagamentos']['pix_key'])
								->setDescription(sprintf('Pgto. Refer: %s', $Pedido['codigo']))
								->setMerchantName($CONFIG['pagamentos']['pix_name'])
								->setMerchantCity($CONFIG['pagamentos']['pix_city'])
								->setAmount($TOTAL['TOTAL_COMPRA_C_BOLETO'])
								->setTxid($Pedido['codigo']);

		//CÓDIGO DE PAGAMENTO PIX
		$payloadQrCode = $obPayload->getPayload();

		$image = (new \chillerlan\QRCode\QRCode)->render($payloadQrCode);
		?>
		<ul class="row">
			<li class="col-md-2 col-xs-12">
				<img src="<?php echo ($image??null)?>" class="img-responsive center-block"/>
			</li>
			<li class="col-md-9 col-xs-12<?php echo $MobileDetect->isMobile() || $MobileDetect->isTablet() ? ' text-center':'';?>">
				<div class="hidden-xs mt5 clearfix"></div>
				<small class="show">1. Abra seu app de pagamentos ou Internet Banking.</small>
				<small class="show">2. Busque pela opção de pagamento via Pix.</small>
				<small class="show mb15">3. Copie e cole o código abaixo:</small>

				<a class="btn btn-success mb15" href="#" onclick="copy_clipboard('copy_text'); return false;" style="min-width: 250px;">copiar código de pagamento</a>
				<a class="btn btn-success mb15<?php echo $MobileDetect->isMobile() || $MobileDetect->isTablet() ? '':' ml15';?>" href="/pix/index.php?id=<?php echo $Pedidos->id?>" style="min-width: 250px;" target="_blank">imprimir qrcode de pagamento</a>
				<small class="show mb15">Cópie o código de pagamento, e efetue seu pagamento no app do banco de sua preferência.<br/>Você também pode imprimir o qrcode de pagamento no botão <strong>imprimir qrcode de pagamento</strong>.</small>
				<span id="copy_text" style="display: none;"><?php echo $payloadQrCode?></span>
			</li>
		</ul>
		<p>Atenção: Efetue o pagamento via pix em no máximo 24h para a aprovação do seu Pedido, após esse período o código perderá a validade.</p>
	<?php } */ ?>

  <span class="show ft18px">Em breve entraremos em contato para informar o andamento do seu pedido.</span>
  <span class="show ft16px mb5">Qualquer dúvida entre em contato com nossa Central de Atendimento: <?php echo $CONFIG['telefone'] ?></span>

  <?php if ($Pedido['forma_pagamento'] == 'Boleto') { ?>
    <a href="/boleto/index.php?id=<?php echo $Pedido['id']; ?>" class="btn btn-primary btn-lg btn-block center-block mb15 mt5" target="_blank" style="max-width: 320px">imprimir boleto</a>
  <?php } ?>

  <div class="mb25 row">
    <div class="col-sm-12 hidden-xs mt15">
      <table width="100%" class="table"><?php echo mail_status($Pedido) ?></table>
    </div>
    <div class="col-sm-6 col-xs-12">
      <h5>Endereço de Entrega</h5>
      <hr />
      <?php echo ($Pedido['pedido_endereco']['id'] ? "Endereço: {$Pedido['pedido_endereco']['endereco']}, {$Pedido['pedido_endereco']['numero']}" : '') ?>
      <?php echo ($Pedido['pedido_endereco']['id'] ? "<br/>Bairro: {$Pedido['pedido_endereco']['bairro']}" : '') ?>
      <?php echo ($Pedido['pedido_endereco']['id'] ? "<br/>Complemento: {$Pedido['pedido_endereco']['complemento']}" : '') ?>
      <?php echo ($Pedido['pedido_endereco']['id'] ? "<br/>Referência: {$Pedido['pedido_endereco']['referencia']}" : '') ?>
      <?php echo ($Pedido['pedido_endereco']['id'] ? "<br/>Cidade/UF: {$Pedido['pedido_endereco']['cidade']}/{$Pedido['pedido_endereco']['uf']}" : '') ?>
      <?php echo ($Pedido['pedido_endereco']['id'] ? "<br/>CEP: {$Pedido['pedido_endereco']['cep']}" : '') ?>
    </div>
    <div class="col-sm-6 col-xs-12">
      <h5>Previsão de Entrega</h5>
      <hr />
      <?php echo (!empty($Pedido['frete_prazo']) ? $Pedido['frete_prazo'] : 'O tempo de entrega será estimado após a confirmação do pagamento.') ?>
    </div>
    <div class="col-sm-12 col-xs-12">
      <h5>Status do pedido</h5>
      <hr />
      <?php
      foreach ($Pedido['pedidos_logs'] as $status) {
        echo sprintf('<i class="fa fa-check"></i> %s %s<br/>', date('d/m/Y H:i', strtotime($status['data_envio'])), strip_tags($status['descricao']));
      }
      ?>
    </div>
    <div class="col-sm-6 col-xs-12 text-right pull-right">
      <h5>Total da Compra</h5>
      <hr />
      <?php echo $Pedido['cartao'] != null        ? "<p>Cartão: {$Pedido['cartao']}</p>" : '' ?>
      <?php echo $Pedido['parcelas'] != 0        ? "<p>Parcelas: {$Pedido['parcelas']}x</p>" : '' ?>
      <?php echo $Pedido['desconto_boleto'] != null   ? '<p>Descontos <small>(Forma de pgto. ' . $Pedido['forma_pagamento'] . ')</small> ' .  floatval($Pedido['desconto_boleto']) . '%</p>' : '' ?>
      <?php echo $Pedido['frete_valor'] != null      ? '<p>Valor Frete R$: ' . number_format($Pedido['frete_valor'], 2, ',', '.') . '</p>' : '' ?>
      <?php echo $TOTAL['TOTAL'] != null      ? '<p>Valor da Compra R$: ' . number_format($TOTAL['TOTAL'], 2, ',', '.') . '</p>' : '' ?>
      <?php echo $TOTAL['TOTAL_CUPOM'] != null   ? '<p>Valor do Cupom ' . $TOTAL['TOTAL_CUPOM'] . '</p>' : '' ?>
      <?php echo $TOTAL['TOTAL_COMPRA_C_BOLETO'] != null ? '<p class="ft20px">Total da Compra R$: ' . number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.') . '</p>' : '' ?>
    </div>
  </div>

  <div class="clearfix text-center">
    <a href="/identificacao/meus-pedidos" class="btn btn-secundary mt5 mb25 mr5">meus pedidos</a>
    <a href="/" class="btn btn-secundary mt5 mb25">voltar ao site</a>
  </div>
</div>
<?php ob_start(); ?>
<script>
  copy_clipboard = function(id) {
    var copyText = document.getElementById(id).innerText;
    var elem = document.createElement("textarea");
    document.body.appendChild(elem);
    elem.value = copyText;
    elem.select();
    document.execCommand("copy");
    document.body.removeChild(elem);
    alert("Código de pagamento copiado com sucesso.");

    // var r = document.createRange();
    // r.selectNode(document.getElementById(id));
    // window.getSelection().removeAllRanges();
    // window.getSelection().addRange(r);
    // document.execCommand('copy');
    // window.getSelection().removeAllRanges();
  }
</script>
<?php
$str['script_manual'] .= ob_get_clean();
