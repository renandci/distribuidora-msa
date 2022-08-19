<?php
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
$ClientIdExplode = explode('&cliente_id=', base64_decode($token));
$ClientId = end($ClientIdExplode);

if (Clientes::count(['conditions' =>  ['sha1(id)=?', $ClientId]])) {
  $Clientes = Clientes::first(['conditions' =>  ['sha1(id)=?', $ClientId]]);
?>
  <div class="col-lg-7 col-lg-offset-2 col-md-7 col-md-offset-2 col-sm-7 col-sm-offset-2 col-xs-12">
    <ul>
      <li class="clearfix mt15 mb25">
        <h2>Alterar Senha</h2>
        <p>Você é <b><?php echo $Clientes->nome; ?></b></p>
        <p>Seu e-mail de acesso é: <b><?php echo $Clientes->email; ?></b></p>
        <span class="show ft13px">Campos com (*) são obrigatórios</span>
        <hr />
        <form id="mudar-senha" method="post" action="">
          <input autocomplete="off" type='hidden' name="token" value="<?php echo md5($Clientes->id); ?>" />
          <div class="div-centro-form-cadastro-cliente">
            <div class="clearfix mt15">
              <span class="pull-left tag-span-input">Nova Senha *</span>
              <span class="pull-left">
                <input autocomplete="off" type="password" name="senha1" id="senha1" class="input-cadastro-cliente model-border model-radius" />
              </span>
            </div>

            <div class="clearfix mt15">
              <span class="pull-left tag-span-input">Confirmar Senha *</span>
              <span class="pull-left">
                <input autocomplete="off" type="password" name="senha2" id="senha2" class="input-cadastro-cliente model-border model-radius" />
              </span>
            </div>
          </div>
          <center><button type="submit" class="mt20 btn btn-success">ALTERAR SENHA</button></center>
          <input autocomplete="off" type="hidden" name="acao" value="redefinirSenhaUsusario" />
        </form>
      </li>
    </ul>
  </div>
  <?php ob_start(); ?>
  <script>
    $("#mudar-senha").validate({
      debug: true,
      errorClass: 'error-cadastro',
      rules: {
        senhaantiga: {
          required: true
        },
        senha1: {
          required: true,
          minlength: 6,
          maxlength: 12
        },
        senha2: {
          required: true,
          minlength: 6,
          maxlength: 12,
          equalTo: '#senha1'
        }
      },
      messages: {
        senhaantiga: {
          required: 'Digite sua senha antiga'
        },
        senha1: {
          required: 'Digite uma senha',
          minlength: 'Senha muito curta',
          maxlength: 'Senha muito longa'
        },
        senha2: {
          required: 'Confirme sua senha',
          minlength: 'Senha muito curta',
          maxlength: 'Senha muito longa',
          equalTo: 'As senha não conferem'
        }
      },
      submitHandler: function(form) {
        var ModalSite = $("#modal-site"),
          formData = $("#mudar-senha").serialize();
        $.ajax({
          url: window.location.href,
          type: 'post',
          data: formData,
          dataType: 'json',
          error: function(X, T, M) {
            if (T === 'timeout') {
              alert('Opss algo falhou tente novamente');
            } else {
              console.log(T + '\n' + M);
            }
          },
          beforeSend: function() {
            ModalSite.modal("show").find("p").html('Aguarde...');
          },
          success: function(str) {
            ModalSite.find("p").html(str.msg);
            $(str.campo).addClass('error');
          }
        });
      }
    });
  </script>
  <?php $str['script_manual'] .= ob_get_clean(); ?>
<?php } else { ?>
  <h2 class="text-center">Acesso negado/Você não é nosso usuário!</h2>
<?php } ?>
