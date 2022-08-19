<style>
  .input-falsos.has-error {
    background-color: #fff6f6;
    border-color: #ff7c7c;
    color: #ff7c7c;
  }

  .input-falsos.has-error+p {
    color: #ff7c7c;
  }

  .input-error-span-2 {
    margin: 3px 0 0 0 !important;
    font-size: 12px;
    font-family: arial;
    min-width: 128px;
  }
</style>

<div class="clearfix new-checkout mt50 mb50 row" id="new-checkout-reload">

  <div class="<?php echo ($GET['AcaoCliente'] != '' ||
                $GET['AcaoEnderecos'] != '' ||
                $_SESSION['cliente']['id_cliente'] == '')
                ? 'col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 '
                : 'col-lg-4 col-md-4 col-sm-6 ' ?>col-xs-12 mb15">
    <!--[ CADASTRO DE PESSOAS ]-->
    <?php
    include dirname(__DIR__) . '/_identificacao/cadastro-dados/cadastro-dados.php';
    ?>
    <!--[ END CADASTRO DE PESSOAS ]-->

    <!--[ CADASTRO ENDERECO/SELECAO DO FRETE/SELECAO DA FORMA DE PAGAMENTO ]-->
    <?php
    if ($STORE['config']['endereco']['configure']['status'] == true) {
      include dirname(__DIR__) . '/_identificacao/cadastro-dados/cadastro-endereco.php';
    } else {
      $ClientesEnderecos = 0; ?>
      <script>
        $(function() {
          console.log("Sem Frete");
          $("#finalizar-pedido").removeClass("hidden").fadeIn(0);
          $("[dataid=gratis]").delay(100).queue(function(e) {
            $(this).trigger("click");
            e();
          });
        });
      </script>
      <span class="hide">
        <span>
          <input type="hidden" name="frete" dataid="gratis" id="GRÁTIS" value="0" data-valor="0.00" data-gratis="" onclick="Checkout.atualizar_carrinho( this );" />
        </span>
        <span>
          <span class="show ft18px color-004">FRETE GRÁTIS</span>
          <span class="show ft16px"></span>
        </span>
      </span>
    <?php } ?>
    <!--[ END CADASTRO ENDERECO/SELECAO DO FRETE/SELECAO DA FORMA DE PAGAMENTO ]-->
  </div>

  <!--[ DADOS DO CARRINHO ]-->
  <div class="col-md-4 col-sm-12 col-xs-12 mb15<?php echo empty($_SESSION['cliente']['id_cliente']) || ($GET['AcaoCliente'] != '' || $GET['AcaoEnderecos'] != '') ? ' hidden' : '' ?>" id="carregar-compra">
    <div class="new-caixa-checkout">
      <div class="clearfix model-border-bottom-thin mb15">
        <span class="badge pull-left">
          <?php echo empty($ClientesEnderecos) ? 3 : 3 ?>
        </span>
        <span class="title pull-left">Resumo da Compra</span>
        <small class="pull-left mb5" style="width: 100%;"></small>
      </div>
      <div>
        <a href="/identificacao/carrinho" class="btn btn-secundary btn-xs ft10px">Voltar ao Carrinho</a>
        <ul class="mt10">
          <?php
          $i                      = 1;
          $TOTAL_ITENS       = 0;
          $TIPO_FRETE       = 0;
          $TOTAL_FRETE       = 0;
          $TOTAL_DESCONTO      = 0;
          $TOTAL_CARRINHO      = 0;
          $TOTAL_CARRINHO_DESC    = 0;
          $TOTAL_ESTOQUE          = 0;
          $obs_text = null;
          $obs_date = null;
          $CarrinhoCompras = $CONFIG['carrinho_all'];
          $CarrinhoComprasCount = (int)count($CarrinhoCompras);
          foreach ($CarrinhoCompras as $r) : ?>
            <li style="border-bottom: 1px solid #eee;<?php echo $r->estoque < $r->quantidade ? 'background-color:#fff2f2"' : '' ?>">
              <table class="table">
                <tr>
                  <td nowrap="nowrap" width="55px">
                    <img src="<?php echo Imgs::src($r->imagem, 'smalls'); ?>" width="55" />
                  </td>
                  <td>
                    <a class="show ft16px" href="/<?php echo converter_texto($r->nome_produto) ?>/<?php echo $r->id_produto ?>/p"><?php echo $r->nome_produto; ?></a>
                    <?php
                    echo sprintf('<span class="show ft12px">Cód: %s</span>', CodProduto($r->nome_produto, $r->id_produto, $r->codigo_produto));
                    echo ($r->nomecor) ? sprintf('<span class="show ft12px">%s: %s</span>', $r->tipocores, $r->nomecor) : '';
                    echo ($r->nometamanho) ? sprintf('<span class="show ft12px">%s: %s</span>', $r->tipotamanhos, $r->nometamanho) : '';
                    echo $r->estoque < $r->quantidade ? '<span class="show ft10px red" data-estoque="zero">Quantidade indisponível! (<a href="/identificacao/carrinho" class="span click-me" id="' . $r->id . '" value="" style="cursor: pointer;">clique aqui para continuar comprando</a>)</span>' : '';
                    ?>
                  </td>
                </tr>
                <tr>
                  <td>
                    <strong class="ft12px">Qtde: <?php echo $r->quantidade; ?></strong>
                  </td>
                  <td class="text-right">
                    <strong class="ft14px">R$: <?php echo number_format(desconto_boleto($r->preco_promo, $CONFIG['desconto_boleto']) * $r->quantidade, 2, ',', '.'); ?></strong>
                  </td>
                </tr>
              </table>
            </li>
          <?php
            if ($r->estoque <= 0) :
              $TOTAL_ESTOQUE = 1;
            endif;

            $TOTAL_CARRINHO      += ($r->preco_promo * $r->quantidade);
            $TOTAL_CARRINHO_DESC += desconto_boleto($r->preco_promo, $CONFIG['desconto_boleto']) * $r->quantidade;
            $TOTAL_FRETE_SOMA    = $r->frete_valor;

            $ID_CUPOM        = $r->id_cupom;
            $CUPOM           = $r->cupom_codigo;
            $CUPOM_TIPO      = $r->cupom_desconto;
            $CUPOM_VALOR     = $r->cupom_value;
            $PERSONALIZADO   = json_decode($r->personalizado, true);

            if (!empty($r->cliente_obs)) {
              $cliente_obs = json_decode($r->cliente_obs, true);
              $obs_text    = $cliente_obs['obs']['text'];
              $obs_date    = $cliente_obs['obs']['date'];
            }

            ++$i;
          endforeach;

          $TOTAL_CARRINHO_FRETE  = number_format($TOTAL_CARRINHO + $TOTAL_FRETE_SOMA, 2, ',', '.');

          $TOTAL = valor_pagamento($TOTAL_CARRINHO, $TOTAL_FRETE_SOMA, $CUPOM_VALOR, $CUPOM_TIPO, $CONFIG['desconto_boleto']);

          // $sqlBloquearCupom = 'SELECT '
          //   . 'pedidos.id_cupom '
          //   . 'FROM pedidos '
          //   . 'JOIN cupons ON cupons.id = pedidos.id_cupom '
          //   . 'WHERE pedidos.ip = "%s" and cupons.cupom_codigo = "%s" and (cupons.cupom_cliente_id > 0 or (md5(pedidos.id_cliente) = "%s"))';

          // $sqlBloquearCupom = sprintf($sqlBloquearCupom, retornaIpReal(), $CUPOM, $_SESSION['cliente']['id_cliente']);

          // $countIdCupom = Pedidos::query($sqlBloquearCupom)->rowCount();

          // if ($countIdCupom > 0) {
          //   printf('<script>alert("Atenção!\nCupom inválido. Tente outro cupom que seja válido.");window.location.href="/identificacao/carrinho"</script>');
          //   return;
          // }

          if ($TOTAL['TOTAL_COMPRA_C_BOLETO'] == '0.00') {
            header(sprintf('location: %s', URL_BASE));
            return;
          }
          ?>

          <li style="background-color:#f3f3f3;">
            <table width="100%" class="table">
              <!-- <tr>
								<td class="text-right black-50">Subtotal&nbsp;&nbsp;</td>
								<td class="text-left color-004 bold ft18px">R$: <?php echo number_format($TOTAL_CARRINHO_DESC, 2, ',', '.'); ?></td>
							</tr> -->
              <tr>
                <td class="text-right black-50">Cupom de Desconto&nbsp;&nbsp;</td>
                <td class="text-left color-004 mr5 bold ft18px" total_desconto>R$: 0,00</td>
              </tr>
              <tr>
                <td class="text-right black-50">Valor frete&nbsp;&nbsp;</td>
                <td class="text-left color-004 bold ft18px" total_frete>R$: <?php echo number_format($TOTAL_FRETE_SOMA, 2, ',', '.'); ?></td>
              </tr>
              <tr>
                <td class="text-right black-50">Total&nbsp;&nbsp;</td>
                <td class="text-left color-004 bold font-bold ft24px" id="total_carrinho_frete" total_carrinho_frete="" data-boleto-transferencia="" data-compra="" data-atacadista="<?php echo (!empty($CONFIG['atacadista']) && ($CONFIG['atacadista_min'] >= $TOTAL['TOTAL_COMPRA_C_BOLETO'] || $CONFIG['atacadista_max'] <= $TOTAL['TOTAL_COMPRA_C_BOLETO']) ? 1 : 0) ?>" data-min="<?php echo (($CONFIG['atacadista_min'] >= $TOTAL['TOTAL_COMPRA_C_BOLETO']) ? 1 : 0) ?>" data-max="<?php echo (($CONFIG['atacadista_max'] <= $TOTAL['TOTAL_COMPRA_C_BOLETO']) ? 1 : 0) ?>">
                  R$: <?php echo number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.'); ?>
                </td>
              </tr>
            </table>
            <span class="hidden" quantidade_parcela=""></span>
          </li>
        </ul>
      </div>
    </div>

    <?php if (!empty($STORE['config']['pedido']['obs'][0])) { ?>
      <?php
      if (isset($POST['obs'])) {
        Carrinho::update_all([
          'set' => ['cliente_obs' => json_encode($POST)],
          'conditions' => ['id_session=?', session_id()]
        ]);
      }
      ?>
      <div class="new-caixa-checkout mt15" id="caixa_observacao">
        <div class="clearfix model-border-bottom-thin mb15">
          <span class="title pull-left">Observação da Compra</span>
          <small class="pull-left mb5" style="width: 100%;"></small>
        </div>
        <div class="row">
          <?php if (!empty($STORE['config']['pedido']['obs']['text'])) { ?>
            <div class="form-group col-md-12 col-xs-12">
              <label><?php echo $STORE['config']['pedido']['obs']['text']; ?>:</label>
              <textarea name="obs[text]" class="form-control" rows="7" autocomplete="off"><?php echo $obs_text ?></textarea>
            </div>
          <?php } ?>

          <?php if (!empty($STORE['config']['pedido']['date']['text'])) { ?>
            <div class="form-group col-md-8 col-xs-12">
              <label><?php echo $STORE['config']['pedido']['date']['text']; ?>:</label>
              <input type="text" name="obs[date]" value="<?php echo $obs_date ?>" class="form-control datepicker" autocomplete="off" />
            </div>
          <?php } ?>
        </div>
      </div>
      <?php ob_start(); ?>
      <link href="/public/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css" />
      <script src="/public/jquery-ui/jquery-ui.min.js"></script>
      <?php $BIBLIOTECAS .= ob_get_clean() ?>
      <?php ob_start(); ?>
      <script>
        var options_date = {
          dateFormat: "dd/mm/yy",
          dayNames: ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado", "Domingo"],
          dayNamesMin: ["D", "S", "T", "Q", "Q", "S", "S", "D"],
          dayNamesShort: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb", "Dom"],
          monthNames: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
          monthNamesShort: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"]
        };

        // Tenta mostrar uma data para entrega personalizada
        $("#caixa_observacao").on("click", ".datepicker", function() {
          var $this = $(this);
          if (!$this.hasClass("hasDatepicker")) $this.datepicker(options_date);
          $this.datepicker("show");
        });

        // Tenta salvar em modo de 2 plano
        $("#caixa_observacao").on("change", ".form-control", function(e) {

          var data_str = {
            obs: {
              text: $("[name='obs[text]']").val(),
              date: $("[name='obs[date]']").val()
            }
          };

          $.ajax({
            url: "/identificacao/checkout-new",
            type: "POST",
            data: data_str,
            global: false,
            success: function() {},
            complete: function() {},
            beforeSend: function() {},
            error: function() {},
          });
        });
      </script>
      <?php $str['script_manual'] .= ob_get_clean(); ?>
    <?php } ?>
  </div>
  <!--[ DADOS DO CARRINHO ]-->

  <form class="mb15 col-md-4 col-sm-6 col-xs-12<?php echo empty($_SESSION['cliente']['id_cliente']) || $GET['AcaoCliente'] != '' || $GET['AcaoEnderecos'] != '' || count($ClientesEnderecos) == 0 ? ' hidden' : '' ?>" id="form-minha-compra" method="post" action="/identificacao/checkout-new">
    <div class="new-caixa-checkout clearfix">
      <div class="clearfix model-border-bottom-thin mb15">
        <span class="badge pull-left">
          <?php echo empty($ClientesEnderecos) ? 3 : 4 ?>
        </span>
        <span class="title pull-left">Formas de Pagamento</span>
        <small class="pull-left mb5" style="width: 100%;">Selecione um forma de pagamento para finalizar seu pedido</small>
      </div>

      <style>
        .model-border-bottom-thin {
          border-bottom-width: 1px;
          border-bottom-style: solid;
          border-bottom-color: #999;
        }
      </style>
      <ul>
        <?php if ($CONFIG['pagamentos']['pagarme'] == '1') : ?>
          <!--[PAGAR ME]-->
          <?php
          include dirname(__DIR__) . '/_identificacao/formas-pagamentos/pgto-pagarme.php';
          ?>
          <li class="model-border-bottom-thin mb15 mt15"></li>
          <!--[\PAGAR ME]-->
        <?php endif; ?>

        <?php if ($CONFIG['pagamentos']['mp'] == '1') : ?>
          <!--[MERCADO PAGO]-->
          <?php
          include dirname(__DIR__) . '/_identificacao/formas-pagamentos/pgto-mp.php';
          ?>
          <li class="model-border-bottom-thin mb15 mt15"></li>
          <!--[\MERCADO PAGO]-->
        <?php endif; ?>

        <?php if ($CONFIG['pagamentos']['cielo_mid'] != '' && $CONFIG['pagamentos']['cielo'] == '1') : ?>
          <!--[AMBIENTE CIELO]-->
          <?php
          include dirname(__DIR__) . '/_identificacao/formas-pagamentos/pgto-cielo-cartao-cielo.php';
          ?>
          <!--[END AMBIENTE CIELO]-->
          <li class="model-border-bottom-thin mb15 mt15"></li>
        <?php endif; ?>

        <?php if ($CONFIG['pagamentos']['pagseguro'] == '1') : ?>
          <!--[AMBIENTE PAGSEGURO]-->
          <?php
          include dirname(__DIR__) . '/_identificacao/formas-pagamentos/pgto-pagseguro.php';
          ?>
          <!--[ENDAMBIENTE PAGSEGURO]-->
          <li class="model-border-bottom-thin mb15 mt15"></li>
        <?php endif; ?>

        <?php if ($CONFIG['pagamentos']['cielo_merchantkey'] != '' && $CONFIG['pagamentos']['cielo'] == '1') : ?>
          <!--[CIELO CARTÃO]-->
          <?php
          include dirname(__DIR__) . '/_identificacao/formas-pagamentos/pgto-cielo-cartao-loja.php';
          ?>
          <!--[END CIELO CARTÃO]-->
          <li class="model-border-bottom-thin mb15 mt15"></li>
        <?php endif; ?>

        <?php if ($CONFIG['pagamentos']['pix'] != '' && $CONFIG['pagamentos']['pix'] == '1') : ?>
          <!--[PIX]-->
          <?php
          include dirname(__DIR__) . '/_identificacao/formas-pagamentos/pgto-pix.php';
          ?>
          <!--[END PIX]-->
          <li class="model-border-bottom-thin mb15 mt15"></li>
        <?php endif; ?>

        <?php if ($CONFIG['pagamentos']['boleto'] == '1' || $CONFIG['pagamentos']['mp_boleto'] == '1') : ?>
          <!--[BOLETO]-->
          <?php
          include dirname(__DIR__) . '/_identificacao/formas-pagamentos/pgto-boleto.php';
          ?>
          <!--[\BOLETO]-->
          <li class="model-border-bottom-thin mb15 mt15"></li>
        <?php endif; ?>

        <?php if ($CONFIG['pagamentos']['transferencia'] == '1') : ?>
          <!--[TRANSFERENCIA]-->
          <?php
          include dirname(__DIR__) . '/_identificacao/formas-pagamentos/pgto-transferencia.php';
          ?>
          <!--[\TRANSFERENCIA]-->
          <li class="model-border-bottom-thin mb15 mt15"></li>
        <?php endif; ?>

        <li id="card-wrapper" class="card-wrapper mb15" style="display: none;"></li>

        <li id="card-form" class="mb15" style="display: none;">
          <?php
          include dirname(__DIR__) . '/_identificacao/formas-pagamentos/pgto-form.php';
          ?>
        </li>

        <li>
          <button type="submit" data-type="pedido" class="btn btn-primary btn-lg btn-block" id="finalizar-pedido" tabindex="99">
            finalizar pedido
          </button>
        </li>
      </ul>
    </div>
    <input type="hidden" name="pagamento[acao]" value="FinalizarPagamento" />
    <input type="hidden" name="pagamento[Amount]" value="" id="pagamentoAmount" />
    <input type="hidden" name="pagamento[Frete]" value="" data-frete />
    <input type="hidden" name="pagamento[SessionId]" value="" id="PagamentoSessionIdClearSale" />
  </form>
</div>

<?php
// PagSeguro
$PagSeguroSessionId = null;
try {
  \PagSeguro\Library::initialize();
  \PagSeguro\Library::cmsVersion()->setName($CONFIG['nome_fantasia'])->setRelease("0.0.0");
  \PagSeguro\Library::moduleVersion()->setName($CONFIG['nome_fantasia'])->setRelease("1.0.0");

  \PagSeguro\Configuration\Configure::setEnvironment(empty($CONFIG['pagamentos']['pagseguro_mode']) ? 'sandbox' : 'production');
  \PagSeguro\Configuration\Configure::setAccountCredentials($CONFIG['pagamentos']['pagseguro_email'], $CONFIG['pagamentos']['pagseguro_token']);
  \PagSeguro\Configuration\Configure::setCharset('UTF-8');

  $Configure = \PagSeguro\Configuration\Configure::getAccountCredentials();
  $Session = \PagSeguro\Services\Session::create($Configure);
  $PagSeguroSessionId = $Session->getResult();
} catch (Exception $e) {
  // die($e->getMessage());
}
?>

<?php ob_start(); ?>
<!-- <script src="https://assets.pagar.me/pagarme-js/3.0/pagarme.min.js"></script> -->
<script src="https://stc.<?php echo $CONFIG['pagamentos']['pagseguro_mode'] == 'sandbox' ? 'sandbox.' : null ?>pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>
<script src="https://assets.pagar.me/pagarme-js/4.5/pagarme.min.js"></script>
<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
<?php $BIBLIOTECAS .= ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
  <?php include dirname(__DIR__) . '/_identificacao/js/checkout-new-js.js' ?>
</script>
<?php $str['script_manual'] .= ob_get_clean(); ?>
