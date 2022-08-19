<?php
$r = null;

if (!empty($GET['id']) && (int)$GET['id'] > 0) {
  $id = $GET['id'];
  $r = current(array_filter(
    $CONFIG['cliente_session']['enderecos'],
    function ($value) use ($id) {
      return ($value['id'] == $id);
    }
  ));
}
?>
<div id="modal-content">
  <h3>Novo endereço</h3>
  <small class="show ft10px black-40">CAMPOS COM (*) SÃO OBRIGATÓRIOS</small>
  <form action="<?php echo URL_BASE ?>identificacao/identificacao-endereco-cadastrar_editar/?_u=<?php echo (!empty($GET['_u']) ? $GET['_u'] : '/identificacao/meus-enderecos') ?>" class="form-horizontal" id="endereco-novo" method="post">
    <input type="hidden" name="acao" value="<?php echo sha1('NewCheckoutEnderecos'); ?>" />
    <input type="hidden" name="endereco[hashendereco]" value="<?php echo isset($GET['id']) && $GET['id'] != '' ? sha1($GET['id']) : '' ?>" />
    <input type="hidden" name="endereco[hashcliente]" value="<?php echo isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != '' ? $_SESSION['cliente']['id_cliente'] : '' ?>" />

    <?php if ($STORE['config']['endereco']['cep']['status'] == true) : ?>
      <div class="form-group form-group-lg">
        <label for="cep" class="col-sm-4 control-label">
          <?php echo $STORE['config']['endereco']['cep']['text'] ?>
        </label>
        <div class="col-sm-3">
          <input autocomplete="off" type="tel" name="endereco[cep]" class="form-control" value="<?php echo $r['cep'] ?>" id="cep" input-mask="cep" />
          <?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['endereco']['cep']) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($STORE['config']['endereco']['nome']['status'] == true) : ?>
      <div class="form-group form-group-lg">
        <label class="col-sm-4 control-label" for="nomeendereco">
          <?php echo $STORE['config']['endereco']['nome']['text'] ?>
        </label>
        <div class="col-sm-5">
          <input autocomplete="off" type="text" name="endereco[nome]" class="form-control" value="<?php echo $r['nome'] ?>" id="nomeendereco" />
          <?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['endereco']['nome']) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($STORE['config']['endereco']['receber']['status'] == true) : ?>
      <div class="form-group form-group-lg">
        <label class="col-sm-4 control-label" for="receber">
          <?php echo $STORE['config']['endereco']['receber']['text'] ?>
        </label>
        <div class="col-sm-5">
          <input autocomplete="off" type="text" name="endereco[receber]" class="form-control" value="<?php echo $r['receber'] ?>" id="receber" />
          <?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['endereco']['receber']) ?>
          <!--<font class="pull-left ft13px" style="width: 100%;">Ex: casa, trabalho, apto</font>-->
        </div>
      </div>
    <?php endif; ?>

    <?php if ($STORE['config']['endereco']['endereco']['status'] == true) : ?>
      <div class="form-group form-group-lg">
        <label class="col-sm-4 control-label" for="endereco">
          <?php echo $STORE['config']['endereco']['endereco']['text'] ?>
        </label>
        <div class="col-sm-7">
          <input autocomplete="off" type="text" name="endereco[endereco]" class="form-control" value="<?php echo $r['endereco'] ?>" id="endereco" />
          <?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['endereco']['endereco']) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($STORE['config']['endereco']['numero']['status'] == true) : ?>
      <div class="form-group form-group-lg">
        <label class="col-sm-4 control-label" for="numero">
          <?php echo $STORE['config']['endereco']['numero']['text'] ?>
        </label>
        <div class="col-sm-3">
          <input autocomplete="off" type="tel" name="endereco[numero]" class="form-control" value="<?php echo $r['numero'] ?>" id="numero" />
          <?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['endereco']['numero']) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($STORE['config']['endereco']['bairro']['status'] == true) : ?>
      <div class="form-group form-group-lg">
        <label class="col-sm-4 control-label" for="bairro">
          <?php echo $STORE['config']['endereco']['bairro']['text'] ?>
        </label>
        <div class="col-sm-5">
          <input autocomplete="off" type="text" name="endereco[bairro]" class="form-control" value="<?php echo $r['bairro'] ?>" id="bairro" />
          <?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['endereco']['bairro']) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($STORE['config']['endereco']['complemento']['status'] == true) : ?>
      <div class="form-group form-group-lg">
        <label class="col-sm-4 control-label" for="complemento">
          <?php echo $STORE['config']['endereco']['complemento']['text'] ?>
        </label>
        <div class="col-sm-6">
          <input autocomplete="off" type="text" name="endereco[complemento]" class="form-control" value="<?php echo $r['complemento'] ?>" id="complemento" />
          <?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['endereco']['complemento']) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($STORE['config']['endereco']['referencia']['status'] == true) : ?>
      <div class="form-group form-group-lg">
        <label class="col-sm-4 control-label" for="referencia">
          <?php echo $STORE['config']['endereco']['referencia']['text'] ?>
        </label>
        <div class="col-sm-7">
          <input autocomplete="off" type="text" name="endereco[referencia]" class="form-control" value="<?php echo $r['referencia'] ?>" id="referencia" />
          <?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['endereco']['referencia']) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($STORE['config']['endereco']['cidade']['status'] == true) : ?>
      <div class="form-group form-group-lg">
        <label class="col-sm-4 control-label" for="cidade">
          <?php echo $STORE['config']['endereco']['cidade']['text'] ?>
        </label>
        <div class="col-sm-5">
          <input autocomplete="off" type="text" name="endereco[cidade]" class="form-control" value="<?php echo $r['cidade'] ?>" id="cidade" data-input="cidade" />
          <?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['endereco']['cidade']) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($STORE['config']['endereco']['uf']['status'] == true) : ?>
      <div class="form-group form-group-lg">
        <label class="col-sm-4 control-label" for="uf">
          <?php echo $STORE['config']['endereco']['uf']['text'] ?>
        </label>
        <div class="col-sm-2">
          <input autocomplete="off" type="text" name="endereco[uf]" class="form-control" value="<?php echo $r['uf'] ?>" size="2" maxlength="2" data-input="uf" id="uf" />
          <?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['endereco']['uf']) ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="clearfix line">
      <div class="controls ">
        <button type="submit" class="btn btn-large btn-primary btn-send-form-address" data-type=submit>Cadastrar endereço</button>
      </div>
    </div>
    <error id="error" style="display: none; visibility: visible;">
      <?php echo tigger_error((is_array($ErrorCheckoutEditarEnderecos['cadastro']) ? current($ErrorCheckoutEditarEnderecos['cadastro']) : false)); ?>
    </error>
    <script>
      <?php ob_start(); ?>
      $(function() {
        $("input[name='endereco[cep]']").mask("00000-000", {
          onComplete: busca_cidade
        });
        /**
         * Validar e cadastrar clitente
         */
        $("#modal-site").on("click", "button[data-type=submit]", function(e) {
          /**
           * Pega o formulario que está em AcaoEnderecos
           */
          var FomrValidate = $(e.target).parent().parent().parent();

          $(FomrValidate).validate({
            debug: true,
            errorClass: "input-error-span-2 show ft12px",
            errorElement: "font",
            rules: {
              "endereco[cep]": {
                required: true
              },
              "endereco[endereco]": {
                required: true
              },
              "endereco[numero]": {
                required: true,
                number: true
              },
              "endereco[bairro]": {
                required: true
              },
              "endereco[cidade]": {
                required: true
              },
              "endereco[uf]": {
                required: true
              },
              "endereco[nome]": {
                required: true
              }
            },
            messages: {
              "endereco[cep]": {
                required: "Digite seu CEP!"
              },
              "endereco[endereco]": {
                required: "Digite seu endereço!"
              },
              "endereco[bairro]": {
                required: "Digite o bairro!"
              },
              "endereco[numero]": {
                required: "Digite o número!",
                number: "Digite apenas números"
              },
              "endereco[cidade]": {
                required: "Digite nome da sua cidade!"
              },
              "endereco[uf]": {
                required: "Digite seu estado!"
              },
              "endereco[nome]": {
                required: "Dê um nome para seu endereço!"
              }
            },
            highlight: function(element, errorClass, validClass) {
              $(element).parent().addClass("new-checkout-error").removeClass("new-checkout-ok");
            },
            unhighlight: function(element, errorClass, validClass) {
              $(element).parent().removeClass("new-checkout-error").addClass("new-checkout-ok");
            },
            submitHandler: function(form, validator) {
              var FormData = $(validator.target).find('input[name]').serialize(),
                FormAction = $(validator.target).attr("action"),
                FormById = $(validator.target).attr("id"),
                DivAbsolute = $(".div-absoluta"),
                DivError = $(".cx-error");

              $.ajax({
                url: FormAction,
                type: "post",
                data: FormData,
                cache: false,
                success: function(str) {
                  var list = $("<div/>", {
                    html: str
                  });
                  $("#" + FormById).html(list.find("#" + FormById).html());
                },
                complete: function(a) {
                  var list = $("<div/>", {
                      html: a.responseText
                    }),
                    ErrorCadastro = list.find("#error").find("span");

                  if (ErrorCadastro.length > 0) {
                    $("#form-cadastro-cliente").html(list.find("#form-cadastro-cliente").html());
                    DivAbsolute.fadeIn(0).next(DivError).fadeIn(0).find("p").html(ErrorCadastro.html());
                    return false;
                  }
                  window.location.href = "<?php echo !empty($GET['_u']) ? $GET['_u'] : '/identificacao/meus-enderecos' ?>";
                },
                error: function(E1, E2, E3) {
                  console.log(E1.responseText + "\n" + E2 + "\n" + E3);
                }
              });
            }
          });
        });
      });
      <?php
      $JSqueeze = new Patchwork\JSqueeze();
      echo $JSqueeze->squeeze(ob_get_clean(), true, false, false);
      ?>
    </script>
  </form>
</div>
