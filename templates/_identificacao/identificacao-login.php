	<div class="row" id="new-ckeckout" class="new-ckeckout-login">
	  <div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12 mt50 mb50">
	    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" class="new-checkout" method="post" id="new-ckeckout-login">
	      <h4>Informe seu <b>E-MAIL</b> para prosseguir com sua compra</h4>
	      <div class="clearfix">
	        <span class="input-falsos">
	          <i class="fa fa-envelope"></i>
	          <input type="email" autocomplete="off" name="email" class="" placeholder="Digite seu e-mail para continuar" />
	          <?php echo !empty($MensagemNovoCheckoutLogin) ? $MensagemNovoCheckoutLogin : ''; ?>
	        </span>
	      </div>
	      <?php if (isset($_SESSION['cliente']['email']) && $_SESSION['cliente']['email'] != '') { ?>
	        <?php echo '<span class="ft13px">' . (isset($_SESSION['cliente']['nome']) ? $_SESSION['cliente']['nome'] : '') . '</span>' ?>
	        <div class="clearfix">
	          <input type="hidden" autocomplete="off" name="email" value="<?php echo (isset($_SESSION['cliente']['email']) ? $_SESSION['cliente']['email'] : '') ?>" />
	          <span class="input-falsos">
	            <i class="fa fa-envelope"></i>
	            <input type="password" autocomplete="off" name="senha" class="" placeholder="Digite sua senha de acesso" />
	            <?php echo !empty($MensagemNovoCheckoutLogin) ? $MensagemNovoCheckoutLogin : ''; ?>
	          </span>
	        </div>
	      <?php } ?>
	      <button type="submit" class="btn btn-primary mt5">
	        <i class="fa fa-lock"></i>
	        continuar
	      </button>
	      <input type="hidden" name="acao" value="<?php echo md5("NovoCheckoutLogin") ?>">
	    </form>
	  </div>
	</div>

	<?php ob_start(); ?>
	<script>
	  //		$(function(){
	  //			$("#new-ckeckout-login").on("click", "#sair", function(e){
	  //				e.preventDefault();
	  //				$.ajax({
	  //					url: window.location.href,
	  //					type: "post",
	  //					data: { acao: "CheckoutLogOut" },
	  //					success: function( str ) {
	  //						var list = $("<div/>", { html: str });
	  //						$("#new-ckeckout-login").html( list.find("#new-ckeckout-login").html() );
	  //					},
	  //					error: function( E1, E2, E3 ){
	  //						console.log( E1.responseText+"\n"+E2+"\n"+E3 );
	  //					}
	  //				});
	  //			});

	  $("#new-ckeckout-login").validate({
	    debug: true,
	    errorClass: "input-error-span-2 text-right",
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
	      $(element).parent().parent().addClass("new-checkout-error").removeClass("new-checkout-ok");
	    },
	    unhighlight: function(element, errorClass, validClass) {
	      $(element).parent().parent().removeClass("new-checkout-error").addClass("new-checkout-ok");
	    },
	    submitHandler: function(form) {
	      var divabsoluta = $(".div-absoluta"),
	        diverror = $(".cx-error"),
	        formData = $("#new-ckeckout-login").serialize();
	      console.log(formData);
	      $.ajax({
	        url: "/identificacao/login?url=<?php echo $GET_URL; ?>&pedido=<?php echo $GET["pedido"]; ?>",
	        type: "post",
	        data: formData,
	        success: function(str) {
	          console.log(str);
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

	  $("input[type=email]")
	    .focus(function() {
	      $(this).parent().addClass("border-in");
	    })
	    .blur(function() {
	      $(this).parent().removeClass("border-in");
	    });
	  // });
	</script>
	<?php $str['script_manual'] .= ob_get_clean(); ?>

	<!--
	<h1 class="clearfix mt45"></h1>

	<div style='background-color: #fff; border: solid thin #ddd;' class="w400px center-block clearfix mobile-100 <?php echo (isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != '') ? 'show' : 'hidden'; ?>">
		<h4 class="font-bold clearfix" style="background-color: #d0f5d7; margin:0">
			<span class='ml5 mb15 mt15 show'>IDENTIFIQUE-SE</span>
		</h4>
		<div class="ml20 mr20 mb20 mt20 clearfix">
			<h4 class="font-bold">Você é <?php echo $_SESSION['cliente']['nome'] ?></h4>
			<div class='text-center'>
				<a class="btn btn-primary" href="<?php echo URL_BASE ?>identificacao/minha-compra"> SIM </a>
				<a class="ml15 btn btn-primary" href="identificacao/sair/minha-compra"> NÃO É VOCÊ </a>
			</div>
		</div>
	</div>

	<form class="form-login pull-left form-login-usuario <?php echo (isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != '') ? 'hidden' : 'show'; ?>" action="login" id="form-login-usuario">
		<div class="form-centro">
			<h3 class="font-bold">JÁ SOU CLIENTE</h3>
			<p>Use sua conta para entrar:</p>
			<span class="span-input-falsos show clearfix">
				<i class="fa fa-envelope pull-left"></i>
				<input autocomplete="off" type="email" name="usuario" class="pull-left model-border model-radius" placeholder="E-mail" value=""/>
			</span>
			<span class="span-input-falsos show clearfix mt25">
				<i class="fa fa-lock pull-left"></i>
				<input autocomplete="off" type="password" name="senha" class="pull-left model-border model-radius" placeholder="Senha" value=""/>
			</span>
			<p class="mt15 mb5 text-centro"><a href="javascript://" class="minha-senha color-001 font-bold" onclick="$('.div-absoluta,.form-senha').fadeIn();">Esqueci minha senha!</a></p>
			<button type="submit" class="btn btn-large btn-primary"> LOGAR </button>
			<input autocomplete="off" name="acao" value="FazerLogin" type="hidden"/>
		</div>
	</form>

	<form class="form-login pull-right from-criar-usuario <?php echo (isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != "") ? "hidden" : "show"; ?>" action="login-minha-senha" id="from-criar-usuario">
		<div class="form-centro">
			<h3 class="font-bold">MINHA PRIMEIRA COMPRA</h3>
			<p>Informe seu e-mail e cep:</p>
			<span class="span-input-falsos show clearfix">
				<i class="fa fa-envelope pull-left"></i>
				<input autocomplete="off" type="email" name="email" class="pull-left model-border model-radius" placeholder="Digite seu e-mail" value=""/>
			</span>
			<span class="span-input-falsos show clearfix mt25">
				<i class="fa fa-map-marker pull-left"></i> <input autocomplete="off" type="text" name="meucep" class="pull-left model-border model-radius" placeholder="Digite seu Cep" value="" maxlength="11"/>
			</span>
			<p class="mt15 mb5"><a href="http://www.buscacep.correios.com.br/" TARGET="_blank" class="a-minha-senha color-001 font-bold">Não sei meu cep</a></p>
			<button type="submit" class="btn btn-large btn-primary"> CONTINUAR </button>
			<input autocomplete="off" name="acao" value="VerificarCadastro" type="hidden"/>
		</div>
	</form>

	<form class="form-senha clearfix" id="from-recuperar-senha">
		<a href="javascript://" onclick="$('.div-absoluta,.cx-error,.form-senha').fadeOut(0);" class="fa fa-close"></a>
		<div class="form-centro">
			<h4 class="font-bold">ESQUECEU A SENHA</h4>
			<p>E-mail:</p>
			<span class="span-input-falsos show clearfix mb5">
				<i class="fa fa-envelope pull-left"></i>
				<input autocomplete="off" type="email" name="email" class="pull-left model-border model-radius" placeholder="Digite seu e-mail" value=""/>
			</span>
			<center>
				<button type="submit" class="btn btn-small btn-primary"> redifinir senha </button>
			</center>
			<p>Informe seu e-mail de casdastro para recuperar sua senha!</p>
			<input autocomplete="off" name="acao" value="RedefinirSenha" type="hidden"/>
			<input autocomplete="off" id="reset" type="reset" style='display:none;'/>
		</div>
	</form>

	<script type="text/javascript">
		$(function(){
			$('input[name=meucep]').mask('99999-999');

			$('#form-login-usuario').validate({
				rules : {
					usuario : { required : true, minlength : 2, email : true },
					senha : { required : true, minlength : 6, maxlength : 12 }
				},
				messages : {
					usuario : { required : 'Digite o seu e-mail', minlength : 'Preencha este campo', email : 'Digite um e-mail válido' },
					senha : { required : 'Digite sua senha', minlength : 'Senha muito curta', maxlength : 'Senha muito longa' }
				},
				submitHandler : function( form ) {
					var divabsoluta = $('.div-absoluta'), diverror = $('.cx-error'), formData = $('#form-login-usuario').serialize();
					$.ajax({
						url : '/identificacao/login?url=<?php echo $GET_URL; ?>&pedido=<?php echo $GET['pedido']; ?>',
						type : 'post',
						data : formData,
						dataType : 'json',
						beforeSend : function() {
							divabsoluta.fadeIn(0);
							diverror.fadeIn(0).find('p').html('Aguarde...');
						},
						success : function( str ) {
							$('.cx-error p').html( str.msg );
							$( str.campo ).addClass('error');
						},
						error : function( E1, E2, E3 ){
                            console.log( E1.responseText+'\n'+E2+'\n'+E3 );
                        }
					});
				}
			});

			$('#from-criar-usuario').validate({
				rules : {
					email : { required : true, minlength : 2, email : true },
					meucep : { required : true }
				},
				messages : {
					email : { required : 'Digite seu e-mail', minlength : 'Preencha este campo', email : 'Digite um e-mail válido' },
					meucep : { required : 'Digite seu CEP' }
				},
				submitHandler : function( form ){
					var divabsoluta = $('.div-absoluta'), diverror = $('.cx-error'), formData = $('.from-criar-usuario').serialize();
					$.ajax({
						url 		: 'identificacao/cadastre-se?acao=cadastre-se&url=<?php echo $GET_URL; ?>&pedido=<?php echo $GET['pedido']; ?>',
						type 		: 'post',
						data 		: formData,
						dataType 	: 'json',
						error 		: function( E1, E2, E3 ){ console.log( E1.responseText+'\n'+E2+'\n'+E3 ); },
						beforeSend	: function() {
							divabsoluta.fadeIn(0);
							diverror.fadeIn(0).find('p').html('Aguarde...');
						},
						success 	: function( str ) {
							$('.cx-error p').html( str.msg );
							$( str.campo ).addClass('error');
						}
					});
				}
			});


			$('#from-recuperar-senha').validate({
				rules : {
					email : { required : true, minlength : 2, email : true }
				},
				messages : {
					email : { required : 'Digite o seu e-mail', minlength : 'Preencha este campo', email : 'Digite um e-mail válido' }
				},
				submitHandler : function( form ) {
					var divabsoluta = $('.div-absoluta'),
                            diverror = $('.cx-error'),
                            formSenha = $('#from-recuperar-senha'),
                            formData = formSenha.serializeArray();
					$.ajax({
						url: '/identificacao/login?acao=login&url=<?php echo $GET_URL; ?>&pedido=<?php echo $GET['pedido']; ?>',
						type : 'post',
						data : formData,
						dataType : 'json',
						error : function( E1, E2, E3 ){
                            console.log( E1.responseText+'\n'+E2+'\n'+E3 );
                        },
						beforeSend	: function() {
							divabsoluta.fadeIn(0);
							diverror.fadeIn(0).find('p').html('Aguarde...');
							formSenha.fadeOut(0);
						},
						success : function( str ) {
							diverror.fadeIn(0).find('p').html( str.msg );
							$( str.campo ).addClass('error');
						},
						complete : function(){
                            $('#reset').click();
                        }
					});
				}
			});
		});
	</script>
	-->
