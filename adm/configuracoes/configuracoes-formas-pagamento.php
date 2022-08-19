<?php
include '../topo.php';

// $Configuracoes = Configuracoes::first(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
// $rsedit = ($Configuracoes->to_array());

$ConfiguracoesPagamento = ConfiguracoesPagamento::first(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
// $ConfiguracoesPagamento-> $ConfiguracoesPagamentoto_array();

?>

<style>
  body {
    background-color: #f1f1f1
  }
</style>

<style>
  .formulario div {
    border-radius: 3px;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
  }

  .formulario p {
    margin: 5px 0 7px 0;
    font-weight: 500;
  }
</style>

<div class="container-fluid" id="recarregar-form">
  <?php
  $mensagem = array();
  if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($POST['opcoes']) && $POST['opcoes'] != '') {
      $value = (array)$POST['opcoes'];
      if (ConfiguracoesPagamento::action_cadastrar_editar([
        'ConfiguracoesPagamento' => [$POST['id'] => [
          'transferencia' => (isset($value['transferencia']) ? '1' : '0'),

          'pagseguro' => (isset($value['pagseguro']) ? $value['pagseguro'] : 0),
          'pagseguro_mode' => (isset($value['pagseguro_mode']) ? $value['pagseguro_mode'] : 0),
          'pagseguro_email' => (isset($value['pagseguro_email']) ? $value['pagseguro_email'] : ''),
          'pagseguro_token' => (isset($value['pagseguro_token']) ? $value['pagseguro_token'] : ''),

          'pix' => (isset($value['pix']) ? $value['pix'] : 0),
          'pix_key' => (isset($value['pix_key']) ? $value['pix_key'] : ''),
          'pix_name' => (isset($value['pix_name']) ? $value['pix_name'] : ''),
          'pix_city' => (isset($value['pix_city']) ? $value['pix_city'] : ''),
          'pix_tipo' => (isset($value['pix_tipo']) ? $value['pix_tipo'] : ''),

          'boleto' => (isset($value['boleto']) ? $value['boleto'] : 0),
          'boleto_mode' => (isset($value['boleto_mode']) ? $value['boleto_mode'] : ''),
          'boleto_token' => (isset($value['boleto_token']) ? $value['boleto_token'] : ''),
          'boleto_venc' => (isset($value['boleto_venc']) ? $value['boleto_venc'] : ''),
          'boleto_client' => (isset($value['boleto_client']) ? $value['boleto_client'] : ''),
          'boleto_secret' => (isset($value['boleto_secret']) ? $value['boleto_secret'] : ''),
          'boleto_pix' => (isset($value['boleto_pix']) ? $value['boleto_pix'] : ''),

          'pagarme' => (isset($value['pagarme']) ? $value['pagarme'] : 0),
          'pagarme_mode' => (isset($value['pagarme_mode']) ? $value['pagarme_mode'] : 0),
          'pagarme_boleto' => (isset($value['pagarme_boleto']) ? $value['pagarme_boleto'] : 0),
          'pagarme_api_key' => (isset($value['pagarme_api_key']) ? $value['pagarme_api_key'] : ''),
          'pagarme_api_token' => (isset($value['pagarme_api_token']) ? $value['pagarme_api_token'] : ''),

          'cielo' => (isset($value['cielo']) ? $value['cielo'] : 0),
          'cielo_mode' => (isset($value['cielo_mode']) ? $value['cielo_mode'] : 0),
          'cielo_merchantid' => (isset($value['cielo_merchantid']) ? $value['cielo_merchantid'] : ''),
          'cielo_merchantkey' => (isset($value['cielo_merchantkey']) ? $value['cielo_merchantkey'] : ''),
          'cielo_mid' => (isset($value['cielo_mid']) ? $value['cielo_mid'] : ''),

          'mp' => (isset($value['mp']) ? $value['mp'] : 0),
          'mp_mode' => (isset($value['mp_mode']) ? $value['mp_mode'] : 0),
          'mp_public_key' => (isset($value['mp_public_key']) ? $value['mp_public_key'] : ''),
          'mp_access_token' => (isset($value['mp_access_token']) ? $value['mp_access_token'] : ''),
          'mp_boleto' => (isset($value['mp_boleto']) ? $value['mp_boleto'] : ''),

          'picpay' => (isset($value['picpay']) ? $value['picpay'] : 0),
          // 'picpay_mode' => (isset( $value['picpay_mode'] ) ? $value['picpay_mode'] : 0),
          'picpay_token' => (isset($value['picpay_token']) ? $value['picpay_token'] : ''),
          'picpay_seller' => (isset($value['picpay_seller']) ? $value['picpay_seller'] : ''),
          'picpay_venc' => (isset($value['picpay_venc']) ? $value['picpay_venc'] : ''),

        ]]
      ], 'alterar', 'id')) {
        header('Location: /adm/configuracoes/configuracoes-formas-pagamento.php');
        return;
      }
    }
  }

  ?>
  <!--[PAGAMENTO VIA PAGSEGURO]-->
  <form action="/adm/configuracoes/configuracoes-pagamentos-opcoes.php" method="post" class="form-action formulario row">
    <input type="hidden" name="id" value="<?php echo $ConfiguracoesPagamento->id ?>" />
    <div class="col-md-12 col-xs-12">
      <h2>Formas de Pagamentos <small>Configure as formas de pagamento de sua loja</small></h2>
    </div>

    <!--[PAGAMENTO VIA PAGARME]-->
    <div class="col-md-6 col-xs-12  mb15">
      <div class="panel panel-default">
        <div class="panel-heading panel-store text-uppercase">
          <input type="checkbox" name="opcoes[pagarme]" id="PagarMe" value="1" <?php echo $ConfiguracoesPagamento->pagarme == 1 ? 'checked' : '' ?> />
          <label for="PagarMe" class="input-checkbox mt5"></label>
          PagarMe
        </div>
        <div class="abri-configuracoes clearfix panel-body">
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Modo: <font class="ft11px">(Sandbox / Prodution )</font></span>

            <input type="radio" name="opcoes[pagarme_mode]" id="PagarMeMode" value="0" <?php echo $ConfiguracoesPagamento->pagarme_mode == '0' ? 'checked' : '' ?> />
            <label for="PagarMeMode" class="input-radio"></label> Modo de Teste

            <input type="radio" name="opcoes[pagarme_mode]" id="PagarMeMode2" value="1" <?php echo $ConfiguracoesPagamento->pagarme_mode == '1' ? 'checked' : '' ?> />
            <label for="PagarMeMode2" class="input-radio"></label> Modo de Vendas

            <input type="checkbox" name="opcoes[pagarme_boleto]" id="pagarme_boleto" value="1" <?php echo $ConfiguracoesPagamento->pagarme_boleto == '1' ? 'checked' : '' ?> />
            <label for="pagarme_boleto" class="input-checkbox"></label> (Incluir Boleto)
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Pagar Me API KEY: <font class="ft11px">(Os dados são digitado no ambiente da loja 'https')</font></span>
            <input type="text" name="opcoes[pagarme_api_key]" value="<?php echo $ConfiguracoesPagamento->pagarme_api_key; ?>" class="form-control" />
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Pagar Me API TOKEN: <font class="ft11px">(Os dados são digitado no ambiente da loja 'https')</font></span>
            <input type="text" name="opcoes[pagarme_api_token]" value="<?php echo $ConfiguracoesPagamento->pagarme_api_token; ?>" class="form-control" />
          </span>
          <span class="show w100 mb15">
            <button type="submit" class="btn btn-primary">salvar</button>
          </span>
        </div>
      </div>
    </div>
    <!--[END PAGAMENTO VIA PAGARME]-->

    <!--[PAGAMENTO VIA PAGSEGURO]-->
    <div class="col-md-6 col-xs-12  mb15">
      <div class="panel panel-default">
        <div class="panel-heading panel-store text-uppercase">
          <input type="checkbox" name="opcoes[pagseguro]" id="PagSeguro" value="1" <?php echo $ConfiguracoesPagamento->pagseguro == '1' ? 'checked' : '' ?> />
          <label for="PagSeguro" class="input-checkbox mt5"></label>
          PagSeguro
        </div>
        <div class="abri-configuracoes clearfix panel-body">
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Modo: <font class="ft11px">(Sandbox / Prodution )</font></span>

            <input type="radio" name="opcoes[pagseguro_mode]" id="PagSeguroMode" value="0" <?php echo $ConfiguracoesPagamento->pagseguro_mode == '0' ? 'checked' : '' ?> />
            <label for="PagSeguroMode" class="input-radio"></label> Modo de Teste

            <input type="radio" name="opcoes[pagseguro_mode]" id="PagSeguroMode2" value="1" <?php echo $ConfiguracoesPagamento->pagseguro_mode == '1' ? 'checked' : '' ?> />
            <label for="PagSeguroMode2" class="input-radio"></label> Modo de Vendas
          </span>

          <span class="pull-left w100 mb15">
            <span class="mb5 show">E-mail: <font class="ft11px">(E-mail de casdastro no PagSeguro)</font></span>
            <input type="text" name="opcoes[pagseguro_email]" value="<?php echo $ConfiguracoesPagamento->pagseguro_email; ?>" class="form-control" />
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Token: <font class="ft11px">(Token é gerado no PagSeguro)</font></span>
            <input type="text" name="opcoes[pagseguro_token]" value="<?php echo $ConfiguracoesPagamento->pagseguro_token; ?>" class="form-control" />
          </span>
          <span class="show w100 mb15">
            <button type="submit" class="btn btn-primary">salvar</button>
          </span>
        </div>
      </div>
    </div>
    <!--[END PAGAMENTO VIA PAGSEGURO]-->

    <!--[PAGAMENTO VIA CIELO]-->
    <div class="col-md-6 col-xs-12  mb15">
      <div class="panel panel-default">
        <div class="panel-heading panel-store text-uppercase">
          <input type="checkbox" name="opcoes[mp]" id="mp" value="1" <?php echo $ConfiguracoesPagamento->mp == 1 ? 'checked' : '' ?> />
          <label for="mp" class="input-checkbox mt5"></label>
          Mercado Pago
        </div>
        <div class="abri-configuracoes clearfix panel-body">
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Modo: <font class="ft11px">(Sandbox / Prodution )</font></span>

            <input type="radio" name="opcoes[mp_mode]" id="mp_mode" value="0" <?php echo $ConfiguracoesPagamento->mp_mode == '0' ? 'checked' : '' ?> />
            <label for="mp_mode" class="input-radio"></label> Modo de Teste

            <input type="radio" name="opcoes[mp_mode]" id="mp_mode_2" value="1" <?php echo $ConfiguracoesPagamento->mp_mode == '1' ? 'checked' : '' ?> />
            <label for="mp_mode_2" class="input-radio"></label> Modo de Vendas

            <input type="checkbox" name="opcoes[mp_boleto]" id="mp_boleto" value="1" <?php echo $ConfiguracoesPagamento->mp_boleto == '1' ? 'checked' : '' ?> />
            <label for="mp_boleto" class="input-checkbox"></label> (Incluir Boleto)
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Chave Public Key:</span>
            <input type="text" name="opcoes[mp_public_key]" value="<?php echo $ConfiguracoesPagamento->mp_public_key; ?>" class="form-control" />
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Chave Access Token:</span>
            <input type="text" name="opcoes[mp_access_token]" value="<?php echo $ConfiguracoesPagamento->mp_access_token; ?>" class="form-control" />
          </span>
          <span class="show w100 mb15">
            <button type="submit" class="btn btn-primary">salvar</button>
          </span>
        </div>
      </div>
    </div>
    <!--[END PAGAMENTO VIA CIELO]-->

    <!--[PAGAMENTO VIA BOLETO]-->
    <div class="col-md-6 col-xs-12  mb15">
      <div class="panel panel-default">
        <div class="panel-heading panel-store text-uppercase">
          <input type="checkbox" name="opcoes[boleto]" id="Boleto" value="1" <?php echo $ConfiguracoesPagamento->boleto == 1 ? 'checked' : '' ?> />
          <label for="Boleto" class="input-checkbox mt5"></label>
          Boleto Fácil
        </div>
        <div class="abri-configuracoes clearfix panel-body">
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Modo: <font class="ft11px">(Sandbox / Prodution )</font></span>
            <input type="radio" name="opcoes[boleto_mode]" id="BoletoMode" value="0" <?php echo $ConfiguracoesPagamento->boleto_mode == '0' ? 'checked' : '' ?> />
            <label for="BoletoMode" class="input-radio"></label> Modo de Teste
            <input type="radio" name="opcoes[boleto_mode]" id="BoletoMode2" value="1" <?php echo $ConfiguracoesPagamento->boleto_mode == '1' ? 'checked' : '' ?> />
            <label for="BoletoMode2" class="input-radio"></label> Modo de Vendas
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Token: <font class="ft11px">(Token de pagamento gerado no Boleto Fácil)</font></span>
            <input type="text" name="opcoes[boleto_token]" value="<?php echo $ConfiguracoesPagamento->boleto_token; ?>" class="form-control" />
          </span>
          <span class="pull-left w40 mb15">
            <span class="mb5 show">Client ID: <font class="ft11px">(Dados encontrados na integração Juno)</font></span>
            <input type="text" name="opcoes[boleto_client]" value="<?php echo $ConfiguracoesPagamento->boleto_client; ?>" class="form-control" />
          </span>
          <span class="pull-left w40 mb15 ml15 mr15">
            <span class="mb5 show">Secret: <font class="ft11px">(Dados encontrados na integração Juno)</font></span>
            <input type="text" name="opcoes[boleto_secret]" value="<?php echo $ConfiguracoesPagamento->boleto_secret; ?>" class="form-control" />
          </span>
          <span class="pull-left w50 mb15">
            <span class="mb5 show">Chave Pix: <font class="ft11px">(Dados encontrados na integração Juno)</font></span>
            <input type="text" name="opcoes[boleto_pix]" value="<?php echo $ConfiguracoesPagamento->boleto_pix; ?>" class="form-control" />
          </span>
          <span class="pull-left w30 ml15 mb15">
            <span class="mb5 show">Vencimento: <font class="ft11px">(Dias de vencimento)</font></span>
            <input type="text" name="opcoes[boleto_venc]" value="<?php echo $ConfiguracoesPagamento->boleto_venc; ?>" class="text-right form-control" />
          </span>
          <span class="pull-left w100 mb15">
            <button type="submit" class="btn btn-primary">salvar</button>
          </span>
        </div>
      </div>
    </div>
    <!--[PAGAMENTO VIA BOLETO]-->


    <!--[PAGAMENTO VIA PIX]-->
    <div class="col-md-6 col-xs-12 mb15">
      <div class="panel panel-default">
        <div class="panel-heading panel-store text-uppercase">
          <input type="checkbox" name="opcoes[pix]" id="pix" value="1" <?php echo $ConfiguracoesPagamento->pix == 1 ? 'checked' : '' ?> />
          <label for="pix" class="input-checkbox mt5"></label>
          Pgamento Via Pix
        </div>
        <div class="abri-configuracoes clearfix panel-body">
          <span class="pull-left w30 mr25 mb15">
            <span class="mb5 show">Tipo de Chave:</span>
            <select name="opcoes[pix_tipo]" id="pix_tipo" style="width: 100%;">
              <?php for ($f = 0; $f < 5; $f++) { ?>
                <option value="<?php echo $f; ?>" <?php echo $ConfiguracoesPagamento->pix_tipo == $f ? ' selected' : null ?>><?php echo chaves_tipo($f); ?></option>
              <?php } ?>
            </select>
          </span>
          <span class="pull-left w65 mb15">
            <span class="mb5 show">PIX KEY: <font class="ft11px">(Sua chave de pagamento Pix)</font></span>
            <input type="text" name="opcoes[pix_key]" value="<?php echo $ConfiguracoesPagamento->pix_key; ?>" class="form-control" />
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">PIX NAME: <font class="ft11px">(Nome da sua instituição Pix)</font></span>
            <input type="text" name="opcoes[pix_name]" value="<?php echo $ConfiguracoesPagamento->pix_name; ?>" class="form-control" />
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">PIX Cidade: <font class="ft11px">(Defina o nome da cidade)</font></span>
            <input type="text" name="opcoes[pix_city]" value="<?php echo $ConfiguracoesPagamento->pix_city; ?>" class="form-control" />
          </span>
          <span class="show w100 mb15">
            <button type="submit" class="btn btn-primary">salvar</button>
          </span>
          <small>* Nota: Para chaves com número de telefone, deve se implementar o DDI + DDD e o número de telefone, sem espaço e sem caracteres especiais</small>
        </div>
      </div>
    </div>
    <!--[PAGAMENTO VIA PIX]-->

    <!--[PAGAMENTO VIA TRANSFERÊNCIA]-->
    <div class="col-md-6 col-xs-12  pull-right mb15">
      <div class="panel panel-default">
        <div class="panel-heading panel-store text-uppercase">
          <input type="checkbox" name="opcoes[transferencia]" id="transferencia" value="1" <?php echo $ConfiguracoesPagamento->transferencia == 1 ? 'checked' : '' ?> />
          <label for="transferencia" class="input-checkbox mt5"></label>
          Transferência Bancária
        </div>
        <div class="abri-configuracoes clearfix panel-body">
          <span class="pull-left w100 mb15">
            <a href="/adm/configuracoes/configuracoes-pagamentos.php" class="btn btn-info" target="_blank">Configurações dos Bancos</a>
          </span>
          <span class="show w100 mb15">
            <button type="submit" class="btn btn-primary">salvar</button>
          </span>
        </div>
      </div>
    </div>
    <!--[PAGAMENTO VIA TRANSFERÊNCIA]-->
    <div class="col-md-12 col-xs-12"></div>
    <!--[PAGAMENTO VIA CIELO]-->
    <div class="col-md-6 col-xs-12  mb15">
      <div class="panel panel-default">
        <div class="panel-heading panel-store text-uppercase">
          <input type="checkbox" name="opcoes[cielo]" id="Cielo" value="1" <?php echo $ConfiguracoesPagamento->cielo == 1 ? 'checked' : '' ?> />
          <label for="Cielo" class="input-checkbox mt5"></label>
          Cielo
        </div>
        <div class="abri-configuracoes clearfix panel-body">
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Modo: <font class="ft11px">(Sandbox / Prodution )</font></span>

            <input type="radio" name="opcoes[cielo_mode]" id="CieloMode" value="0" <?php echo $ConfiguracoesPagamento->cielo_mode == '0' ? 'checked' : '' ?> />
            <label for="CieloMode" class="input-radio"></label> Modo de Teste

            <input type="radio" name="opcoes[cielo_mode]" id="CieloMode2" value="1" <?php echo $ConfiguracoesPagamento->cielo_mode == '1' ? 'checked' : '' ?> />
            <label for="CieloMode2" class="input-radio"></label> Modo de Vendas
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Merchant ID: <font class="ft11px">(Os dados são digitado no ambiente da loja 'https')</font></span>
            <input type="text" name="opcoes[cielo_merchantid]" value="<?php echo $ConfiguracoesPagamento->cielo_merchantid; ?>" class="form-control" />
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Merchant Key: <font class="ft11px">(Os dados são digitado no ambiente da loja 'https')</font></span>
            <input type="text" name="opcoes[cielo_merchantkey]" value="<?php echo $ConfiguracoesPagamento->cielo_merchantkey; ?>" class="form-control" />
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">Merchant MID: <font class="ft11px">(Os dados são digitado no ambiente da cielo)</font></span>
            <input type="text" name="opcoes[cielo_mid]" value="<?php echo $ConfiguracoesPagamento->cielo_mid; ?>" class="form-control" />
          </span>
          <span class="show w100 mb15">
            <button type="submit" class="btn btn-primary">salvar</button>
          </span>
        </div>
      </div>
    </div>
    <!--[END PAGAMENTO VIA CIELO]-->

    <!--[PAGAMENTO VIA PICPAY]-->
    <div class="col-md-6 col-xs-12  mb15">
      <div class="panel panel-default">
        <div class="panel-heading panel-store text-uppercase">
          <input type="checkbox" name="opcoes[picpay]" id="PicPay" value="1" <?php echo $ConfiguracoesPagamento->picpay == 1 ? 'checked' : '' ?> />
          <label for="PicPay" class="input-checkbox mt5"></label>
          PicPay
        </div>
        <div class="abri-configuracoes clearfix panel-body">
          <!--
                    <span class="pull-left w100 mb15">
                        <span class="mb5 show">Modo: <font class="ft11px">(Sandbox / Prodution)</font></span>
                        <input type="radio" name="opcoes[pagarme_mode]" id="PicPayMode" value="0" <?php echo $ConfiguracoesPagamento->picpay_mode == '0' ? 'checked' : '' ?>/>
                        <label for="PicPayMode" class="input-radio"></label> Modo de Teste
                        <input type="radio" name="opcoes[pagarme_mode]" id="PicPayMode2" value="1" <?php echo $ConfiguracoesPagamento->picpay_mode == '1' ? 'checked' : '' ?>/>
                        <label for="PicPayMode2" class="input-radio"></label> Modo de Vendas
                    </span>
                    -->
          <span class="pull-left w100 mb15">
            <span class="mb5 show">PicPay Token:</span>
            <input type="text" name="opcoes[picpay_token]" value="<?php echo $ConfiguracoesPagamento->picpay_token; ?>" class="form-control" />
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">PicPay Seller:</span>
            <input type="text" name="opcoes[picpay_seller]" value="<?php echo $ConfiguracoesPagamento->picpay_seller; ?>" class="form-control" />
          </span>
          <span class="pull-left w100 mb15">
            <span class="mb5 show">PicPay Vencimento:</span>
            <input type="text" name="opcoes[picpay_venc]" value="<?php echo $ConfiguracoesPagamento->picpay_venc; ?>" class="form-control" style="width: 80px;" />
          </span>
          <span class="show w100 mb15">
            <button type="submit" class="btn btn-primary">salvar</button>
          </span>
        </div>
      </div>
    </div>
    <!--[END PAGAMENTO VIA PICPAY]-->

  </form>
</div>

<script>
  <?php ob_start(); ?>
  $("#recarregar-form").on("click", "input[type=checkbox]", function(e) {
    var $this = $(this)
    $AbriConfiguracoes = $this.parent().parent().find(".abri-configuracoes");
    if ($AbriConfiguracoes.is(":visible")) {
      $AbriConfiguracoes.fadeOut();
      $this.parent().parent().find("button[type=submit]").trigger("click");
    } else {
      $AbriConfiguracoes.fadeIn();
    }
  });

  $("#recarregar-form").on("submit", "form", function(e) {
    e.preventDefault();
    var $form = $(this);

    $(this).ajaxSubmit({
      uploadProgress: function(event, position, total, percentComplete) {
        infoSite("Enviando: " + percentComplete + "%", "info-concluido");
      },
      success: function(str) {
        var list = $("<div/>", {
          html: str
        });
        $("#recarregar-form").html(list.find("#recarregar-form").html());
      },
      error: function(x, t, m) {
        console.log(x.responseText + "\n" + t + "\n" + m);
      },
      url: window.location.href,
      type: "post",
      data: $form.serializeArray(),
      dataType: "html",
      cache: false
    });
  });
  <?php
  $SCRIPT['script_manual'] .= ob_get_clean();

  ?>
</script>
<?php
include '../rodape.php';
