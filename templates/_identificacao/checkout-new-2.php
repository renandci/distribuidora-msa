<div class="clearfix new-checkout mt50 mb50 row" id="new-checkout-reload" style="position: relative;">

  <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 mb15 pull-left">
    <!--[ ENDEREÇOS ]-->
    <div class="new-caixa-checkout">
      <div class="clearfix model-border-bottom-thin mb15">
        <span class="badge active pull-left">1</span>
        <span class="title pull-left">Escolha um endereço para entrega</span>
        <a class="pull-right btn btn-secundary btn-xs ml5 ft12px endereco-car-edit" href="/identificacao/identificacao-endereco-cadastrar_editar/?_u=<?php echo URL_BASE; ?>identificacao/checkout-new">Cadastrar um novo</a>
      </div>
      <div class="row" id="new-cadastro-enderecos">
        <?php
        if (!empty($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != '') {
          $cep = null;
          foreach ($CONFIG['cliente_session']['enderecos'] as $end) {
            if ($end['status'] == 'ativo') {
              $cep = $end['cep'];
            }
        ?>
            <div class="col-md-6 mb5">
              <div class="clearfix <?php echo $end['status'] == 'ativo' ? ' endereco-checked' : '' ?>" style="padding: 7px; min-height: 175px">
                <span class="pull-left">
                  <input type="radio" name="end[]" id="end_<?php echo $end['id'] ?>" class="input-checkbox" value="<?php echo $end['id'] ?>" <?php echo $end['status'] == 'ativo' ? ' checked' : '' ?> />
                  <label class="fa ft22px" for="end_<?php echo $end['id'] ?>" data-href="<?php echo Url::getBase() ?>identificacao/checkout-new/?AcaoEnderecos=SelecionaEndereco&endereco_id=<?php echo $end['id'] ?>" data-select="endereco"></label>
                </span>
                <span class="pull-left">
                  <?php if (!empty($end['nome'])) { ?>
                    <p class="show ft18px mb5"><?php echo $end['nome'] ?></p>
                  <?php } ?>
                  <?php if (!empty($end['receber'])) { ?>
                    <span class="show">Receber: <?php echo $end['receber'] ?></span>
                  <?php } ?>
                  <?php if (!empty($end['endereco'])) { ?>
                    <span class="show"><?php echo $end['endereco'] ?> - <?php echo $end['numero'] ?>, <?php echo $end['bairro'] ?></span>
                  <?php } ?>
                  <?php if (!empty($end['complemento'])) { ?>
                    <span class="show"><?php echo $end['complemento'] ?></span>
                  <?php } ?>
                  <?php if (!empty($end['referencia'])) { ?>
                    <span class="show"><?php echo $end['referencia'] ?></span>
                  <?php } ?>
                  <?php if (!empty($end['cidade'])) { ?>
                    <span class="show"><?php echo $end['cidade'] ?>, <?php echo $end['uf'] ?></span>
                  <?php } ?>
                  <?php if (!empty($end['cep'])) { ?>
                    <span class="show"><?php echo $end['cep'] ?></span>
                  <?php } ?>
                </span>
                <a href="/identificacao/identificacao-endereco-cadastrar_editar/?id=<?php echo $end['id']; ?>&_u=<?php echo URL_BASE; ?>identificacao/checkout-new" class="endereco-car-edit btn btn-xs btn-primary pull-right">
                  Editar
                </a>
              </div>
            </div>
          <?php } ?>
        <?php } ?>

        <!--[ TIPO DE ENVIOS ]-->
        <div class="col-md-12 mt15" id="reload_frete"><?php echo ((!empty($GET['track']) && $GET['track'] == 'reload_frete') ? AtualizarFrete(session_id(), $_SESSION, $cep) : 'Carregando fretes...') ?></div>

        <!--[ END TIPO DE ENVIOS ]-->
        <div class="col-md-12" id="AtualizarPudosJadLog"></div>
      </div>

      <?php ob_start(); ?>
      <script>
        var MediaQueries = window.matchMedia("(min-width: 768px)"),
          widthSidebar = $("#carregar-compra").find(".new-caixa-checkout").width() + 15;
        $(window).scroll(function() {
          var length = $("#new-checkout-reload").height() - $("#carregar-compra").height() + $("#new-checkout-reload").offset().top,
            scroll = $(this).scrollTop(),
            height = $("#carregar-compra").height() + "px",
            heightLeft = $("#new-checkout-reload").height() + "px";

          if (MediaQueries.matches) {

            if (scroll <= $("#new-checkout-reload").offset().top) {
              $("#carregar-compra").find(".new-caixa-checkout").css({
                "position": "absolute",
                "top": "0",
                "width": widthSidebar
              }).parent().css({
                "height": heightLeft
              });
            } else if (scroll >= length) {
              $("#carregar-compra").find(".new-caixa-checkout").css({
                "position": "absolute",
                "top": "auto",
                "bottom": "0",
                "width": widthSidebar
              }).parent().css({
                "height": heightLeft
              });
            } else {
              $("#carregar-compra").find(".new-caixa-checkout").css({
                "position": "fixed",
                "top": "0",
                "bottom": "0",
                "width": widthSidebar,
                "height": height
              }).parent().css({
                "height": heightLeft
              });
            }
          }
        });

        // Carrega o frete para o input
        $("#modal-site").find("input[input-mask=cep]").mask("00000-000", {
          onComplete: busca_cidade
        });

        // Click para ediçoes e cadastros
        $("#new-checkout-reload").on("click", ".endereco-car-edit", function(e) {

          e.preventDefault();
          var ModalSite = $("#modal-site"),
            Href = e.target.href || this.href;
          $.ajax({
            url: Href,
            dataType: "html",
            beforeSend: function() {
              $("#aminacao-site").fadeIn(0);
              ModalSite.modal("show");
            },
            complete: function() {
              $("#aminacao-site").fadeOut(0);
            },
            success: function(str) {
              var list = $("<div/>", {
                html: str
              });
              ModalSite.find(".modal-header").fadeOut(0);
              ModalSite.find(".modal-body").html([
                $("<button/>", {
                  type: "button",
                  class: "close",
                  attr: {
                    "data-dismiss": "modal",
                    "aria-label": "Close"
                  },
                  html: [
                    $("<span/>", {
                      html: [
                        "&times;"
                      ],
                      attr: {
                        "aria-hidden": "true"
                      }
                    })
                  ]
                }),
                list.find("#modal-content").html()
              ]);
            },
            error: function(X, T, M) {
              if (T === 'timeout') {
                alert('Opss algo falhou tente novamente');
              } else {
                alert(T);
              }
            }
          });
        });
      </script>
      <?php
      $str['script_manual'] .= ob_get_clean();
      ?>
      <!--[ END ENDEREÇOS ]-->
    </div>
    <?php
    // include dirname(__DIR__) . '/_identificacao/cadastro-dados/cadastro-dados.php';
    ?>
    <!--[ END CADASTRO DE PESSOAS ]-->

    <!--[ CADASTRO ENDERECO/SELECAO DO FRETE/SELECAO DA FORMA DE PAGAMENTO ]-->
    <?php /*
        if( $STORE['config']['endereco']['configure']['status'] == true ) {
            include dirname(__DIR__) . '/_identificacao/cadastro-dados/cadastro-endereco.php';
        }
        else { $ClientesEnderecos = 0; ?>
			<script>
				$(function(){
					console.log("Sem Frete");
					$("#finalizar-pedido").removeClass("hidden").fadeIn(0);
					$("[dataid=gratis]").delay(100).queue(function(e){
						$(this).trigger("click");
						e();
					});
				});
			</script>
			<span class="hide">
				<span>
					<input type="hidden" name="frete" dataid="gratis" id="GRÁTIS" value="0" data-valor="0.00" data-gratis="" onclick="Checkout.atualizar_carrinho( this );"/>
				</span>
				<span>
					<span class="show ft18px color-004">FRETE GRÁTIS</span>
					<span class="show ft16px"></span>
				</span>
			</span>
        <?php } */ ?>
    <!--[ END CADASTRO ENDERECO/SELECAO DO FRETE/SELECAO DA FORMA DE PAGAMENTO ]-->
  </div>

  <form class="mb15 col-md-8 col-sm-8 col-xs-12 pull-left" id="form-minha-compra" method="post" action="/identificacao/checkout-new">
    <div class="new-caixa-checkout clearfix">
      <div class="clearfix model-border-bottom-thin mb15">
        <span class="badge pull-left active">2</span>
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

  <!--[ DADOS DO CARRINHO ]-->
  <div class="col-md-4 col-sm-12 col-xs-12 pull-right" id="carregar-compra">
    <div class="new-caixa-checkout">
      <div class="clearfix model-border-bottom-thin mb15">
        <span class="badge pull-left<?php echo !empty($GET['Badge']) && $GET['Badge'] == '4' ? ' active' : '' ?>">
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
          $TOTAL_ESTOQUE          = 0;

          // $CarrinhoCompras = Carrinho::cart();
          $CarrinhoCompras = $CONFIG['carrinho_all'];
          foreach ($CarrinhoCompras as $r) : ?>
            <li style="border-bottom: 1px solid #eee;<?php echo $r->estoque < $r->quantidade ? 'background-color:#fff2f2"' : '' ?>">
              <table class="table">
                <tr>
                  <td nowrap="nowrap" width="75px">
                    <img src="<?php echo Imgs::src($r->imagem, 'smalls'); ?>" width="75" />
                  </td>
                  <td>
                    <a class="show ft18px" href="/<?php echo converter_texto($r->nome_produto) ?>/<?php echo $r->id_produto ?>/p">
                      <?php echo $r->nome_produto; ?>
                    </a>
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
                    Qtde: <?php echo $r->quantidade; ?>
                  </td>
                  <td class="rosa bold-3">
                    R$: <?php echo number_format($r->preco_promo, 2, ',', '.'); ?>
                  </td>
                </tr>
              </table>
            </li>
          <?php
            if ($r->estoque <= 0) :
              $TOTAL_ESTOQUE = 1;
            endif;

            $TOTAL_CARRINHO      += ($r->preco_promo * $r->quantidade);
            $TOTAL_FRETE_SOMA    = $r->frete_valor;

            $ID_CUPOM        = $r->id_cupom;
            $CUPOM           = $r->cupom_codigo;
            $CUPOM_TIPO        = $r->cupom_desconto;
            $CUPOM_VALOR      = $r->cupom_value;
            $PERSONALIZADO          = json_decode($r->personalizado, true);
            ++$i;
          endforeach;

          $TOTAL_CARRINHO_FRETE  = number_format($TOTAL_CARRINHO + $TOTAL_FRETE_SOMA, 2, ',', '.');

          $TOTAL = valor_pagamento($TOTAL_CARRINHO, $TOTAL_FRETE_SOMA, $CUPOM_VALOR, $CUPOM_TIPO, $CONFIG['desconto_boleto']);

          if ($TOTAL['TOTAL_COMPRA_C_BOLETO'] == '0.00') {
            header(sprintf('location: %s', URL_BASE));
            return;
          }
          ?>

          <li style="background-color:#f3f3f3;">
            <table width="100%" class="table">
              <tr>
                <td align="right" class="black-50">Subtotal&nbsp;&nbsp;</td>
                <td align="left" class="color-004 bold ft18px">R$: <?php echo number_format($TOTAL_CARRINHO, 2, ',', '.'); ?></td>
              </tr>
              <tr>
                <td align="right" class="black-50">Cupom de Desconto&nbsp;&nbsp;</td>
                <td align="left" class="color-004 mr5 bold ft18px" total_desconto>R$: 0,00</td>
              </tr>
              <tr>
                <td align="right" class="black-50">Valor frete&nbsp;&nbsp;</td>
                <td align="left" class="color-004 bold ft18px" total_frete>R$: <?php echo number_format($TOTAL_FRETE_SOMA, 2, ',', '.'); ?></td>
              </tr>
              <tr>
                <td align="right" class="black-50">Total&nbsp;&nbsp;</td>
                <td align="left" class="color-004 bold ft24px" id="total_carrinho_frete" total_carrinho_frete="" data-boleto-transferencia="" data-compra="" data-atacadista="<?php echo (!empty($CONFIG['atacadista']) && ($CONFIG['atacadista_min'] >= $TOTAL['TOTAL_COMPRA_C_BOLETO'] || $CONFIG['atacadista_max'] <= $TOTAL['TOTAL_COMPRA_C_BOLETO']) ? 1 : 0) ?>" data-min="<?php echo (($CONFIG['atacadista_min'] >= $TOTAL['TOTAL_COMPRA_C_BOLETO']) ? 1 : 0) ?>" data-max="<?php echo (($CONFIG['atacadista_max'] <= $TOTAL['TOTAL_COMPRA_C_BOLETO']) ? 1 : 0) ?>">R$: <?php echo number_format($TOTAL['TOTAL_COMPRA'], 2, ',', '.'); ?></td>
              </tr>
            </table>
            <span class="hidden" quantidade_parcela=""></span>
            <button type="button" class="btn btn-primary btn-lg btn-block" tabindex="99" onclick="$('#finalizar-pedido').click();">
              finalizar pedido
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!--[ DADOS DO CARRINHO ]-->

</div>
<style>
  .input-error-span-2 {
    margin: 3px 0 0 0 !important;
    font-size: 12px;
    font-family: arial;
    min-width: 128px;
  }

  .endereco-checked {
    background-color: #fcf8e3;
    border-color: #facbcc;
    border-width: 1px;
    border-style: solid;
  }

  @media(max-width: 768px) {
    .new-checkout {
      margin-top: 0;
    }
  }

  @media(min-width: 768px) {
    #carregar-compra {
      right: 0;
      position: absolute;
    }
  }
</style>
<?php ob_start(); ?>
<!-- <script src="https://assets.pagar.me/pagarme-js/3.0/pagarme.min.js"></script> -->
<script src="https://assets.pagar.me/pagarme-js/4.5/pagarme.min.js"></script>
<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
<?php $BIBLIOTECAS .= ob_get_clean(); ?>

<?php ob_start(); ?>
<script>
  <?php include dirname(__DIR__) . '/_identificacao/js/checkout-new-js.js' ?>
</script>
<?php $str['script_manual'] .= ob_get_clean();
