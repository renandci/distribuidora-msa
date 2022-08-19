<?php
// Default
header('Access-Control-Allow-Origin: *');
// STATUS DOS EMAILS DA LOJA
// 0 - PEDIDO EXCLUIDO
// 1 - PEDIDO REALIZADO
// 2 - PEDIDO AGUARDANDO PAGAMENTO
// 3 - PEDIDO PAGAMENTO APROVADO
// 4 - PEDIDO PAGAMENTO NAO APROVADO
// 5 - PEDIDO PAGAMENTO NAO EFETUADO
// 6 - PEDIDO PRODUTO EM PRODUCAO
// 7 - PEDIDO PRODUTO EM SEPARACAO DE ESTOQUE
// 8 - PEDIDO PRODUTO EM TRANSPORTE
// 9 - PEDIDO PRODUTO ENTETREGUE
// 10 - PEDIDO PEDIDO CANCELADO
use Cielo\API30\Merchant;
use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\CieloEcommerce;

use function GuzzleHttp\json_decode;

// use Cielo\API30\Ecommerce\Request\CieloRequestException;

include dirname(__DIR__) . '/topo.php';

$PEDIDO_ID = isset($GET['id']) && $GET['id'] != '' ? (int)$GET['id'] : 0;
$PEDIDO_ID = isset($POST['id']) && $POST['id'] != '' ? (int)$POST['id'] : $PEDIDO_ID;

// Tenta capturar o pedido no PagarMe
if (!empty($GET['acao']) && $GET['acao'] == 'pagarme_capture') {

  $connection = ActiveRecord\ConnectionManager::get_connection();
  $connection->transaction();
  try {
    $Pedidos = Pedidos::find($PEDIDO_ID);

    $TOTAL_CAP = valor_pagamento($Pedidos->valor_compra, $Pedidos->frete_valor, $Pedidos->desconto_cupom, '$', $Pedidos->desconto_boleto);

    $PagarMe = new PagarMe\Client($CONFIG['pagarme_api_key']);

    $capturedTransaction = $PagarMe->transactions()->capture([
      'id' => $Pedidos->pedido_transacao->pagarme_id,
      'amount' => (number_format($TOTAL_CAP['TOTAL_COMPRA'], 2, '', '') * 1)
    ]);

    $_SESSION['error'] = 'Transação capturada com sucesso!';

    Pedidos::action_cadastrar_editar(['Pedidos' => [$PEDIDO_ID => ['status' => 3]]], 'alterar', 'codigo');

    // Adiciona um novo logs de pedidos
    PedidosLogs::logs($PEDIDO_ID, $_SESSION['admin']['id_usuario'], 'Pagamento aprovado', 3);

    $connection->commit();
  } catch (\Exception $exception) {
    $_SESSION['error'] = 'Não foi possivel capturar a transação!';
    $connection->rollback();
  }
  header(sprintf('location: /adm/vendas/vendas-detalhes.php?id=%u', $PEDIDO_ID));
  return;
}

// Remove o txt do pedido do sistema
if (!empty($GET['acao']) && $GET['acao'] == 'txt_remove') {
  if (Pedidos::action_cadastrar_editar(['Pedidos' => [$PEDIDO_ID => ['nrnfe' => '', 'dheminfe' => '', "porc_nota" => 0]]], 'alterar', 'codigo')) {
    unlink($GET['arquivo']);
    header('location: /adm/vendas/vendas-detalhes.php?message=Txt removido com sucesso!&id=' . $PEDIDO_ID);
    return;
  }
}

// Remove o pedido do sistema
if (!empty($GET['acao']) && $GET['acao'] == 'excluir_pedido') {
  header('location: /adm/vendas/vendas.php');
  return;
  // if( Pedidos::action_cadastrar_editar( [ 'Pedidos' => [ (INT)$GET['id_pedido'] => [ 'excluir' => 1 ] ] ], 'delete', 'codigo') ) {
  // 	header('location: /adm/vendas/vendas.php');
  // 	return;
  // }
}

if (isset($POST['acao']) && $POST['acao'] == 'STATUS') {
  $status = (int)$POST['status'];
  $codigo = trim($POST['codigo']);
  $motivos =  isset($POST['motivos']) && $POST['motivos'] != '' ? trim($POST['motivos']) : null;
  $cod_rastreio = isset($POST['rastreio']) && $POST['rastreio'] != '' ? trim($POST['rastreio']) : null;

  $Pedidos = Pedidos::find($PEDIDO_ID);

  $status_new = $status;

  $status_old = $Pedidos->pedidos_logs[0]->status;

  // Cancelar a acao do status entregue para pedido cancelado.
  if ($status_new == 10 && $status_old == 9) {
    header('location: /adm/vendas/vendas-detalhes.php?id=' . $PEDIDO_ID . '&message=');
    return;
  }

  $varStatus   = text_status_vendas($status);

  $Descricao   = $varStatus;
  $Descricao .= $cod_rastreio ? "<br/>Cod. rastreio: {$cod_rastreio}" : '';
  $Descricao .= $motivos ? "<br/>Motivos: {$motivos}" : '';

  $Pedidos->status = $status;
  $Pedidos->rastreio = $cod_rastreio;
  $Pedidos->motivos = $motivos;
  $Pedidos->save();

  PedidosLogs::logs($PEDIDO_ID, $_SESSION['admin']['id_usuario'], $Descricao, $status);

  header('location: /adm/vendas/vendas-detalhes.php?id=' . $PEDIDO_ID);
  return;
}

if (empty($PEDIDO_ID)) {
  header('location: /adm/sair.php?acao=sair');
  return;
}

$Pedido = Pedidos::find($PEDIDO_ID);

$TOTAL = valor_pagamento($Pedido->valor_compra, $Pedido->frete_valor, $Pedido->desconto_cupom, '$', $Pedido->desconto_boleto);

// echo $nfe_cn = substr(str_pad($Pedido->data_venda->format('dmYHs'), 8, '0', STR_PAD_BOTH), -8) * 1;
// printf('<pre>%s</pre>', print_r($Pedido->pedidos_vendas[0]->produto->freteproduto, true));
?>
<style>
  <?php ob_start();

  ?>body {
    background-color: #f1f1f1
  }

  .class-border-top {
    border-top: 1px dotted #ccc;
  }

  .titulo-telemarketing {
    padding: 5px;
    font-size: 12px;
  }

  .lista-telemarketing {
    list-style: none;
    font-size: 10px;
    padding: 0;
    margin: 0;
  }

  .lista-telemarketing li {
    padding: 5px;
    line-height: 11px;
  }

  .lista-telemarketing li:nth-child(2n+1),
  .lista-logs:nth-child(2n+1) {
    background-color: #f1f1f1;
  }

  input[type=radio][name=status] {
    display: none;
  }

  .esconder-status {
    display: none;
  }

  <?php $minifier = new MatthiasMullie\Minify\CSS(ob_get_clean());
  echo $minifier->minify();
  ?>
</style>

<div id="div-edicao" class="container">
  <div class="row">
    <!--[INFORMAÇÕES ADICIONAIS]-->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <?php if (isset($GET['message']) && $GET['message'] != '') { ?>
        <div class="alert alert-info"><?php echo $GET['message'] ?></div>
      <?php } ?>

      <?php if (isset($GET['message']) && $GET['message'] == 'fail_status') { ?>
        <div class="alert alert-danger">O pedido <strong><?php echo $Pedido->codigo ?></strong> não pode ser cancelado.
        </div>
      <?php } ?>

      <?php if (isset($GET['off']) && $GET['off'] != '') { ?>
        <div class="alert alert-warning">Pedido com nova tentativa de pagamento</div>
      <?php } ?>

      <?php if (isset($_SESSION['error']) && $_SESSION['error'] !== '') { ?>
        <div class="alert alert-info"><?php echo $_SESSION['error'] ?></div>
      <?php } ?>

      <h2 class="neo-sans-medium">
        NÚMERO DO PEDIDO: <?php echo $Pedido->codigo ?>
        <small class="pull-right ft14px">DATA DA VENDA: <?php echo $Pedido->data_venda->format('d/m/Y H:i'); ?></small>
      </h2>
    </div>
    <!--[/END INFORMAÇÕES ADICIONAIS]-->

    <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
      <!--[STATUS DO PEDIDO]-->
      <div class="panel panel-default">
        <div class="panel-heading panel-store text-uppercase">Status do Pedido</div>
        <div class="panel-body text-center" id="recarrega-status">
          <?php echo status_imgs($Pedido->status) ?>
        </div>
      </div>
      <!--[/END STATUS DO PEDIDO]-->

      <div class="row">
        <!--[NFE TXT ELETRONICA]-->
        <?php if (!empty($Pedido->nrnfe) && !empty($Pedido->dheminfe) && empty($Pedido->nfe_notas->id)) { ?>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="panel panel-default">
              <div class="panel-heading panel-store text-uppercase">número do txt</div>
              <div class="panel-body">
                <strong>NR. </strong>: <?php echo $Pedido->nrnfe ?>
                <a class="btn btn-warning btn-xs pull-right" target="_blank" href="/adm/download.php?arquivo=./nfe/txt/<?php echo $Pedido->codigo ?>.txt" <?php echo _P('vendas-txt', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
                  <i class="fa fa-print"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
            <div class="panel panel-default">
              <div class="panel-heading panel-store text-uppercase">Emissão txt</div>
              <div class="panel-body">
                <?php echo date('d/m/Y H:i', strtotime($Pedido->dheminfe)); ?>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
            <div class="panel panel-default">
              <div class="panel-heading panel-store text-uppercase">ações txt</div>
              <div class="panel-body text-center">
                <a class="btn btn-danger btn-xs btn-block" href="/adm/vendas/vendas-detalhes.php?id=<?php echo $Pedido->id ?>&acao=txt_remove&arquivo=../nfe/txt/<?php echo str_pad((int)$Pedido->codigo, 8, "0", STR_PAD_LEFT) ?>.txt" <?php echo _P('vendas-txt', $_SESSION['admin']['id_usuario'], 'excluir') ?> onclick="return confirm('Deseja realmente cancelar!')">
                  cancelar txt
                </a>
              </div>
            </div>
          </div>
        <?php } ?>
        <!--[NFE TXT ELETRONICA]-->

        <!--[NFE ELETRONICA]-->
        <?php if (!empty($Pedido->nfe_notas)) { ?>

          <?php foreach ($Pedido->nfes_notas as $nfe) { ?>
            <div class="col-lg-9 col-md-9 col-sm-6 col-xs-6">
              <div class="panel panel-<?php echo $nfe->status == 1 ? 'info' : 'warning  text-warning' ?>">
                <div class="panel-heading neo-sans-medium text-uppercase">
                  número da nota: <?php echo substr($nfe->chavenfe, -18, 8) ?>
                  <?php echo $nfe->status == 3 ? '<span class="badge neo-sans-light ft10px pull-right">Nota com Devolução</span>' : '' ?>
                </div>
                <div class="panel-body">
                  <!--<a href="/adm/nfe/xml/nfe/<?php echo $nfe->chavenfe ?>.xml" target="_blank"><?php echo $nfe->chavenfe ?></a><br/>-->
                  <a href="/assets/<?php echo ASSETS ?>/xml/<?php echo $nfe->chavenfe ?>.xml" target="_blank"><?php echo $nfe->chavenfe ?></a><br />
                  <strong>NR. Recibo</strong>: <?php echo $nfe->nrrec ?><br />
                  <strong>NR. Recibo de Autorização</strong>: <?php echo $nfe->nrprot ?>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
              <div class="panel panel-<?php echo $nfe->status == 1 ? 'info' : 'warning' ?>">
                <div class="panel-heading neo-sans-medium text-uppercase">Emissão</div>
                <div class="panel-body">
                  Data de emissão<br />
                  <?php echo date('d/m/Y H:i', strtotime($nfe->dhemi)); ?>
                  <?php if (!empty($nfe->nrprot)) { ?>
                    <a class="btn btn-info btn-xs btn-block" href="/adm/nfe/nfe-imprimir.php?id_emitente=<?php echo $nfe->id_emitentes ?>&id_pedido=<?php echo $nfe->id_pedido ?>&id_nota=<?php echo $nfe->id ?>" target="_blank" <?php echo _P('nfe-imprimir', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
                      reimprimir nf-e
                    </a>
                  <?php } ?>
                </div>
              </div>
            </div>
          <?php } ?>
        <?php } ?>
        <!--[/END NFE ELETRONICA]-->

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <!--[CIELO STATUS]-->
          <?php
          if (is_object($Pedido->pedido_transacao) && $Pedido->forma_pagamento == 'Cielo') {
            try {
              // Configure o ambiente
              $environment = $CONFIG['cielo_mode'] == '1' ? Environment::production() : Environment::sandbox();

              // Configure seu merchant
              $merchant = new Merchant($CONFIG['cielo_merchantid'], $CONFIG['cielo_merchantkey']);
              // Pegar as informacoes do pagamento
              $StatusSale = (new CieloEcommerce($merchant, $environment))->getSale($Pedido->pedido_transacao->cielo_paymentid);
          ?>
              <div class="panel panel-default">
                <div class="panel-heading panel-store text-uppercase">Cielo Checkout</div>
                <div class="panel-body">
                  <?php echo $StatusSale->getPayment()->getStatus() ? CieloMensagensVendas::getMensagem($StatusSale->getPayment()->getStatus()) : ''; ?>
                  Cielo Pagamento ID: <?php echo $StatusSale->getPayment()->getPaymentId(); ?><br />
                  Cielo TID: <?php echo $StatusSale->getPayment()->getTid(); ?>
                  <?php echo $StatusSale->getPayment()->getReturnCode() ? '<br/>Cielo COD. LR: ' . $StatusSale->getPayment()->getReturnCode() : ''; ?>
                  <?php echo $StatusSale->getPayment()->getInstallments() ? '<br/>Nome Titular: ' . $StatusSale->getPayment()->getCreditCard()->getHolder() : '' ?>
                  <?php echo $StatusSale->getPayment()->getInstallments() ? '<br/>Número cartão: ' . $StatusSale->getPayment()->getCreditCard()->getCardNumber() : '' ?>
                  <?php echo $StatusSale->getPayment()->getInstallments() ? '<br/>Válidade: ' . $StatusSale->getPayment()->getCreditCard()->getExpirationDate() : '' ?>
                  <?php echo $StatusSale->getPayment()->getCreditCard()->getBrand() ? '<br/>Bandeira: ' . $StatusSale->getPayment()->getCreditCard()->getBrand() : '' ?>
                  <?php echo $StatusSale->getPayment()->getInstallments() ? '<br/>Parcelas: ' . $StatusSale->getPayment()->getInstallments() . 'x' : '' ?>
                </div>
              </div>
            <?php
            } catch (Exception $e) {
            } ?>
          <?php } ?>
          <!--[/END CIELO STATUS]-->
        </div>

        <!--[DADOS DO CLIENTE]-->
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4">
          <div class="panel panel-default">
            <div class="panel-heading panel-store text-uppercase">dados do cliente</div>
            <div class="panel-body">
              <span class="show bold ft16px mb5">Nome:
                <?php echo $Pedido->cliente->id > 0 ? $Pedido->cliente->nome : 'não informado.'; ?></span>
              <span class="show">E-mail:
                <?php echo $Pedido->cliente->id > 0 ? $Pedido->cliente->email : 'não informado.'; ?></span>
              <span class="show">Telefone:
                <?php echo $Pedido->cliente->id > 0 ? $Pedido->cliente->telefone : 'não informado.'; ?></span>
              <span class="show">Celular:
                <?php echo $Pedido->cliente->id > 0 && $Pedido->cliente->celular != '' ? sprintf('%s %s', $Pedido->cliente->celular, $Pedido->cliente->operadora) : 'não informado.'; ?></span>
              <span class="show">
                CPF/CNPJ: <?php echo $Pedido->cliente->id > 0 != '' ? $Pedido->cliente->cpfcnpj : 'não informado.'; ?>
                <a class="btn btn-info btn-xs pull-right" href="https://servicos.receita.fazenda.gov.br/Servicos/CPF/ConsultaSituacao/ConsultaPublica.asp" target="_blank">consultar</a>
              </span>
              <span class="show">Data Nasc.:
                <?php echo !empty($Pedido->cliente->data_nascimento) ? preg_replace('/\s+/', '', $Pedido->cliente->data_nascimento) : 'não informado.'; ?></span>

              <?php
              $text = sprintf('Olá *%s* %%0A %s', $Pedido->cliente->nome, URL_BASE);
              $whats_send = !empty($Pedido->cliente->telefone) && strlen(soNumero($Pedido->cliente->telefone)) >= 11 ? soNumero($Pedido->cliente->telefone) : null;
              $whats_send = !empty($Pedido->cliente->celular) && strlen(soNumero($Pedido->cliente->celular))   >= 11 ? soNumero($Pedido->cliente->celular)  : $whats_send;
              if ($whats_send) { ?>
                <a href="https://wa.me/55<?php echo $whats_send ?>?text=<?php echo $text ?>" target="_blank" class="btn btn-xs btn-success">
                  <i class="fa fa-whatsapp"></i> iniciar conversa
                </a>
              <?php } ?>

            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4" id="endereco_entrega">
          <div class="panel panel-default">
            <div class="panel-heading panel-store text-uppercase">endereço de entrega</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-7 col-xs-12">
                  <!-- <?php echo $Pedido->pedido_endereco->id > 0 ? sprintf('<strong class="show">Nome: %s</strong>', $Pedido->pedido_endereco->nome) : ''; ?> -->
                  <span class="show bold ft16px mb5">Endereço:
                    <?php echo $Pedido->pedido_endereco->endereco ? sprintf('%s, %s', $Pedido->pedido_endereco->endereco, $Pedido->pedido_endereco->numero) : 'não informado.'; ?></span>
                  <span class="show">Bairro:
                    <?php echo $Pedido->pedido_endereco->bairro ? $Pedido->pedido_endereco->bairro : 'não informado.'; ?></span>
                  <span class="show">Complemento:
                    <?php echo $Pedido->pedido_endereco->complemento ? $Pedido->pedido_endereco->complemento : 'não informado.'; ?></span>
                  <span class="show">Referências:
                    <?php echo $Pedido->pedido_endereco->referencia ? $Pedido->pedido_endereco->referencia : 'não informado.'; ?></span>
                  <span class="show">Cidade/UF:
                    <?php echo $Pedido->pedido_endereco->cidade ? sprintf('%s/%s', $Pedido->pedido_endereco->cidade, $Pedido->pedido_endereco->uf) : 'não informado.'; ?></span>
                  <span class="show">CEP:
                    <?php echo $Pedido->pedido_endereco->cep > 0 ? $Pedido->pedido_endereco->cep : 'não informado.'; ?></span>
                </div>
                <div class="col-md-5 col-xs-12">
                  <span class="show ft16px bold text-uppercase mb5">Emissão de NFe:</span>
                  <span class="show neo-sans-medium ft12px text-uppercase">Cód Municipío:
                    <?php echo $Pedido->pedido_endereco->cod_ibge->cod_ibge > 0 ? substr($Pedido->pedido_endereco->cod_ibge->cod_ibge, 2) : 'não informado.'; ?></span>
                  <span class="show neo-sans-medium ft12px text-uppercase">Cód UF:
                    <?php echo $Pedido->pedido_endereco->cod_ibge->cod_ibge > 0 ? substr($Pedido->pedido_endereco->cod_ibge->cod_ibge, 0, 2) : 'não informado.'; ?></span>
                </div>
              </div>
              <span class="show text-right">
                <a href="/adm/vendas/vendas-enderecos.php?id=<?php echo $Pedido->id ?>&id_end=<?php echo $Pedido->pedido_endereco->id ?>" class="btn btn-info btn-xs" id="pedido_alterar_endereco" data-nr="<?php echo $Pedido->codigo ?>" <?php echo _P('vendas-enderecos', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
                  <i class="fa fa-edit"></i> editar</a>
              </span>
            </div>
          </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pull-left">
          <div class="panel panel-default">
            <div class="panel-heading panel-store text-uppercase">formas de pagamento</div>
            <div class="panel-body text-center">
              <strong class="show text-uppercase bold ft20px text-left">
                <span><?php echo $Pedido->forma_pagamento ?></span><br />
                <font color="#a20000">R$: <?php echo number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.') ?>
                </font>
              </strong>

              <?php if ($Pedido->forma_pagamento == 'Pix') { ?>
                <span class="show">Identificação do pix pelo nosso número:
                  <?php echo strtotime($Pedido->data_venda) ?></span>
                <a href="../../pix/index.php?id=<?php echo $GET['id'] ?>" target="_blank" class="btn btn-warning-default btn-sm mr5">ver pix</a>
                <?php
                $obPayload = (new \App\Pix\Payload)->setPixKey($CONFIG['pagamentos']['pix_key'])
                  ->setDescription(sprintf('Pgto. Refer: %s', $Pedido->codigo))
                  ->setMerchantName($CONFIG['pagamentos']['pix_name'])
                  ->setMerchantCity($CONFIG['pagamentos']['pix_city'])
                  ->setAmount($TOTAL['TOTAL_COMPRA_C_BOLETO'])
                  ->setTxid($Pedido->codigo);

                //CÓDIGO DE PAGAMENTO PIX
                $payloadQrCode = $obPayload->getPayload();

                $btn_pix = 'https://wa.me/55' . (strlen(soNumero($Pedido->cliente->telefone)) >= 11 ? soNumero($Pedido->cliente->telefone) : soNumero($Pedido->cliente->celular)) . '?text='
                  . sprintf('%s%%0a*Olá %s*.%%0a%%0a Abra seu app de pagamentos ou Internet Banking.%%0aBusque pela opção de pagamento via Pix.%%0aCopie e cole o código abaixo:%%0a%%0a%s%%0a', URL_BASE, $Pedido->cliente->nome, $payloadQrCode);
                ?>
                <a href="javascript: void(0);" onclick="window.open('<?php echo $btn_pix ?>', 'WatsApp', 'width=800, height=600')" target="_blank" class="btn btn-primary-default btn-sm">enviar whatsapp</a>
              <?php } elseif ($Pedido->forma_pagamento == 'Boleto') { ?>
                <span class="show">Identificação do boleto pelo nosso número:
                  <?php echo strtotime($Pedido->data_venda) ?></span>
                <a href="../../boleto/index.php?id=<?php echo $GET['id'] ?>" target="_blank" class="btn btn-warning-default btn-sm">ver boleto</a>
              <?php } elseif ($Pedido->forma_pagamento == 'Transferência') { ?>
                <span class="show">Identificação do boleto pelo nosso número:
                  <?php echo strtotime($Pedido->data_venda) ?></span>
                <a href="../../transferencia/index.php?id=<?php echo $Pedido->codigo ?>" target="_blank" class="btn btn-warning-default btn-sm">ver transferência</a>
              <?php } else { ?>
                <?php echo $Pedido->cartao ? sprintf('Bandeira: %s', $Pedido->cartao) : null ?>
                <?php echo $Pedido->parcelas ? sprintf(' - %sx', $Pedido->parcelas) : null ?>
              <?php } ?>
              <?php if (!empty($Pedido->pedido_transacao->pagarme_id)) { ?>
                <span class="show bold">#<?php echo $Pedido->pedido_transacao->pagarme_id ?></span>
              <?php } ?>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pull-right" id="mapa">
          <div class="panel panel-default">
            <div class="panel-heading panel-store text-uppercase">local da compra/ip: <?php echo $Pedido->ip ?> -
              <?php echo $Pedido->platform ?> <?php echo $Pedido->browser ?></div>
            <div class="panel-body">
              <div id='gmap_canvas' style='min-height:275px; width:100%;'></div>
              <style>
                #gmap_canvas img {
                  max-width: none !important;
                  background: none !important
                }
              </style>
            </div>
          </div>

          <?php ob_start(); ?>
          <script src='https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyCarGFF_WSsunQec6-H-yF9dPgh2kCL_dM'>
          </script>
          <?php $SCRIPT['bibliotecas'] .= ob_get_clean(); ?>

          <?php
          try {
            // $get_contents = file_get_contents_utf8(sprintf('http://www.localizaip.com.br/api/iplocation.php?ip=%s', $Pedido->ip));
            // $get_contents = file_get_contents(sprintf('http://ip-api.com/json/%s?fields=lat,lon', $Pedido->ip));

            $get_contents = file_get_contents_utf8(sprintf('http://api.ipstack.com/%s?access_key=b3ab1ec3fa69c0a16c789e63f088abd3', $Pedido->ip));
            $maps = json_decode($get_contents, true);
            if (empty($maps)) {
              throw new Exception('');
            }
            ob_start();
          ?>
            <script>
              init_map = function() {
                var myOptions = {
                  zoom: 12,
                  center: new google.maps.LatLng("<?php echo dinheiro($maps['latitude']) ?>",
                    "<?php echo dinheiro($maps['longitude']) ?>"),
                  mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById('gmap_canvas'), myOptions);
                marker = new google.maps.Marker({
                  map: map,
                  position: new google.maps.LatLng("<?php echo dinheiro($maps['latitude']) ?>",
                    "<?php echo dinheiro($maps['longitude']) ?>")
                });
                infowindow = new google.maps.InfoWindow({
                  content: '' +
                    '<strong><?php echo $Pedido->pedido_endereco->nome; ?></strong><br/>' +
                    'Endereço: <?php echo $Pedido->pedido_endereco->endereco ?> - <?php echo $Pedido->pedido_endereco->numero; ?><br/>' +
                    'Cidade/UF: <?php echo addslashes($Pedido->pedido_endereco->cidade) ?>/<?php echo $Pedido->pedido_endereco->uf; ?><br/>' +
                    'CEP: <?php echo $Pedido->pedido_endereco->cep; ?>'
                });
                google.maps.event.addListener(marker, 'click', function() {
                  infowindow.open(map, marker);
                });
                infowindow.open(map, marker);
              };
              google.maps.event.addDomListener(window, "load", init_map);
            </script>
          <?php $SCRIPT['script_manual'] .= ob_get_clean();
          } catch (\Exception $th) { ?>
            <script>
              $("#mapa").addClass("hidden");
              $("#mapa").prev().prev().removeAttr("class").addClass("col-lg-6 col-md-6 col-sm-12 col-xs-12");
              $("#mapa").prev().removeAttr("class").addClass("col-lg-6 col-md-6 col-sm-12 col-xs-12");
            </script>
          <?php } ?>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pull-left" id="reload_buttons">
          <div class="panel panel-default">
            <div class="panel-heading panel-store text-uppercase">formas de envio</div>
            <div class="panel-body">
              <span class="show text-uppercase bold ft20px">
                <span class=""><?php echo $Pedido->frete_tipo ?></span> - <font color="#a20000">R$:
                  <?php echo number_format($Pedido->frete_valor, 2, ',', '.') ?></font>
              </span>
              <div class="text-center">
                <?php
                if (in_array($Pedido->status, [7, 8]) && $Pedido->rastreio) {
                  $rastreio = $Pedido->rastreio;
                  $rastreio = ($Pedido->correio_etiqueta->etiqueta ? mask($Pedido->correio_etiqueta->etiqueta, sprintf("##########%s##", $Pedido->correio_etiqueta->dv)) : $rastreio);
                  $rastreio = ($Pedido->jadlog_etiqueta->codigo ? $Pedido->jadlog_etiqueta->codigo : $rastreio);
                  echo ($rastreio ? rastreio($rastreio) : null);
                }
                ?>

                <?php if (in_array($Pedido->status, [7, 8]) && empty($Pedido->rastreio) && empty($Pedido->jadlog_etiqueta->id)) { ?>
                  <?php if (empty($Pedido->correio_etiqueta->id) && $Pedido->correio_etiqueta->id == null) { ?>
                    <a href="/adm/correios/correios-gerar-etiquetas.php?acao=gerar_etiquetas&id=<?php echo $PEDIDO_ID ?>" class="btn btn-sm btn-warning btn_gerar_etiquetas mb15" data-nr="<?php echo $Pedido->codigo; ?>" <?php echo _P('correios-gerar-etiquetas', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
                      gerar etiqueta correios
                      <strong class="hidden ft12px" data-frete="correios">Carregando...</strong>
                    </a>
                  <?php } else { ?>
                    <a href="/adm/correios/correios-gerar-etiquetas.php?acao=remover_etiquetas&etiquetas_id=all&id=<?php echo $PEDIDO_ID ?>" class="btn btn-danger btn-xs btn_remover_etiquetas" <?php echo _P('correios-gerar-etiquetas', $_SESSION['admin']['id_usuario'], 'excluir') ?> data-etiqueta="<?php echo $Pedido->correio_etiqueta->etiqueta ?>">
                      remover etiqueta
                    </a>
                    <a href="/adm/correios/correios-print.php?imprimir_tipo=etiquetas_a4&etiquetas_id=<?php echo $Pedido->correio_etiqueta->id ?>" class="btn btn-info btn-xs" <?php echo _P('correios-print', $_SESSION['admin']['id_usuario'], 'excluir') ?> target="_blank">
                      imprimir etiqueta
                    </a>
                    <hr class="mt15 mb5" />
                    <?php echo rastreio(($Pedido->correio_etiqueta->etiqueta ? mask($Pedido->correio_etiqueta->etiqueta, sprintf("##########%s##", $Pedido->correio_etiqueta->dv)) : null)); ?>
                  <?php } ?>
                <?php } ?>

                <?php if (in_array($Pedido->status, [7, 8]) && empty($Pedido->rastreio) && empty($Pedido->correio_etiqueta->id)) { ?>
                  <?php if (empty($Pedido->jadlog_etiqueta->id) && $Pedido->jadlog_etiqueta->id == null) { ?>
                    <a href="/adm/jadlog/jadlog-etiquetas-action.php?acao=gerar_etiquetas&id=<?php echo $Pedido->id ?>" class="btn btn-sm btn-danger btn_gerar_etiquetas_jadlog mb15" data-nr="<?php echo $Pedido->codigo; ?>" <?php echo _P('jadlog-etiquetas-action', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
                      gerar etiqueta jadlog
                      <strong class="hidden ft12px" data-frete="jadlog">Carregando...</strong>
                    </a>
                  <?php } else { ?>
                    <a href="/adm/jadlog/jadlog-etiquetas-action.php?acao=remover_etiquetas&etiquetas_id=<?php echo $Pedido->jadlog_etiqueta->id ?>&id_pedido=<?php echo $Pedido->id ?>" class="btn btn-danger btn-xs btn_remover_etiquetas_jadlog" <?php echo _P('jadlog-etiquetas-action', $_SESSION['admin']['id_usuario'], 'excluir') ?> data-etiqueta="<?php echo $Pedido->jadlog_etiqueta->codigo ?>">
                      remover etiqueta
                    </a>
                    <a href="/adm/jadlog/jadlog-print.php?imprimir_tipo=etiquetas_a4&etiquetas_id=<?php echo $Pedido->id ?>" class="btn btn-info btn-xs" <?php echo _P('jadlog-print', $_SESSION['admin']['id_usuario'], 'excluir') ?> target="_blank">
                      imprimir etiqueta
                    </a>
                    <hr class="mt15 mb5" />
                    <?php echo rastreio(($Pedido->jadlog_etiqueta->codigo ? $Pedido->jadlog_etiqueta->codigo : null)) ?>
                  <?php } ?>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-12"></div>
        <?php
        if (!empty($Pedido->frete_pudoid) && $Pedido->frete_pudoid != null) {
          $PickupPoints = new JadLogNew($CONFIG['jadlog']['token']);
          $ReturnPickupPoints = $PickupPoints->post(sprintf('/pickup/pudos/%s', soNumero($Pedido->pedido_endereco->cep)));
          $array = $ReturnPickupPoints['body']->pudos;
        ?>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
              <div class="panel-heading panel-store text-uppercase">ponto de coleta</div>
              <div class="panel-body">
                <?php
                echo array_reduce($array, function ($html, $data) use ($Pedido) {
                  if (!empty($Pedido->frete_pudoid) && $Pedido->frete_pudoid == $data->pudoId) {
                    $html = '';
                    $html .= '<table width="100%" cellpadding="0" bgcolor="#f9f9f9">';
                    $html .= sprintf('<tr><td class="ft20px">%s - <span class="ft10px">%s</span></td></tr>', $data->razao, $data->responsavel);
                    $html .= '<tr><td class="ft16px mb5">';
                    $html .= '<ul class="ft12px">';
                    $html .= sprintf('<li class="mb5">Endereço: %s, %s</li>', $data->pudoEnderecoList[0]->endereco, $data->pudoEnderecoList[0]->numero);
                    $html .= sprintf('<li class="mb5">Bairro: %s</li>', $data->pudoEnderecoList[0]->bairro);
                    $html .= sprintf('<li class="mb5">Cidade/UF: %s/%s</li>', $data->pudoEnderecoList[0]->cidade, $data->pudoEnderecoList[0]->uf);
                    $html .= '</ul>';
                    $html .= sprintf('<span class="show">CNPJ: %s</span>', $data->cnpjCpf);
                    $html .= sprintf('<span class="show" style="color: #a20000">%s</span>', $str['jadlog_pudo_id']);
                    // $html .= '<span class="show" style="color: #a20000">Atençao: você devera reritar seu pedido em nossa filial</span>';
                    $html .= '</td></tr>';
                    $html .= '</table>';
                  }
                  return $html;
                });
                ?>
              </div>
            </div>
          </div>
        <?php } ?>

        <?php if ($Pedido->indicacao->id > 1 && $Pedido->indicacao->id_pedido > 0) { ?>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4">
            <div class="panel panel-default">
              <div class="panel-heading panel-store text-uppercase">indicação do cliente</div>
              <div class="panel-body">
                <span class="show mb5 bold ft16px text-underline">COMO NOS CONHECEU</span>
                <?php
                if ($Pedido->indicacao->indicacao == 'OUTROS')
                  echo sprintf('<strong class="show">%s<strong><span class="ft13px show">%s</span>', $Pedido->indicacao->indicacao, $Pedido->indicacao->outros);
                elseif ($Pedido->indicacao->indicacao != '')
                  echo sprintf('<strong class="show">%s<strong><span class="ft13px show">%s</span>', $Pedido->indicacao->indicacao, $Pedido->indicacao->outros);
                else
                  sprintf('%s', $Pedido->indicacao->indicacao);
                ?>
              </div>
            </div>
          </div>
        <?php } ?>
        <!--[/END DADOS DO CLIENTE]-->
        <!--[DADOS DE COMPRAS]-->
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
          <div class="panel panel-default">
            <div class="panel-heading panel-store text-uppercase">produtos adquiridos</div>
            <div class="panel-body">
              <table class="table table-condensed">
                <tr>
                  <th colspan="2">Produtos Adquiridos</th>
                  <th class="text-center">Qtde</th>
                  <th class="text-center">Valor <small class="show">(Qtd * Unit)</small></th>
                </tr>
                <?php
                $i = 0;
                $QTDE = 0;
                $VALOR_PRODUTOS = 0;
                $kit_hidden = [];
                $cubagem = null;
                $cubagem['altura'] = 0;
                $cubagem['largura'] = 0;
                $cubagem['comprimento'] = 0;
                foreach ($Pedido->pedidos_vendas as $rr) :

                  foreach ($rr->produto->grid_kits as $tt)
                    $kit_hidden[] = $tt->produto->id;

                ?>
                  <tr<?php echo (in_array($rr->id_produto, $kit_hidden) > 0 ? ' class="hidden"' : null) ?>>
                    <td nowrap="nowrap" width="1%">
                      <img src="<?php echo Imgs::src($rr->produto->capa->imagem, 'smalls'); ?>" style="vertical-align: middle; width: 70px;" width="70" />
                    </td>
                    <td>
                      <div class="row">
                        <p class="ft16px col-lg-12 col-md-12 col-sm-12 col-xs-12">
                          <?php echo $rr->produto->nome_produto; ?></p>
                        <span class="show ft12px mb5 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                          <?php
                          echo $rr->produto->codigo_referencia ? sprintf('<span class="show">Referência: %s</span>', $rr->produto->codigo_referencia) : '';
                          echo '<span class="show">Cod.: ' . CodProduto($rr->produto->nome_produto, $rr->produto->id, $rr->produto->codigo_produto) . '</span>';
                          echo $rr->produto->marca->marcas ? "<span class='show'>Marca: {$rr->produto->marca->marcas}</span>" : '';
                          echo $rr->produto->cor->nomecor ? "<span class='show'>Cor: {$rr->produto->cor->nomecor}</span>" : '';
                          echo $rr->produto->tamanho->nometamanho ? "<span class='show'>Tam: {$rr->produto->tamanho->nometamanho}</span>" : '';
                          ?>
                        </span>
                        <?php
                        if (!empty($rr->personalizado) && $rr->personalizado != '') : ?>
                          <a href="/adm/vendas/vendas-detalhes.php?id=<?php echo $GET['id'] ?>#personalizado_<?php echo $rr->id ?>" class="btn btn-danger btn-xs">
                            ver personalização
                          </a>
                          <div id="personalizado_<?php echo $rr->id ?>" style="display: none;" class="clearfix _modal_personalizado">
                            <?php
                            $group = '';
                            $json = html_entity_decode($rr->personalizado);
                            $personalizado = json_decode($json, true);
                            foreach ($personalizado as $key => $value) : ?>
                              <div class="fieldset mb5">
                                <div class="row">
                                <div class="col-md-12">
                                  <strong class="text-uppercase"><?php echo $value[0]?></strong>
                                  <p><?php echo $value[1]?></p>
                                </div>
                                  <?php /* foreach ($value as $key1 => $value1) : ?>
                                    <div class="col-md-6">
                                      <h4><?php echo $value1 ?></h4>
                                    </div>
                                  <?php endforeach; */ ?>
                                </div>
                              </div>
                            <?php endforeach; ?>
                          </div>
                        <?php
                        endif;
                        ?>
                      </div>
                    </td>
                    <td nowrap="nowrap" width="1%" align="center">
                      <?php echo $rr->quantidade; ?>
                    </td>
                    <td nowrap="nowrap" width="1%" align="center">
                      <font color="#a20000" class="bold ft18px">R$:
                        <?php echo number_format($rr->valor_pago * $rr->quantidade, 2, ',', '.') ?></font>
                    </td>
                    </tr>
                  <?php
                  $QTDE += $rr->quantidade;
                  // $VALOR_PRODUTOS += $rr->valor_pago * $rr->quantidade;
                  $VALOR_PRODUTOS  = $Pedido->valor_compra;

                  // Soma as quantidade
                  if (in_array($rr->id_produto, $kit_hidden) > 0)
                    $QTDE = $rr->quantidade;

                  // // Soma os valores dos produtos
                  // if(in_array($rr->id_produto, $kit_hidden) > 0)
                  // $VALOR_PRODUTOS	= $Pedido->valor_compra;

                  // Cubagem para o frete
                  if (in_array($rr->id_produto, $kit_hidden) > 0) :

                    if ($cubagem['altura'] < ($altura = $rr->produto->freteproduto->altura))
                      $cubagem['altura'] += ($altura = $rr->produto->freteproduto->altura);

                    if ($cubagem['largura'] < ($largura = $rr->produto->freteproduto->largura))
                      $cubagem['largura'] = ($largura = $rr->produto->freteproduto->largura);

                    if ($cubagem['comprimento'] < ($comprimento = $rr->produto->freteproduto->comprimento))
                      $cubagem['comprimento'] = ($comprimento = $rr->produto->freteproduto->comprimento);

                    // $cubagem['altura'] += $rr->produto->freteproduto->altura;
                    // $cubagem['largura'] += $rr->produto->freteproduto->largura;
                    // $cubagem['comprimento'] += $rr->produto->freteproduto->comprimento;
                    $cubagem['peso'] += ($rr->produto->freteproduto->peso * $rr->quantidade);
                    $cubagem['quantidade'] += $rr->quantidade;
                  else :
                    if ($cubagem['altura'] < ($altura = $rr->produto->freteproduto->altura))
                      $cubagem['altura'] += ($altura = $rr->produto->freteproduto->altura);

                    if ($cubagem['largura'] < ($largura = $rr->produto->freteproduto->largura))
                      $cubagem['largura'] = ($largura = $rr->produto->freteproduto->largura);

                    if ($cubagem['comprimento'] < ($comprimento = $rr->produto->freteproduto->comprimento))
                      $cubagem['comprimento'] = ($comprimento = $rr->produto->freteproduto->comprimento);

                    $cubagem['peso'] += ($rr->produto->freteproduto->peso * $rr->quantidade);
                    $cubagem['quantidade'] += $rr->quantidade;
                  endif;

                  $i++;
                endforeach;

                // printf('<pre>%s</pre>', print_r($cubagem, true));

                  ?>
                  <!--[/end for]-->
              </table>
            </div>
          </div>
        </div>
        <!--[/END DADOS DE COMPRAS]-->

        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
          <div class="panel panel-default">
            <div class="panel-heading panel-store text-uppercase">TOTAL GERAL</div>
            <div class="panel-body">
              <div class="row">
                <!-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<?php echo !empty($Pedido->frete_prazo) ? $Pedido->frete_prazo : ''; ?>
								</div> -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                  <span class="show">Frete: <?php echo $Pedido->frete_tipo ?></span>
                  <span class="show">SubTotal: <font color="#a20000">R$:
                      <?php echo number_format($VALOR_PRODUTOS, 2, ',', '.') ?></font></span>
                  <span class="show">Total de Itens: <font color='#a20000'><?php echo $QTDE ?></font></span>
                  <?php if ($Pedido->id_cupom > 0) { ?>
                    <span class='show'>Cupom: <strong><?php echo $Pedido->pedido_cupom->cupom_codigo ?></strong></span>
                    <span class='show'>Desconto R$:
                      -<?php echo number_format($Pedido->desconto_cupom, 2, ',', '.') ?></span>
                  <?php } ?>
                  <?php echo $Pedido->desconto_boleto > 0 ? "<span class='show'>Desconto no boleto -{$Pedido->desconto_boleto}%</span>" : ''; ?>
                  <span class="show">Valor Frete: <font color='#a20000'>R$:
                      <?php echo number_format($Pedido->frete_valor, 2, ',', '.') ?></font></span>
                  <span class="show">
                    <strong>Total da compra</strong>: <font color='#a20000' class="bold ft24px">R$:
                      <?php echo number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.') ?></font>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php
        if (!empty($Pedido->obs)) {
          $obs = json_decode($Pedido->obs);
        ?>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-warning">
              <div class="panel-heading bold text-uppercase">OBSERVAÇÕES DO PEDIDO</div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <strong><?php echo $STORE['config']['pedido']['obs']['text'] ?></strong><br />
                    <?php echo $obs->obs->text; ?>
                    <hr class="mt5 mb5" />
                    <strong><?php echo $STORE['config']['pedido']['date']['text'] ?></strong><br />
                    <?php echo $obs->obs->date; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="panel panel-warning">
            <div class="panel-heading bold text-uppercase">PRAZO ESTIMADO PARA ENTREGA</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <?php echo !empty($Pedido->frete_prazo) ? $Pedido->frete_prazo : ''; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--[BUTTONS E AÇÕES]-->
    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center col-no-gutters" style="position: relative;">
      <!--
			<div class="panel panel-default">
				<div class="panel-heading panel-store text-uppercase">açoes</div>
				<div class="panel-body">
				-->
      <div id="flutue">
        <?php if ($Pedido->forma_pagamento == 'Cartão') { ?>
          <a href="/adm/vendas/vendas-cielo.php?pedido_id=<?php echo $Pedido->id ?>" class="btn btn-info btn-lg btn-block mb5 btn-acao-status" data-acao="cielo">
            checkout cielo
          </a>
        <?php } ?>

        <button class="btn btn-primary btn-lg btn-block btn-acao-status" data-acao='status-avancar' data-status='<?php echo (int)$Pedido->status; ?>'>
          avançar status
        </button>

        <?php if (($Pedido->forma_pagamento == 'Pagar Me' && !empty($CONFIG['pagarme_api_key'])) &&
          (!empty($CONFIG['clearsale']['conf']) && $CONFIG['clearsale']['conf'] == true) &&
          $Pedido->status == 11
        ) { ?>
          <a href="/adm/vendas/vendas-detalhes.php?id=<?php echo $Pedido->id ?>&acao=pagarme_capture" class="btn btn-info btn-lg btn-block mb5">
            capturar
          </a>
        <?php } ?>

        <?php if (in_array($Pedido->status, array('3', '6', '7', '9'))) { ?>
          <a href="/adm/nfe/nfe.php?id_pedido=<?php echo $GET['id']; ?>" target="_blank" class="btn btn-info btn-lg btn-block mt5 ft14px" id="nfe" <?php echo _P('nfe', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
            <?php echo $Pedido->status == 9 ? 'nota de devolução' : 'emitir/cancelar nf-e' ?>
          </a>
        <?php } ?>

        <a class="btn btn-warning btn-sm mt5 w80" href="/adm/vendas/vendas-impressao-declaracao.php?id=<?php echo $GET['id']; ?>" target="_blank" <?php echo _P('vendas-impressao-declaracao', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
          imprimir declaração
        </a>

        <button class="btn btn-success mt5 btn-acao-status w80" data-acao='status-telemarketing' data-status='11'>telemarketing</button>
        <button class="btn btn-default mt5 btn-acao-status w80" data-acao='status-pedidos-logs' data-status='12'>logs
          pedidos</button>

        <a class="btn btn-success btn-sm mt5 w80<?php echo count($Pedido->questionario) == 0 ? ' hidden' : '' ?>" href="/adm/vendas/vendas-impressao-questionario.php?id=<?php echo $GET['id']; ?>" target="_blank" <?php echo _P('vendas-impressao-questionario', $_SESSION['admin']['id_usuario'], 'acessar') ?>>imprimir
          questionário</a>

        <?php if (in_array($Pedido->status, array('3', '6', '7'))) { ?>
          <a class="btn btn-default btn-sm btn-txt mt5 w80" href="javascript: void();" <?php echo _P('vendas-txt', $_SESSION['admin']['id_usuario'], 'acessar') ?>>gerar txt nfe</a>
        <?php } ?>

        <a class="btn btn-default btn-sm mt5 w80" href="javascript: void();" onclick="imprimir_dados('imprimir_dados');">imprimir pedido</a>

        <a class="btn btn-default btn-sm mt5 w80" href="/adm/vendas/vendas.php" style="color: #000">
          voltar / vendas
        </a>

        <a class="btn btn-danger btn-sm mt5" href='/adm/vendas/vendas-detalhes.php?acao=excluir_pedido&id_pedido=<?php echo $GET['id']; ?>' <?php echo _P('vendas-detalhes', $_SESSION['admin']['id_usuario'], 'excluir') ?> onclick="return confirm('Deseja realmente excluir!');">excluir</a>

      </div>
      <!--
				</div>
			</div>
			-->
    </div>
    <!--[/END BUTTONS E AÇÕES]-->
  </div>
</div>

<!--[ BLOCK ETIQUETAS ]
<form method="post" id="janela_etiqueta" style="display: none;overflow-x: hidden">
	<div class="row">
		<div class="col-md-4 form-group">
			<label>QTDE Vol:</label>
			<input name="frete_qtde" class="text-right form-control" value="1">
		</div>
		<div class="col-md-4 form-group">
			<label>Seguro:</label>
			<select name="frete_seguro" class="form-control">
				<option value="">Seguro</option>
				<option value="0" selected>NÃO</option>
				<option value="1">SIM</option>
			</select>
		</div>
		<div class="col-md-12 form-group">
			<label>Serviço de postagem:</label>
			<select name="frete_servico" class="form-control" style="width: 100%;">
				<option value="">Serviço de postagem</option>
			</select>
		</div>
		<div class="col-md-12 text-center form-group">
			<hr/>
			<button type="submit" class="btn btn-primary btn-large">
				gerar etiqueta
			</button>
		</div>
	</div>
</form>
[ END BLOCK ETIQUETAS ]-->

<div id='telemarketing' style="display: none; height: 100%">
  <div class="clearfix" style="height: 100%">
    <div class="col-md-8" style="height: 100%;">
      <textarea name="descricao" class="w100" style="height: 400px"></textarea>
    </div>
    <div class="col-md-4" id="telemarketing-reload" style='height: 100%; overflow-y: auto;'>
      <?php
      $adm_id   = $_SESSION['admin']['id_usuario'];
      $descricao   = trim($POST['descricao']);
      $pedido_id  = $Pedido->id;

      if (isset($POST['descricao'], $POST['acao']) && $POST['acao'] === 'TELEMARKETING' && $POST['descricao'] != '') {
        if (PedidosMarketing::action_cadastrar_editar(['PedidosMarketing' => [0 => [
          'id_adm' => $adm_id,
          'id_pedido' => $pedido_id,
          'descricao' => $descricao
        ]]], 'cadastrar', 'id')) {
        }
      }

      $result = PedidosMarketing::find_by_sql(''
        . 'select '
        . 'adm.usuario, '
        . 'mark.data, '
        . 'mark.descricao, '
        . 'mark.id_adm '
        . 'from pedidos_marketing mark '
        . 'left join adm adm on adm.id = mark.id_adm '
        . 'where mark.id_adm=? and mark.id_pedido=? '
        . 'order by mark.id desc', [$adm_id, $pedido_id]);
      $group = '';
      foreach ($result as $rMarketing) { ?>
        <?php if ($group != $rMarketing->id_adm) {
          $group = $rMarketing->id_adm; ?>
          <div class="titulo-telemarketing plano-fundo-adm-001 cor-branco"><b><?php echo $rMarketing->usuario; ?></b></div>
          <ul class="lista-telemarketing mt5 mb5">
          <?php } ?>
          <li class="mb5">
            <span class='show text-right'><?php echo $rMarketing->data->format('d/m/Y H:i'); ?></span>
            <?php echo nl2br($rMarketing->descricao); ?>
          </li>
          <?php if ($group != $rMarketing->id_adm) { ?>
          </ul>
        <?php } ?>
      <?php } ?>
    </div>
  </div>
</div>

<div id="recarregar-pedidos-logs" style="display: none;">
  <table cellpadding='8' cellspacing='0' width='100%'>
    <tbody>
      <tr class='plano-fundo-adm-001 cor-branco'>
        <td>
          Pedido: <span class='ft16px'><?php echo $Pedido->codigo ?></span><br />
          Data Venda: <span class='ft16px'><?php echo $Pedido->data_venda->format('d/m/Y') ?></span>
        </td>
        <td align='center'>Usuário</td>
        <td align='center'>Atualizado</td>
      </tr>
      <?php foreach ($Pedido->pedidos_logs as $rLog) { ?>
        <tr class='lista-logs'>
          <td><?php echo nl2br($rLog->descricao) ?></td>
          <td align='center' nowrap="nowrap" width="1%"><?php echo $rLog->adm->apelido ?></td>
          <td align='center' nowrap="nowrap" width="1%"><?php echo $rLog->data_envio->format('d/m/Y H:i') ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>

  <!-- <div id="janela-cancelar-nfe" style="display: none">
        <form id="formulario-cancelar-nfe" class="clearfix" method="post" action="">
            <div class="clearfix">
                <div id="div-cancelar">
                    <p>Digite um motivo <span style='font-size: 9px'>(opcional)</span></p>
                    <textarea name="motivo" rows="4" class="w95"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">salvar</button>
            <input type="hidden" name="acao" value="cancelar-nfe" id="acao-cancelar"/>
            <input type="reset" id="reset" style='display:none;'/>
        </form>
    </div>-->
</div>

<?php ob_start(); ?>
<script>
  <?php require dirname(__DIR__) . '/vendas/js/vendas-detalhes.js'; ?>
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();


include dirname(__DIR__) . '/rodape.php';
