<?php
//session_unset();
//session_destroy();
if (
  isset($_SESSION['cliente']['id_cliente']) &&
  $_SESSION['cliente']['id_cliente'] != '' &&
  empty($MensagemNovoCheckoutLogin)
) {
  $u = filter_input(INPUT_GET, '_u', FILTER_SANITIZE_STRING);
  header('location: ' . $u);
  return;
}
$is_mail = isset($_SESSION['cliente']['email']) && $_SESSION['cliente']['email'] != '' ? $_SESSION['cliente']['email'] : null;

?>
<div class="row" id="new-ckeckout">
  <div class="col-md-10 col-md-offset-1 col-xs-12 mt50 mb50">
    <form action="/identificacao/login/?_u=<?php echo $GET['_u'] ?>" method="post" id="new-ckeckout-login">
      <input type="hidden" name="acao" value="NewCheckoutLogin">
      <div class="row">
        <?php if ($MobileDetect->isMobile() || $MobileDetect->isTablet()) { ?>
          <div class="col-md-5 col-xs-12">
            <h3>Primeira compra no site <?php echo $CONFIG['nome_fantasia'] ?>?</h3>
            <div class="clearfix text-center">
              <a class="btn btn-primary btn-block btn-lg" href="/identificacao/cadastre-se/?_u=<?php echo $GET['_u'] ?>&_atacadista=<?php echo $GET['_atacadista'] ?>">
                cadastre-se !
              </a>
            </div>
          </div>
          <div class="col-md-2 col-xs-12 tag_divisor"><span></span></div>
          <div class="col-md-5 col-xs-12">
            <h3>Já sou Cliente <?php echo $CONFIG['nome_fantasia'] ?>!</h3>
            <div class="form-group has-feedback-inverter has-feedback<?php echo (!empty($MensagemNovoCheckoutLoginEmail) ? ' has-error' : '') ?>">
              <label class="control-label">Informe seu <b>E-MAIL</b></label>
              <input type="email" name="email" autocomplete="off" class="form-control input-lg" value="<?php echo $is_mail ?>" />
              <span class="fa fa-envelope form-control-feedback" aria-hidden="true"></span>
              <?php echo (!empty($MensagemNovoCheckoutLoginEmail) ? $MensagemNovoCheckoutLoginEmail : '') ?>
            </div>
            <div class="form-group has-feedback-inverter has-feedback<?php echo (!empty($MensagemNovoCheckoutLoginSenha) ? ' has-error' : ''); ?>">
              <label class="control-label">Informe sua <b>SENHA</b></label>
              <input type="password" name="senha" autocomplete="off" class="form-control input-lg" />
              <span class="fa fa-lock form-control-feedback" aria-hidden="true"></span>
              <span class="fa fa-eye form-control-feedback" style="cursor: pointer; right: 0; left: auto; width: 46px; height: 46px; line-height: 46px; pointer-events: inherit" aria-pass="true"></span>
              <?php echo !empty($MensagemNovoCheckoutLoginSenha) ? $MensagemNovoCheckoutLoginSenha : ''; ?>
            </div>
            <div class="clearfix">
              <button type="submit" class="btn btn-primary btn-lg pull-right">
                <i class="fa fa-lock"></i>
                continuar
              </button>
              <a class="mt15 mr15 pull-right" href="javascript: void(0);" data-toggle="modal" data-target="#modal-senha">
                Esqueci minha senha!
              </a>
            </div>
          </div>

        <?php } else { ?>
          <div class="col-md-5 col-xs-12">
            <h3>Já sou Cliente <?php echo $CONFIG['nome_fantasia'] ?>!</h3>
            <div class="form-group has-feedback-inverter has-feedback<?php echo (!empty($MensagemNovoCheckoutLoginEmail) ? ' has-error' : '') ?>">
              <label class="control-label">Informe seu <b>E-MAIL</b></label>
              <input type="email" name="email" autocomplete="off" class="form-control input-lg" value="<?php echo $is_mail ?>" />
              <span class="fa fa-envelope form-control-feedback" aria-hidden="true"></span>
              <?php echo (!empty($MensagemNovoCheckoutLoginEmail) ? $MensagemNovoCheckoutLoginEmail : '') ?>
            </div>
            <div class="form-group has-feedback-inverter has-feedback<?php echo (!empty($MensagemNovoCheckoutLoginSenha) ? ' has-error' : ''); ?>">
              <label class="control-label">Informe sua <b>SENHA</b></label>
              <input type="password" name="senha" autocomplete="off" class="form-control input-lg" />
              <span class="fa fa-lock form-control-feedback" aria-hidden="true"></span>
              <span class="fa fa-eye form-control-feedback" style="cursor: pointer; right: 0; left: auto; width: 46px; height: 46px; line-height: 46px; pointer-events: inherit" aria-pass="true"></span>
              <?php echo !empty($MensagemNovoCheckoutLoginSenha) ? $MensagemNovoCheckoutLoginSenha : ''; ?>
            </div>
            <div class="clearfix">
              <button type="submit" class="btn btn-primary btn-lg pull-right">
                <i class="fa fa-lock"></i>
                continuar
              </button>
              <a class="mt15 mr15 pull-right" href="javascript: void(0);" data-toggle="modal" data-target="#modal-senha">
                Esqueci minha senha!
              </a>
            </div>
          </div>
          <div class="col-md-2 col-xs-12 tag_divisor"><span></span></div>
          <div class="col-md-5 col-xs-12">
            <h3>Primeira compra no site <?php echo $CONFIG['nome_fantasia'] ?>?</h3>
            <div class="clearfix text-center">
              <a class="btn btn-primary btn-block btn-lg" href="/identificacao/cadastre-se/?_u=<?php echo $GET['_u'] ?>&_atacadista=<?php echo $GET['_atacadista'] ?>">
                cadastre-se !
              </a>
            </div>
          </div>
        <?php } ?>
      </div>
      <?php echo (!empty($MensagemNovoCheckoutLogin) ? $MensagemNovoCheckoutLogin : ''); ?>
    </form>
  </div>
</div>

<!--[ MODAL SENHA ]-->
<form class="modal fade" id="modal-senha" tabindex="-1" role="dialog" aria-labelledby="modal-senha">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modal-senha">ESQUECEU A SENHA</h4>
      </div>
      <div class="modal-body clearfix" id="modal-body">
        <div class="row mt15">
          <div class="col-md-8 col-sm-8">
            <div class="form-group has-feedback-inverter has-feedback<?php echo (!empty($MensagemNovoCheckoutLoginSenha) ? ' has-error' : ''); ?>">
              <input type="email" name="email" autocomplete="off" class="form-control input-lg" placeholder="Digite seu e-mail" />
              <span class="fa fa-envelope form-control-feedback" aria-hidden="true"></span>
            </div>
          </div>
          <div class="col-md-4 col-sm-4">
            <button type="submit" class="btn btn-primary btn-block btn-lg">redefinir senha</button>
          </div>
        </div>
        <input autocomplete="off" name="acao" value="RedefinirSenha" type="hidden" />
      </div>
      <div class="modal-footer text-center">
        <p>Informe seu e-mail de cadastro para recuperar sua senha!</p>
      </div>
    </div>
  </div>
</form>
<!--[ END MODAL SENHA ]-->
<?php unset($_SESSION['cliente']['email']); ?>

<?php ob_start(); ?>
<script>
  console.clear();


  // Click event of the showPassword button
  $("#new-ckeckout-login").on("click", "[aria-pass=true]", function(e) {

    $(this).toggleClass("fa-eye-slash");

    // Get the password field
    var passwordField = $("input[name=senha]");

    // Get the current type of the password field will be password or text
    var passwordFieldType = passwordField.attr("type");

    // Check to see if the type is a password field
    if (passwordFieldType == "password")
      // Change the password field to text
      passwordField.attr("type", "text");

    else
      // If the password field type is not a password field then set it to password
      passwordField.attr("type", "password");
  });


  $("#modal-senha").validate({
    errorClass: "ft12px text-danger",
    errorElement: "span",
    rules: {
      email: {
        required: true,
        minlength: 5,
        email: true
      }
    },
    messages: {
      email: {
        required: "Digite o seu e-mail",
        minlength: "Preencha este campo",
        email: "Digite um e-mail válido"
      }
    },
    highlight: function(element, errorClass, validClass) {
      $(element).parent().parent().addClass("has-error");
    },
    unhighlight: function(element, errorClass, validClass) {
      $(element).parent().parent().addClass("has-error");
    },
    errorPlacement: function(error, element) {
      error.insertAfter(element.next('span'));
    },
    submitHandler: function(form) {
      var formSenha = $("#modal-senha"),
        formData = formSenha.serializeArray();
      $.ajax({
        url: window.location.href,
        type: "post",
        data: formData,
        dataType: "json",
        error: function(a, b, c) {
          console.log(a.responseText + "\n" + b + "\n" + c);
        },
        beforeSend: function() {
          $("#modal-body").find("div:first").fadeOut(10);
          $("#modal-body").append([
            $("<p/>", {
              html: "Aguarde um momento...",
              class: "remove-p"
            })
          ]);
        },
        success: function(str) {
          if ($("#modal-body").find("p.remove-p").length > 0)
            $("#modal-body").find("p.remove-p").remove();

          $("#modal-body").append([
            $("<p/>", {
              html: str.msg,
              class: "remove-p"
            })
          ]);
        },
        complete: function() {
          $("#modal-body").delay(2800).queue(function(e) {
            $("#modal-senha").find("button.close").trigger("click");
            $(this).find("div:first").fadeIn(0);
            $(this).find(".remove-p").remove();
            e();
          });
        }
      });
    }
  });

  //		$("#new-ckeckout-login").on("click", "#sair", function(e){
  //			e.preventDefault();
  //			$.ajax({
  //				url: window.location.href,
  //				type: "post",
  //				data: { acao: "CheckoutLogOut" },
  //				success: function( str ) {
  //					var list = $("<div/>", { html: str });
  //					$("#new-ckeckout-login").html( list.find("#new-ckeckout-login").html() );
  //				},
  //				error: function( E1, E2, E3 ){
  //					console.log( E1.responseText+"\n"+E2+"\n"+E3 );
  //				}
  //			});
  //		});

  $("#new-ckeckout-login").validate({
    debug: true,
    errorClass: "ft12px text-danger",
    errorElement: "span",
    rules: {
      email: {
        required: true,
        minlength: 2,
        email: true
      },
      senha: {
        required: true,
        minlength: 6,
        maxlength: 12
      }
    },
    messages: {
      email: {
        required: "Digite o seu e-mail",
        minlength: "Preencha este campo",
        email: "Digite um e-mail válido"
      },
      senha: {
        required: "Digite sua senha",
        minlength: "Senha muito curta",
        maxlength: "Senha muito longa"
      }
    },
    highlight: function(element, errorClass, validClass) {
      $(element).parent().addClass("has-error");
    },
    unhighlight: function(element, errorClass, validClass) {
      $(element).parent().removeClass("has-error");
    },
    errorPlacement: function(error, element) {
      error.insertAfter(element.next('span'));
    },
    submitHandler: function(form) {
      var formData = $("#new-ckeckout-login").find("input[name], select[name]").serialize();
      $.ajax({
        // url: "/identificacao/login?url=<?php echo $GET_URL; ?>&pedido=<?php echo $GET['pedido']; ?>",
        url: window.location.href,
        type: "post",
        data: formData,
        // complete: function(){},
        success: function(str) {
          var list = $("<div/>", {
            html: str
          });
          $("#new-ckeckout-login").html(list.find("#new-ckeckout-login").html());
        },
        error: function(E1, E2, E3) {
          console.log(E1.responseText + "\n" + E2 + "\n" + E3);
        }
      });
    }
  });
</script>
<?php
$str['script_manual'] .= ob_get_clean();
