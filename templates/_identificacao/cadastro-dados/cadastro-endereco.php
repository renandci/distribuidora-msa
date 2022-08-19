<form class="mt15 new-checkout-validate<?php echo empty($_SESSION['cliente']['id_cliente']) || $GET['AcaoCliente'] != '' ? ' hidden' : '' ?>" id="new-cadastro-enderecos" action="<?php echo $STORE['config']['url'] ?>&_u=<?php echo URL_BASE ?>identificacao/checkout-new" method="post">
  <div class="new-caixa-checkout clearfix<?php echo !empty($GET['Badge']) && $GET['Badge'] == '2' ? ' new-caixa-checkout-active' : '' ?>">
    <div class="clearfix model-border-bottom-thin">
      <span class="badge pull-left<?php echo !empty($GET['Badge']) && $GET['Badge'] == '2' ? ' active' : '' ?>">2</span>
      <a class="fa fa-pencil ft22px pull-right checkout-editar-dados<?php echo ($GET['AcaoEnderecos'] == 'MostrarEnderecos' && $_SESSION['cliente']['id_cliente'] != '') ? ' hidden' : '' ?>" href="<?php echo Url::getBase() ?>identificacao/checkout-new/?AcaoEnderecos=MostrarEnderecos&Badge=2"></a>
      <span class="title pull-left">Endereço de Entrega</span>
      <small class="pull-left mb5" style="width: 100%;">Cadastre seu endereço de entrega</small>
    </div>

    <?php if (!empty($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != '') : ?>
      <!--[ IF CLIENTE LOGADO ]-->
      <?php foreach ($CONFIG['cliente_session']['enderecos'] as $endereco) : ?>
        <?php if ($endereco['status'] == 'ativo' && $GET['AcaoEnderecos'] == '') : ?>
          <a class="pull-right btn btn-xs ml5 ft13px checkout-editar-dados<?php echo ($GET['AcaoEnderecos'] == 'CadastrarEnderecos' && $_SESSION['cliente']['id_cliente'] != '') ? ' hidden' : '' ?>" href="<?php echo Url::getBase() ?>identificacao/checkout-new/?AcaoEnderecos=CadastrarEnderecos&Badge=2">Cadastrar um novo</a>
          <a class="pull-right btn btn-xs ft13px checkout-editar-dados<?php echo ($GET['AcaoEnderecos'] == 'CadastrarEnderecos' && $_SESSION['cliente']['id_cliente'] != '') ? ' hidden' : '' ?>" href="<?php echo Url::getBase() ?>identificacao/checkout-new/?AcaoEnderecos=MostrarEnderecos&Badge=2">Selecionar outro</a>
          <!--[ ENDERECO ATIVO ]-->
          <div class="row mt10 mb15">
            <div class="col-md-12">
              <?php if (!empty($endereco['nome'])) { ?>
                <p class="show ft18px mb5"><?php echo $endereco['nome'] ?></p>
              <?php } ?>
              <?php if (!empty($endereco['receber'])) { ?>
                <span class="show"><?php echo $endereco['receber'] ?></span>
              <?php } ?>
              <?php if (!empty($endereco['endereco'])) { ?>
                <span class="show"><?php echo $endereco['endereco'] ?> - <?php echo $endereco['numero'] ?>, <?php echo $endereco['bairro'] ?></span>
              <?php } ?>
              <?php if (!empty($endereco['complemento'])) { ?>
                <span class="show"><?php echo $endereco['complemento'] ?></span>
              <?php } ?>
              <?php if (!empty($endereco['referencia'])) { ?>
                <span class="show"><?php echo $endereco['referencia'] ?></span>
              <?php } ?>
              <?php if (!empty($endereco['cidade'])) { ?>
                <span class="show"><?php echo $endereco['cidade'] ?>, <?php echo $endereco['uf'] ?></span>
              <?php } ?>
              <?php if (!empty($endereco['cep'])) { ?>
                <span class="show"><?php echo $endereco['cep'] ?></span>
              <?php } ?>
            </div>
          </div>
          <!--[ END ENDERECO ATIVO ]-->
          <!--[ TIPO DE ENVIOS ]-->
          <div id="reload_frete"><?php echo ((!empty($GET['track']) && ($GET['track'] == 'reload_frete' && !empty($endereco['cep']))) ? AtualizarFrete(session_id(), $_SESSION, $endereco['cep']) : null) ?></div>
          <!--[ END TIPO DE ENVIOS ]-->
          <div id="AtualizarPudosJadLog"></div>
        <?php endif; ?>

        <?php if ($GET['AcaoEnderecos'] == 'MostrarEnderecos' && $GET['endereco_id'] == '') : ?>
          <!--[ MOSTRAR TODOS OS ENDERECO CADASTRADOS ]-->
          <fieldset class="show pull-left mt15 model-radius" style="width: 100%;">
            <p class="pull-left ft18px mb5"><?php echo $endereco['nome'] ?></p>
            <a class="fa fa-pencil pull-right ft18px checkout-editar-dados" href="<?php echo Url::getBase() ?>identificacao/checkout-new/?AcaoEnderecos=EditarEndereco&Badge=2&endereco_id=<?php echo sha1($endereco['id']) ?>"></a>
            <a class="fa fa-trash pull-right ft18px mr5" href="<?php echo Url::getBase() ?>identificacao/checkout-new/?AcaoEnderecos=ExcluirEndereco&Badge=2&endereco_id=<?php echo sha1($endereco['id']) ?>"></a>
            <div class="col-md-12 col-xs-12">
              <?php if (!empty($endereco['nome'])) { ?>
                <p class="show ft18px mb5"><?php echo $endereco['nome'] ?></p>
              <?php } ?>
              <?php if (!empty($endereco['endereco'])) { ?>
                <span class="show"><?php echo $endereco['endereco'] ?> - <?php echo $endereco['numero'] ?>, <?php echo $endereco['bairro'] ?></span>
              <?php } ?>
              <?php if (!empty($endereco['complemento'])) { ?>
                <span class="show"><?php echo $endereco['complemento'] ?></span>
              <?php } ?>
              <?php if (!empty($endereco['referencia'])) { ?>
                <span class="show"><?php echo $endereco['referencia'] ?></span>
              <?php } ?>
              <?php if (!empty($endereco['cidade'])) { ?>
                <span class="show"><?php echo $endereco['cidade'] ?>, <?php echo $endereco['uf'] ?></span>
              <?php } ?>
              <?php if (!empty($endereco['cep'])) { ?>
                <span class="show"><?php echo $endereco['cep'] ?></span>
              <?php } ?>
            </div>
            <a href="<?php echo Url::getBase() ?>identificacao/checkout-new/?AcaoEnderecos=SelecionaEndereco&endereco_id=<?php echo $endereco['id'] ?>" class="ft13px pull-right text-right" data-select="endereco">Selecionar Endereço</a>
          </fieldset>
          <!--[ END MOSTRAR TODOS OS ENDERECO CADASTRADOS ]-->
        <?php endif; ?>
      <?php endforeach; ?>
      <!--[ ENDIF CLIENTE LOGADO ]-->
    <?php endif; ?>

    <input type="hidden" name="acao" value="<?php echo sha1('NewCheckoutEnderecos'); ?>" />
    <input type="hidden" name="endereco[hashcliente]" value="<?php echo isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != '' ? $_SESSION['cliente']['id_cliente'] : '' ?>" />

    <?php
    $ClientesEnderecos = count($CONFIG['cliente_session']['enderecos']);
    if ($GET['AcaoEnderecos'] == 'CadastrarEnderecos' || ($GET['AcaoEnderecos'] == 'EditarEndereco' && $GET['endereco_id'] != '') || $ClientesEnderecos == 0) : ?>
      <!--[ EDITAR OU CADASTRAR UM NOVO ENDERECO ]-->
      <?php
      $enderecoInit = isset($_SESSION['cliente']['id_cliente'])
        && $_SESSION['cliente']['id_cliente'] != '' && $GET['AcaoEnderecos'] != 'CadastrarEnderecos' && $ClientesEnderecos > 0 ?
        (ClientesEnderecos::first(['conditions' => ['sha1(id)=?',  $GET['endereco_id']]]))->to_array()
        : (isset($POST['endereco']['hashendereco']) && $POST['endereco']['hashendereco'] != '' ? $POST['endereco']['hashendereco'] : false);
      $endereco = is_array($post) ? $post : $enderecoInit;
      ?>
      <input type="hidden" name="endereco[hashendereco]" value="<?php echo isset($GET['endereco_id']) && $GET['endereco_id'] != '' ? $GET['endereco_id'] : '' ?>" />
      <a class="pull-right ft13px" href="<?php echo Url::getBase() ?>identificacao/checkout-new" data-select="endereco">Voltar</a>
      <?php if ($STORE['config']['endereco']['cep']['status'] == true) : ?>
        <div class="row pull-left">
          <div class="mt15 col-md-6 col-sm-8 col-xs-12">
            <label class="pull-left mb5 ft15px">CEP</label>
            <span class="input-falsos mb15">
              <i class="fa fa-map-marker"></i>
              <input type="text" autocomplete="off" name="endereco[cep]" value="<?php echo is_post_value($endereco['cep']) ?>" data-mask="cep" id="cep" />
              <?php echo tigger_error($ErrorCheckoutEditarEnderecos['endereco']['cep']) ?>
            </span>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($STORE['config']['endereco']['receber']['status'] == true) : ?>
        <div class="pull-left">
          <label class="pull-left mb5 ft15px">Receber</label>
          <span class="input-falsos mb15">
            <i class="fa fa-envelope-o"></i>
            <input type="text" autocomplete="off" name="endereco[receber]" value="<?php echo is_post_value($endereco['receber']) ?>" id="receber" data-input="receber" />
            <?php echo tigger_error($ErrorCheckoutEditarEnderecos['endereco']['receber']) ?>
          </span>
        </div>
      <?php endif; ?>
      <?php if ($STORE['config']['endereco']['endereco']['status'] == true) : ?>
        <div class="pull-left">
          <label class="pull-left mb5 ft15px">Endereço</label>
          <span class="input-falsos mb15">
            <i class="fa fa-envelope-o"></i>
            <input type="text" autocomplete="off" name="endereco[endereco]" value="<?php echo is_post_value($endereco['endereco']) ?>" id="endereco" data-input="endereco" />
            <?php echo tigger_error($ErrorCheckoutEditarEnderecos['endereco']['endereco']) ?>
          </span>
        </div>
      <?php endif; ?>
      <div class="row pull-left">
        <?php if ($STORE['config']['endereco']['numero']['status'] == true) : ?>
          <div class="col-md-5 col-sm-5">
            <label class="pull-left mb5 ft15px">Número</label>
            <span class="input-falsos mb15">
              <input type="text" autocomplete="off" name="endereco[numero]" value="<?php echo is_post_value($endereco['numero']) ?>" id="numero" />
              <?php echo tigger_error($ErrorCheckoutEditarEnderecos['endereco']['numero']) ?>
            </span>
          </div>
        <?php endif; ?>
        <?php if ($STORE['config']['endereco']['bairro']['status'] == true) : ?>
          <div class="col-md-7 col-sm-7">
            <label class="pull-left mb5 ft15px">Bairro</label>
            <span class="input-falsos mb15">
              <input type="text" autocomplete="off" name="endereco[bairro]" value="<?php echo is_post_value($endereco['bairro']) ?>" id="bairro" data-input="bairro" />
              <?php echo tigger_error($ErrorCheckoutEditarEnderecos['endereco']['bairro']) ?>
            </span>
          </div>
        <?php endif; ?>
        <?php if ($STORE['config']['endereco']['complemento']['status'] == true) : ?>
          <div class="col-md-12 col-sm-12">
            <label class="pull-left mb5 ft15px">Complemento</label>
            <small>Ex: (Apto. casa, empresa)</small>
            <span class="input-falsos mb15">
              <input type="text" autocomplete="off" name="endereco[complemento]" value="<?php echo is_post_value($endereco['complemento']) ?>" id="complemento" />
              <?php echo tigger_error($ErrorCheckoutEditarEnderecos['endereco']['complemento']) ?>
            </span>
          </div>
        <?php endif; ?>
        <?php if ($STORE['config']['endereco']['referencia']['status'] == true) : ?>
          <div class="col-md-12 col-sm-12">
            <label class="pull-left mb5 ft15px">Referências</label>
            <span class="input-falsos mb15">
              <input type="text" autocomplete="off" name="endereco[referencia]" value="<?php echo is_post_value($endereco['referencia']) ?>" id="referencia" />
              <?php echo tigger_error($ErrorCheckoutEditarEnderecos['endereco']['referencia']) ?>
            </span>
          </div>
        <?php endif; ?>
        <?php if ($STORE['config']['endereco']['cidade']['status'] == true) : ?>
          <div class="col-md-9 col-sm-8">
            <label class="pull-left mb5 ft15px">Cidade</label>
            <span class="input-falsos mb15">
              <input type="text" autocomplete="off" name="endereco[cidade]" value="<?php echo is_post_value($endereco['cidade']) ?>" data-input="cidade" id="cidade" />
              <?php echo tigger_error($ErrorCheckoutEditarEnderecos['endereco']['cidade']) ?>
            </span>
          </div>
        <?php endif; ?>
        <?php if ($STORE['config']['endereco']['uf']['status'] == true) : ?>
          <div class="col-md-3 col-sm-4">
            <label class="pull-left mb5 ft15px">UF</label>
            <span class="input-falsos mb15">
              <input type="text" autocomplete="off" name="endereco[uf]" value="<?php echo is_post_value($endereco['uf']) ?>" data-input="uf" id="uf" />
              <?php echo tigger_error($ErrorCheckoutEditarEnderecos['endereco']['uf']) ?>
            </span>
          </div>
        <?php endif; ?>
        <?php if ($STORE['config']['endereco']['nome']['status'] == true) : ?>
          <div class="col-md-7 col-sm-7 mb15">
            <label class="pull-left mb5 ft15px">
              De um nome para seu endereço <small class="pull-left mb5">(Ex: Meu serviço, Minha casa)</small>
            </label>
            <span class="input-falsos">
              <input type="text" autocomplete="off" name="endereco[nome]" value="<?php echo is_post_value($endereco['nome']) ?>" id="nome" />
              <?php echo tigger_error($ErrorCheckoutEditarEnderecos['endereco']['nome']) ?>
            </span>
          </div>
        <?php endif; ?>
      </div>
      <button type="submit" data-type="submit" class="btn btn-block pull-left btn-primary btn-lg">
        salvar dados
      </button>
      <!--[ END EDITAR OU CADASTRAR UM NOVO ENDERECO ]-->
    <?php endif; ?>
  </div>
  <error id="error" style="display: none; visibility: visible;">
    <?php echo tigger_error((is_array($ErrorCheckoutEditarEnderecos['endereco']) ? current($ErrorCheckoutEditarEnderecos['endereco']) : false)); ?>
    <?php
    //        if( is_array( $ErrorCheckoutEditarEnderecos['endereco'] ) ) {
    //            foreach( $ErrorCheckoutEditarEnderecos['endereco'] as $k => $text  ) {
    //                $text = preg_replace_callback('/<script>.*?<\/script>/sim',
    //                        function($match) {
    //                            preg_match('/.*?/sim', $match[0], $h3);
    //                            return $h3[0];
    //                }, $text);
    //                echo $text;
    //            }
    //        }
    ?>
  </error>
</form>
