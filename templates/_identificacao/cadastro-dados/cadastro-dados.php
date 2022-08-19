<?php

/**
 * Buscar os dados do cliente
 * Todos os dados do cliente estarão disponíveis em um unico so lugar
 */
// $Cliente = isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != '' ?
//                                                                         Clientes::first(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]] ) : null;

// $ClienteInit = isset( $_SESSION['cliente']['id_cliente'] ) && $_SESSION['cliente']['id_cliente'] != '' ? $Cliente->to_array() : null;

$cliente = isset($ErrorCheckoutCadastreSe['cadastro']) && count($ErrorCheckoutCadastreSe['cadastro']) > 0 ? $post : $CONFIG['cliente_session'];

$URL_INIT = count($cliente['enderecos']) > 0 || $STORE['config']['endereco']['configure']['status'] == null
  ? URL_BASE . 'identificacao/checkout-new' : URL_BASE . 'identificacao/checkout-new/?AcaoEnderecos=CadastrarEnderecos&Badge=2';
?>

<form class="new-checkout-validate" id="new-cadastro-cliente" action="<?php echo URL_BASE ?>identificacao/checkout-new?AcaoCliente=<?php echo $GET['AcaoCliente'] ?>&Badge=<?php echo $GET['Badge'] ?>&_u=<?php echo $URL_INIT ?>" method="post">
  <input type="hidden" name="acao" value="<?php echo sha1(($GET['AcaoCliente'] == 'EditarCadastro' ? 'NewCheckoutEditarCadastro' : 'NewCheckoutCadastreSe')) ?>" />
  <input type="hidden" name="cliente[hashcliente]" value="<?php echo isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != '' ? $_SESSION['cliente']['id_cliente'] : '' ?>" id="hashcliente" />
  <div class="new-caixa-checkout clearfix<?php echo !empty($GET['Badge']) && $GET['Badge'] == '1' ? ' new-caixa-checkout-active' : '' ?>">
    <div class="clearfix model-border-bottom-thin">
      <span class="badge pull-left<?php echo !empty($GET['Badge']) && $GET['Badge'] == '1' ? ' active' : '' ?>">1</span>
      <a class="fa fa-pencil ft22px pull-right checkout-editar-dados<?php echo ($GET['AcaoCliente'] != 'EditarCadastro' && $_SESSION['cliente']['id_cliente'] == '') ? ' hidden' : '' ?>" href="<?php echo Url::getBase() ?>identificacao/checkout-new/?AcaoCliente=EditarCadastro&Badge=1"></a>
      <span class="title pull-left">Informações Pessoais</span>
      <small class="pull-left mb5" style="width: 100%;">Nos informe somente alguns de seus dados pessoais para realizarmos a venda</small>
    </div>

    <?php if (
      !empty($_SESSION['cliente']['id_cliente'])
      && ($_SESSION['cliente']['id_cliente'] != '' && $GET['AcaoCliente'] != 'EditarCadastro')
      && count($ErrorCheckoutCadastreSe) == 0
    ) : ?>
      <div class="row mt10">
        <div class="col-md-12">
          <p class="show ft18px mb10"><?php echo $cliente['nome'] ?></p>
          <span class="<?php echo !empty($cliente['email']) ? 'show' : 'hidden' ?> mb5">E-mail: <?php echo $cliente['email'] ?></span>
          <span class="<?php echo !empty($cliente['telefone']) ? 'show' : 'hidden' ?> mb5">Tel: <?php echo $cliente['telefone'] ?></span>
          <span class="<?php echo !empty($cliente['cidade']) ? 'show' : 'hidden' ?> mb5">Cidade: <?php echo $cliente['cidade'] ?></span>
          <span class="<?php echo !empty($cliente['uf']) ? 'show' : 'hidden' ?> mb5">UF: <?php echo $cliente['uf'] ?></span>
          <span class="show text-right ft12px">
            <a href="/identificacao/sair">Fazer Logout</a>
          </span>
        </div>
      </div>
    <?php else : ?>

      <div class="clearfix" id="ckeckout-editar-dados">
        <?php if ($STORE['config']['cadastro']['tipopessoa']['status'] == true) : ?>
          <div class="pull-left mt25">
            <input type="radio" id="tipopessoa-a" class="input-radio" value="1" name="TipoPessoa" <?php echo strlen(soNumero($cliente['cpfcnpj'])) == 11 || $cliente['cpfcnpj'] == '' ? 'checked' : '' ?> />
            <label class="fa ft22px" for="tipopessoa-a"></label>
            Pessoa Fisica.

            <input type="radio" id="tipopessoa-b" class="input-radio" value="2" name="TipoPessoa" <?php echo strlen(soNumero($cliente['cpfcnpj'])) > 11 ? 'checked' : '' ?> />
            <label class="fa ft22px" for="tipopessoa-b"></label>
            Pessoa Jurídica.
          </div>
        <?php endif; ?>

        <?php if ($STORE['config']['cadastro']['email']['status'] == true) : ?>
          <div class="pull-left mt25">
            <label class="pull-left mb5 ft15px"><?php echo $STORE['config']['cadastro']['email']['text'] ?></label>
            <span class="input-falsos mb15">
              <i class="fa fa-envelope"></i>
              <input type="email" autocomplete="off" name="cadastro[email]" value="<?php echo isset($GET['email']) ? $GET['email'] : is_post_value($cliente['email']) ?>" id="email" />
              <?php echo tigger_error($ErrorCheckoutCadastreSe['cadastro']['email']) ?>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($STORE['config']['cadastro']['nome']['status'] == true) : ?>
          <div class="pull-left">
            <label class="pull-left mb5 ft15px" data-tipo-name="<?php echo $STORE['config']['cadastro']['nome']['text-attr'] ?>">
              <?php echo $STORE['config']['cadastro']['nome']['text'] ?>
            </label>
            <span class="input-falsos mb15">
              <i class="fa fa-user"></i>
              <input type="text" autocomplete="off" name="cadastro[nome]" value="<?php echo is_post_value($cliente['nome']) ?>" id="nome" />
              <?php echo tigger_error($ErrorCheckoutCadastreSe['cadastro']['nome']) ?>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($STORE['config']['cadastro']['cpfcnpj']['status'] == true) : ?>
          <div class="pull-left">
            <label class="pull-left mb5 ft15px" data-tipo-cpfcnpj="<?php echo $STORE['config']['cadastro']['cpfcnpj']['text-attr'] ?>">
              <?php echo $STORE['config']['cadastro']['cpfcnpj']['text'] ?>
            </label>
            <span class="input-falsos mb15">
              <i class="fa fa-id-card-o"></i>
              <input type="text" autocomplete="off" name="cadastro[cpfcnpj]" value="<?php echo is_post_value($cliente['cpfcnpj']) ?>" data-mask="cpfcnpj" id="cpfcnpj" />
              <?php echo tigger_error($ErrorCheckoutCadastreSe['cadastro']['cpfcnpj']) ?>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($STORE['config']['cadastro']['rg']['status'] == true) : ?>
          <div class="pull-left">
            <label class="pull-left mb5 ft15px" data-tipo-cpfcnpj="<?php echo $STORE['config']['cadastro']['rg']['text-attr'] ?>"><?php echo $STORE['config']['cadastro']['rg']['text'] ?></label>
            <span class="input-falsos mb15">
              <i class="fa fa-id-card-o"></i>
              <input type="text" autocomplete="off" name="cadastro[rg]" value="<?php echo is_post_value($cliente['rg']) ?>" data-mask="rg" id="rg" />
              <?php echo tigger_error($ErrorCheckoutCadastreSe['cadastro']['rg']) ?>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($STORE['config']['cadastro']['data_nascimento']['status'] == true) : ?>
          <div class="pull-left">
            <label class="pull-left mb5 ft15px" data-tipo-cpfcnpj="<?php echo $STORE['config']['cadastro']['data_nascimento']['text-attr'] ?>">
              <?php echo $STORE['config']['cadastro']['data_nascimento']['text'] ?></label>
            <span class="input-falsos mb15">
              <i class="fa fa-calendar-minus-o"></i>
              <input type="text" autocomplete="off" name="cadastro[data_nascimento]" value="<?php echo is_post_value($cliente['data_nascimento']) ?>" data-mask="data_nascimento" id="data_nascimento" />
              <?php echo tigger_error($ErrorCheckoutCadastreSe['cadastro']['data_nascimento']) ?>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($STORE['config']['cadastro']['telefone']['status'] == true) : ?>
          <div class="pull-left">
            <label class="pull-left mb5 ft15px"><?php echo $STORE['config']['cadastro']['telefone']['text'] ?></label>
            <span class="input-falsos mb15">
              <i class="fa fa-phone"></i>
              <input type="text" autocomplete="off" name="cadastro[telefone]" value="<?php echo is_post_value($cliente['telefone']) ?>" data-mask="telefone" id="telefone" />
              <?php echo tigger_error($ErrorCheckoutCadastreSe['cadastro']['telefone']) ?>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($STORE['config']['cadastro']['celular']['status'] == true) : ?>
          <div class="pull-left">
            <label class="pull-left mb5 ft15px"><?php echo $STORE['config']['cadastro']['celular']['text'] ?></label>
            <span class="input-falsos mb15">
              <i class="fa fa-mobile-phone"></i>
              <input type="text" autocomplete="off" name="cadastro[celular]" value="<?php echo is_post_value($cliente['celular']) ?>" data-mask="telefone" id="celular" />
              <?php echo tigger_error($ErrorCheckoutCadastreSe['cadastro']['celular']) ?>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($STORE['config']['cadastro']['operadora']['status'] == true) : ?>
          <div class="pull-left">
            <label class="pull-left mb5 ft15px"><?php echo $STORE['config']['cadastro']['operadora']['text'] ?></label>
            <span class="input-falsos mb15">
              <i class="fa fa-volume-control-phone"></i>
              <input type="text" autocomplete="off" name="cadastro[operadora]" value="<?php echo is_post_value($cliente['operadora']) ?>" id="operadora" />
              <?php echo tigger_error($ErrorCheckoutCadastreSe['cadastro']['operadora']) ?>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($STORE['config']['cadastro']['sexo']['status'] == true) : ?>
          <div class="pull-left mb15">
            <input type="radio" id="masculino" class="input-radio" value="masculino" name="cadastro[sexo]" <?php echo $cliente['sexo'] == 'masculino' ? 'checked' : '' ?> />
            <label class="fa ft22px" for="masculino"></label>
            Masculino.

            <input type="radio" id="feminino" class="input-radio" value="feminino" name="cadastro[sexo]" <?php echo $cliente['sexo'] == 'feminino' ? 'checked' : '' ?> />
            <label class="fa ft22px" for="feminino"></label>
            Feminino.
          </div>
        <?php endif; ?>

        <?php if ($STORE['config']['cadastro']['cidade']['status'] == true) : ?>
          <div class="pull-left">
            <label class="pull-left mb5 ft15px"><?php echo $STORE['config']['cadastro']['cidade']['text'] ?></label>
            <span class="input-falsos mb15">
              <i class="fa fa-id-card-o"></i>
              <input type="text" autocomplete="off" name="cadastro[cidade]" value="<?php echo is_post_value($cliente['cidade']) ?>" id="cidade" />
              <?php echo tigger_error($ErrorCheckoutCadastreSe['cadastro']['cidade']) ?>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($STORE['config']['cadastro']['uf']['status'] == true) : ?>
          <div class="pull-left mb10">
            <div class="row">
              <div class="col-md-4 col-sm-12">
                <label class="pull-left mb5 ft15px"><?php echo $STORE['config']['cadastro']['uf']['text'] ?></label>
                <span class="input-falsos mb15">
                  <i class="fa fa-id-card-o"></i>
                  <input type="text" autocomplete="off" name="cadastro[uf]" value="<?php echo is_post_value($cliente['uf']) ?>" id="uf" />
                  <?php echo tigger_error($ErrorCheckoutCadastreSe['cadastro']['uf']) ?>
                </span>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if (empty($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] == '') : ?>
          <div class="pull-left mb10">
            <div class="row">
              <div class="col-md-12">
                <label class="pull-left mb5 ft15px">Senha de acesso</label>
                <span class="input-falsos mb15">
                  <i class="fa fa-lock"></i>
                  <input type="password" autocomplete="off" name="cadastro[senha_real]" id="senha_real" />
                  <?php echo tigger_error($ErrorCheckoutCadastreSe['cadastro']['senha_real']) ?>
                </span>
              </div>
              <div class="col-md-12">
                <label class="pull-left mb5 ft15px">Repita sua Senha</label>
                <span class="input-falsos mb15">
                  <i class="fa fa-lock"></i>
                  <input type="password" autocomplete="off" name="cadastro[senha_confirm]" id="senha_confirm" />
                  <?php echo tigger_error($ErrorCheckoutCadastreSe['cadastro']['senha_confirm']) ?>
                </span>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <button type="submit" data-type="submit" class="btn btn-block pull-left btn-lg btn-primary<?php echo (($_SESSION['cliente']['id_cliente'] != '' && $GET['AcaoCliente'] != 'EditarCadastro')  && count($ErrorCheckoutCadastreSe) == 0) ? ' hidden' : '' ?>">
        salvar dados
      </button>
    <?php endif; ?>
  </div>
  <error id="error" style="display: none; visibility: visible;">
    <?php echo tigger_error((is_array($ErrorCheckoutCadastreSe['cadastro']) ? current($ErrorCheckoutCadastreSe['cadastro']) : false)); ?>
    <?php
    //        if( is_array( $ErrorCheckoutCadastreSe['cadastro'] ) ) {
    //            foreach( $ErrorCheckoutCadastreSe['cadastro'] as $k => $text ) {
    ////                $text = preg_replace_callback('/<script>.*?<\/script>/sim',
    ////                        function($match) {
    ////                            preg_match('/.*?/sim', $match[0], $h3);
    ////                            return $h3[0];
    ////                }, $text);
    //                echo "{$text}<br/>";
    //            }
    //        }
    ?>
  </error>
</form>
