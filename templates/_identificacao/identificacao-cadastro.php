<?php

/**
 * Buscar os dados do cliente
 * Todos os dados do cliente estarão disponíveis em um unico so lugar
 */
// $USER_SESSION = isset( $_SESSION['cliente']['id_cliente'] ) && $_SESSION['cliente']['id_cliente'] != '' ? Clientes::first(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]) : false;
// $rws = isset($post) && is_array($post) ? (object)$post : $USER_SESSION;
$CONFIG['cliente_session'] = isset($post) ? $post : $CONFIG['cliente_session'];
?>
<style>
	.form-horizontal .form-group-lg .control-label {
		font-size: 14px;
	}
</style>
<form class="mb30<?php echo empty($_SESSION['cliente']['id_cliente']) ? ' col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12' : ' col-md-9 col-sm-8 col-xs-12' ?>" id="form-cadastro" method="post" action="identificacao/cadastre-se/?_u=<?php echo $GET['_u'] ?>&_atacadista=<?php echo $GET['_atacadista'] ?>">

	<input type="hidden" name="cadastro[atacadista]" value="<?php echo (isset($GET['_atacadista']) && $GET['_atacadista'] !== '' ? 1 : 0) ?>" />

	<div data-id="cadastro" class="form-horizontal mb25">
		<h2 class="mb5">Dados Pessoais</h2>
		<span class="show ft13px">Campos com (*) são obrigatórios</span>
		<hr />

		<?php if ($STORE['config']['cadastro']['tipopessoa']['status'] == true) : ?>
			<div class="form-group form-group-lg">
				<div class="checkbox">
					<label class="col-sm-4 control-label">
						<input type="radio" id="tipopessoa-a" value="1" name="tipopessoa" <?php echo strlen(soNumero($CONFIG['cliente_session']['cpfcnpj'])) <= 11 ? 'checked' : '' ?> />
						<label for="tipopessoa-a" class="input-checkbox fa ft18px"></label>
						Pessoa Fisica.
					</label>

					<label class="col-sm-4 control-label">
						<input type="radio" id="tipopessoa-b" value="2" name="tipopessoa" <?php echo strlen(soNumero($CONFIG['cliente_session']['cpfcnpj'])) >= 14 ? 'checked' : '' ?> />
						<label for="tipopessoa-b" class="input-checkbox fa ft18px"></label>
						Pessoa Jurídica.
					</label>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($STORE['config']['cadastro']['email']['status'] == true) : ?>
			<div class="form-group form-group-lg">
				<label for="email" class="col-sm-4 control-label">
					<?php echo $STORE['config']['cadastro']['email']['text'] ?>
				</label>
				<div class="col-sm-7">
					<input autocomplete="off" type="email" name="cadastro[email]" class="form-control" value="<?php echo ($GET['email'] ? $GET['email'] : is_post_value($CONFIG['cliente_session']['email'])) ?>" id="email" />
					<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['cadastro']['email']) ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($STORE['config']['cadastro']['nome']['status'] == true) : ?>
			<div class="form-group form-group-lg">
				<label for="nome" class="col-sm-4 control-label" data-tipo-name="<?php echo $STORE['config']['cadastro']['nome']['text-attr'] ?>">
					<?php echo $STORE['config']['cadastro']['nome']['text'] ?>
				</label>
				<div class="col-sm-6">
					<input autocomplete="off" type="text" name="cadastro[nome]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['nome']) ?>" id="nome" />
					<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['cadastro']['nome']) ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($STORE['config']['cadastro']['cpfcnpj']['status'] == true) : ?>
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="cpfcnpj" data-tipo-cpfcnpj="<?php echo $STORE['config']['cadastro']['cpfcnpj']['text-attr'] ?>">
					<?php echo $STORE['config']['cadastro']['cpfcnpj']['text'] ?>
				</label>
				<div class="col-sm-4">
					<input autocomplete="off" type="tel" name="cadastro[cpfcnpj]" class="form-control" input-mask="cpfcnpj" id="cpfcnpj" value="<?php echo is_post_value($CONFIG['cliente_session']['cpfcnpj']) ?>" maxlength="20" />
					<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['cadastro']['cpfcnpj']) ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($STORE['config']['cadastro']['rg']['status'] == true) : ?>
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="rg" data-tipo-rgie="<?php echo $STORE['config']['cadastro']['rg']['text-attr'] ?>">
					<?php echo $STORE['config']['cadastro']['rg']['text'] ?>
				</label>
				<div class="col-sm-5">
					<input autocomplete="off" type="text" name="cadastro[rg]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['rg']) ?>" id="rg" />
					<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['cadastro']['rg']) ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($STORE['config']['cadastro']['data_nascimento']['status'] == true) : ?>
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="data_nascimento"><?php echo $STORE['config']['cadastro']['data_nascimento']['text'] ?></label>
				<div class="col-sm-4">
					<input autocomplete="off" placeholder="Exemplo: 01/01/2001"  type="tel" name="cadastro[data_nascimento]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['data_nascimento']) ?>" id="data_nascimento" input-mask="data_nascimento" />
					<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['cadastro']['data_nascimento']) ?>
				</div>
				<!-- <font class="pull-left ft12px" style="width: 100%; text-indent: 245px;">Exemplo 01 / 01 / 2001</font> -->
			</div>
		<?php endif; ?>

		<?php if ($STORE['config']['cadastro']['sexo']['status'] == true) : ?>
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="sexo">
					<?php echo $STORE['config']['cadastro']['sexo']['text'] ?>
				</label>
				<div class="col-sm-4">
					<select name="cadastro[sexo]" id="sexo" class="form-control">
						<option value="">Selecione</option>
						<option value="masculino" <?php echo ($CONFIG['cliente_session']['sexo'] == 'masculino') ? ' selected' : ''; ?>>Masculino</option>
						<option value="feminino" <?php echo ($CONFIG['cliente_session']['sexo'] == 'feminino') ? ' selected' : ''; ?>>Feminino</option>
					</select>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($STORE['config']['cadastro']['telefone']['status'] == true) : ?>
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="telefone">
					<?php echo $STORE['config']['cadastro']['telefone']['text'] ?>
				</label>
				<div class="col-sm-4">
					<input autocomplete="off" type="tel" name="cadastro[telefone]" class="form-control" value="<?php echo is_post_value(soNumero($CONFIG['cliente_session']['telefone'])) ?>" id="telefone" input-mask="telefone" />
					<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['cadastro']['telefone']) ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($STORE['config']['cadastro']['celular']['status'] == true) : ?>
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="celular">
					<?php echo $STORE['config']['cadastro']['celular']['text'] ?>
				</label>
				<div class="col-sm-4">
					<input autocomplete="off" type="tel" name="cadastro[celular]" class="form-control" value="<?php echo is_post_value(soNumero($CONFIG['cliente_session']['celular'])) ?>" id="celular" input-mask="telefone" />
					<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['cadastro']['celular']) ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($STORE['config']['cadastro']['operadora']['status'] == true) : ?>
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="operadora">
					<?php echo $STORE['config']['cadastro']['operadora']['text'] ?>
				</label>
				<div class="col-sm-5">
					<input autocomplete="off" type="text" name="cadastro[operadora]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['operadora']) ?>" id="operadora" />
					<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['cadastro']['operadora']) ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($STORE['config']['cadastro']['cidade']['status'] == true) : ?>
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="cidade">
					<?php echo $STORE['config']['cadastro']['cidade']['text'] ?>
				</label>
				<div class="col-sm-5">
					<input autocomplete="off" type="text" name="cadastro[cidade]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['cidade']) ?>" id="cidade" data-input="cidade" />
					<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['cadastro']['cidade']) ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if ($STORE['config']['cadastro']['uf']['status'] == true) : ?>
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="uf">
					<?php echo $STORE['config']['cadastro']['uf']['text'] ?>
				</label>
				<div class="col-sm-2">
					<input autocomplete="off" type="text" name="cadastro[uf]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['uf']) ?>" id="uf" data-input="uf" />
					<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['cadastro']['uf']) ?>
				</div>
			</div>
		<?php endif; ?>

	</div>

	<?php // if( $STORE['config']['endereco']['configure']['status'] == true ) { 
	?>
	<?php if ($GET_ACAO !== 'editar-cadastro') { ?>
		<!--[CADASTRO DE ENDERECO]-->
		<div class="form-horizontal mb25" data-id="endereco">
			<h2 class="mediun mb5">Endereço</h2>
			<span class="show ft13px">Campos com (*) são obrigatórios</span>
			<hr />

			<?php if ($STORE['config']['endereco']['cep']['status'] == true) : ?>
				<div class="form-group form-group-lg">
					<label for="cep" class="col-sm-4 control-label">
						<?php echo $STORE['config']['endereco']['cep']['text'] ?>
					</label>
					<div class="col-sm-3">
						<input autocomplete="off" type="tel" name="endereco[cep]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['endereco']->cep) ?>" id="cep" input-mask="cep" />
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
						<input autocomplete="off" type="text" name="endereco[nome]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['endereco']->nome) ?>" id="nomeendereco" />
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
						<input autocomplete="off" type="text" name="endereco[receber]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['endereco']->receber) ?>" id="receber" />
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
						<input autocomplete="off" type="text" name="endereco[endereco]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['endereco']->endereco) ?>" id="endereco" />
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
						<input autocomplete="off" type="tel" name="endereco[numero]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['endereco']->numero) ?>" id="numero" />
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
						<input autocomplete="off" type="text" name="endereco[bairro]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['endereco']->bairro) ?>" id="bairro" />
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
						<input autocomplete="off" type="text" name="endereco[complemento]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['endereco']->complemento) ?>" id="complemento" />
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
						<input autocomplete="off" type="text" name="endereco[referencia]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['endereco']->referencia) ?>" id="referencia" />
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
						<input autocomplete="off" type="text" name="endereco[cidade]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['endereco']->cidade) ?>" id="cidade" data-input="cidade" />
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
						<!-- <input autocomplete="off" type="text" name="endereco[uf]" class="form-control" value="<?php echo is_post_value($CONFIG['cliente_session']['endereco']->uf) ?>" size="2" data-input="uf" id="uf" /> -->
						<select autocomplete="off" type="text" name="endereco[uf]" value="<?php echo is_post_value($CONFIG['cliente_session']['endereco']->uf) ?>" class="form-control" maxlength="2" data-input="uf" id="uf" style="height: auto;font-size: 12pt;line-height: normal;">
							<option value=""></option>
							<option value="AC" <?php echo $CONFIG['cliente_session']['endereco']->uf == "AC" ? 'selected' : ''?> >AC</option>
							<option value="AL" <?php echo $CONFIG['cliente_session']['endereco']->uf == "AL" ? 'selected' : ''?> >AL</option>
							<option value="AP" <?php echo $CONFIG['cliente_session']['endereco']->uf == "AP" ? 'selected' : ''?> >AP</option>
							<option value="AM" <?php echo $CONFIG['cliente_session']['endereco']->uf == "AM" ? 'selected' : ''?> >AM</option>
							<option value="BA" <?php echo $CONFIG['cliente_session']['endereco']->uf == "BA" ? 'selected' : ''?> >BA</option>
							<option value="CE" <?php echo $CONFIG['cliente_session']['endereco']->uf == "CE" ? 'selected' : ''?> >CE</option>
							<option value="DF" <?php echo $CONFIG['cliente_session']['endereco']->uf == "DF" ? 'selected' : ''?> >DF</option>
							<option value="ES" <?php echo $CONFIG['cliente_session']['endereco']->uf == "ES" ? 'selected' : ''?> >ES</option>
							<option value="GO" <?php echo $CONFIG['cliente_session']['endereco']->uf == "GO" ? 'selected' : ''?> >GO</option>
							<option value="MA" <?php echo $CONFIG['cliente_session']['endereco']->uf == "MA" ? 'selected' : ''?> >MA</option>
							<option value="MT" <?php echo $CONFIG['cliente_session']['endereco']->uf == "MT" ? 'selected' : ''?> >MT</option>
							<option value="MS" <?php echo $CONFIG['cliente_session']['endereco']->uf == "MS" ? 'selected' : ''?> >MS</option>
							<option value="MG" <?php echo $CONFIG['cliente_session']['endereco']->uf == "MG" ? 'selected' : ''?> >MG</option>
							<option value="PA" <?php echo $CONFIG['cliente_session']['endereco']->uf == "PA" ? 'selected' : ''?> >PA</option>
							<option value="PB" <?php echo $CONFIG['cliente_session']['endereco']->uf == "PB" ? 'selected' : ''?> >PB</option>
							<option value="PR" <?php echo $CONFIG['cliente_session']['endereco']->uf == "PR" ? 'selected' : ''?> >PR</option>
							<option value="PE" <?php echo $CONFIG['cliente_session']['endereco']->uf == "PE" ? 'selected' : ''?> >PE</option>
							<option value="PI" <?php echo $CONFIG['cliente_session']['endereco']->uf == "PI" ? 'selected' : ''?> >PI</option>
							<option value="RJ" <?php echo $CONFIG['cliente_session']['endereco']->uf == "RJ" ? 'selected' : ''?> >RJ</option>
							<option value="RN" <?php echo $CONFIG['cliente_session']['endereco']->uf == "RN" ? 'selected' : ''?> >RN</option>
							<option value="RS" <?php echo $CONFIG['cliente_session']['endereco']->uf == "RS" ? 'selected' : ''?> >RS</option>
							<option value="RO" <?php echo $CONFIG['cliente_session']['endereco']->uf == "RO" ? 'selected' : ''?> >RO</option>
							<option value="RR" <?php echo $CONFIG['cliente_session']['endereco']->uf == "RR" ? 'selected' : ''?> >RR</option>
							<option value="SC" <?php echo $CONFIG['cliente_session']['endereco']->uf == "SC" ? 'selected' : ''?> >SC</option>
							<option value="SP" <?php echo $CONFIG['cliente_session']['endereco']->uf == "SP" ? 'selected' : ''?> >SP</option>
							<option value="SE" <?php echo $CONFIG['cliente_session']['endereco']->uf == "SE" ? 'selected' : ''?> >SE</option>
							<option value="TO" <?php echo $CONFIG['cliente_session']['endereco']->uf == "TO" ? 'selected' : ''?> >TO</option>
						</select>
						<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['endereco']['uf']) ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	<?php } ?>

	<?php if ($GET_ACAO !== 'editar-cadastro') { ?>
		<div class="form-horizontal mb25" data-id="senha">
			<h2 class="mb5">Dados de acesso</h2>
			<span class="show ft13px">Campos com (*) são obrigatórios</span>
			<hr />
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="senha_real">
					Senha *
				</label>
				<div class="col-sm-3">
					<input autocomplete="off" type="password" name="cadastro[senha_real]" class="form-control" id="senha_real" />
					<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['cadastro']['senha_real']) ?>
				</div>
			</div>
			<div class="form-group form-group-lg">
				<label class="col-sm-4 control-label" for="senha_confirm">Confirmar senha *</label>
				<div class="col-sm-3">
					<input autocomplete="off" type="password" name="cadastro[senha_confirm]" class="form-control" id="senha_confirm" />
					<?php echo tigger_error($ErrorCheckoutCadastrarEditarAll['cadastro']['senha_confirm']) ?>
				</div>
			</div>
			<span class="show ft12px">A senha requer entre 4 a 12 caracteres</span>
		</div>
	<?php } ?>

	<?php if ($GET_ACAO !== 'editar-cadastro') { ?>
		<div class="form-horizontal mb25 hidden" data-id="indicacao">
			<h2 class="mb5">Informe aonde você conheceu a <?php echo $CONFIG['nome_fantasia']; ?></h2>
			<hr />
			<div class="form-group form-group-lg">
				<div class="col-sm-5 col-xs-12 mb15">
					<select name="cadastro[indicacao]" id="indicacao" class="form-control">
						<option value="O cliente não optou sobre as indicações do site.">SELECIONE UMA INDICAÇÃO</option>
						<option value="GOOGLE">GOOGLE</option>
						<option value="FACEBOOK">FACEBOOK</option>
						<option value="INSTAGRAM">INSTAGRAM</option>
						<option value="INDICAÇAO DE UM(A) AMIGO(A)">INDICAÇAO DE UM(A) AMIGO(A)</option>
						<option value="OUTROS">OUTROS</option>
					</select>
				</div>
				<div class="col-sm-12" id="outros" style="display: none;">
					<textarea name="cadastro[outros]" rows="5" placeholder="Digite aonde você nos encontrou." class="form-control" /></textarea>
				</div>
			</div>
		</div>
	<?php } ?>

	<center class="clearfix w100<?php echo isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] ? ' pull-right' : '' ?>">
		<button type="submit" class="mt20 btn btn-success btn-lg" data-type="submit"> FINALIZAR CADASTRO </button>
	</center>
	<input name="acao" type="hidden" value="<?php echo sha1('CadastroCadastrarEditar'); ?>" />
	<error id="error" style="display:none;visibility:visible;">
		<?php echo tigger_error((is_array($ErrorCheckoutCadastrarEditarAll['cadastro']) ? current($ErrorCheckoutCadastrarEditarAll['cadastro']) : false)); ?>
		<?php echo (!empty($MensagemNovoCheckoutLogin) ? $MensagemNovoCheckoutLogin : ''); ?>
	</error>
</form>
<style>
	.form-group {
		padding-top: 7px;
		padding-bottom: 7px;
	}

	.form-group.has-error {
		background-color: #fff5f5;
	}
</style>
<?php ob_start(); ?>
<script>
	<?php require PATH_ROOT . 'public/js/jquery-cpf-and-cnpj.js'; ?>
	// var Cadastro = {
	// 	reload_element: function(str, element) {
	// 		var list = $("<div/>", {
	// 			html: str
	// 		});
	// 		$(element).html(list.find(element).html());
	// 	}
	// };
	var telMaskBehavior = function(val) {
		return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
	};
	var telOptions = {
		onKeyPress: function(val, e, field, options) {
			field.mask(telMaskBehavior.apply({}, arguments), options);
		}
	};

	var cpfCnpjMaskBehavior = function(val) {
		return val.replace(/\D/g, '').length === 14 ? '00.000.000/0000-00' : '000.000.000-00999';
	};
	var cpfCnpjOptions = {
		onKeyPress: function(val, e, field, options) {
			field.mask(cpfCnpjMaskBehavior.apply({}, arguments), {
				reverse: false
			});
		}
	};

	$("#form-cadastro").on("change", "select", function() {
		if ($(this).val() === 'OUTROS')
			$("#outros").fadeIn(10);
		else
			$("#outros").fadeOut(0).find("textarea").val("");
	});

	// $(document).ajaxStart(function() {
	// 	$("button[type=submit]").attr({"disabled": true });
	// 	$("#aminacao-site").fadeIn(0);
	// })
	// .ajaxComplete(function(a,b,c) {
	// 	$("button[type=submit]").attr({"disabled": false});
	// })
	// .ajaxStop(function(event, request, settings) {
	// 	$("#aminacao-site").fadeOut(10);
	// 	$("#form-cadastro").find("input[name=asd]:checked").trigger("click");
	// 	$("#form-cadastro").find("input[name],select[name]")
	// 	.focus(function(){
	// 		$(this).parent().addClass("border-in");
	// 	})
	// 	.blur(function(){
	// 		$(this).parent().removeClass("border-in");
	// 	});
	// });

	// $("#form-cadastro").find("input[name],select[name]")
	// .focus(function(){
	// 	$(this).parent().addClass("border-in");
	// })
	// .blur(function(){
	// 	$(this).parent().removeClass("border-in");
	// });

	// // Somente parar efeturar o cadastro do cliente
	// <?php if ($GET_ACAO == 'cadastre-se') { ?>
	// $("#form-cadastro").on("change", "#email", function(e){
	// 	var isEmail = $(e.target).val();		
	// 	$.ajax({
	// 		url: window.location.href,
	// 		type: "post",
	// 		data: { acao: "VerificarCadastroDeEmail", email: isEmail },
	// 		beforeSend: function(){},
	// 		complete: function(){},
	// 		success: function( str ) {
	// 			var list = $("<div/>", { html: str });
	// 			$("#email").parent().parent().html( list.find("#email").parent().parent().html() );
	// 		}
	// 	})
	// });
	// <?php } ?>

	// Tenta modificar os inputs para um cadastro de pessoa Fisica ou Juridica
	$("#form-cadastro").on("click", "[name=tipopessoa]", function(e) {
		var eClick = $(e.currentTarget);
		var tipoName = $("[data-tipo-name]");
		var tipoCpfCnpj = $("[data-tipo-cpfcnpj]");
		var tipoRgIe = $("[data-tipo-rgie]");
				
		// Define os tipos de cada input
		var arrayTipoName 	 = ["<?php echo $STORE['config']['cadastro']['nome']['text'] ?>",    "<?php echo $STORE['config']['cadastro']['nome']['text-attr'] ?>"];
		var arrayTipoCpfCnpj = ["<?php echo $STORE['config']['cadastro']['cpfcnpj']['text'] ?>", "<?php echo $STORE['config']['cadastro']['cpfcnpj']['text-attr'] ?>"];
		var arrayTipoRgIe 	 = ["<?php echo $STORE['config']['cadastro']['rg']['text'] ?>",      "<?php echo $STORE['config']['cadastro']['rg']['text-attr'] ?>"];

		// Joga um reverso de array, e... tá pronto!
		if( eClick.val() === "2" ) {
			arrayTipoName.reverse();
			arrayTipoCpfCnpj.reverse();
			arrayTipoRgIe.reverse();
		}

		tipoName.attr({"data-tipo-name": arrayTipoName[1]}).html(arrayTipoName[0]);
		tipoCpfCnpj.attr({"data-tipo-cpfcnpj": arrayTipoCpfCnpj[1]}).html(arrayTipoCpfCnpj[0]);
		tipoRgIe.attr({"data-tipo-rgie": arrayTipoRgIe[1]}).html(arrayTipoRgIe[0]);

		// Adiciona um regra para o campo RG que passa a ser IE
		if(tipoRgIe.is(":visible") && eClick.val() === "2") {
			$( "input[name='cadastro[rg]']" ).rules( "add", { required: 1 });
		} else {
			$( "input[name='cadastro[rg]']" ).rules( "remove", "required");
		}

		// Remove a regra para o campo data_nascimento sendo cadastro de IE
		if($("input[name='cadastro[data_nascimento]']").is(":visible") && eClick.val() === "2") {
			$( "input[name='cadastro[data_nascimento]']" ).rules( "remove", "required");
		} else if($("input[name='cadastro[data_nascimento]']").is(":visible")) {
			$( "input[name='cadastro[data_nascimento]']" ).rules( "add", { required: 1 });
		}
	});

	$("#form-cadastro").find("input[input-mask=telefone]").mask(telMaskBehavior, telOptions);
	$("#form-cadastro").find("input[input-mask=cpfcnpj]").mask(cpfCnpjMaskBehavior, cpfCnpjOptions);

	$("#form-cadastro").find("input[input-mask=cep]").mask("00000-000", {
		onComplete: busca_cidade
	});
	$("#form-cadastro").find("input[input-mask=data_nascimento]").mask('00 / 00 / 0000');
	// $("#form-cadastro").find("input[name=tipopessoa]:checked").trigger("click");

	/**
	 * Tenta validar cpf e conj na mesma mascara
	 */
	// 50257516832
	$.validator.addMethod("cpfcnpj_test", function(value, element) {
		return this.optional(element) || valida_cpf_cnpj(value)
	}, "CNPJ ou CPF inválido!");

	/**
	 * Tenta verificar se já existe um e-mail cadastrado
	 */
	$.validator.addMethod("check_mail", function(value, element) {
		<?php if (empty($CONFIG['cliente_session']['id'])) { ?>
			return $.ajax({
				global: false,
				url: window.location.href,
				type: "post",
				data: {
					acao: "VerificarCadastroDeEmail",
					email: value
				},
				error: function() {},
				complete: function() {},
				beforeSend: function() {},
				success: function(str) {
					var list = $("<div/>", {
						html: str
					});
					$("#email").parent().parent().html(list.find("#email").parent().parent().html());
				}
			});
		<?php } else echo 'return 1;' ?>
	}, "Já existe um usuário com esse e-mail");

	/**
	 * Tenta verificar se é um telefone válido
	 */
	$.validator.addMethod("telcel_test", function(phone_number, element) {
		phone_number = phone_number.replace(/\s+/g, "");
		return this.optional(element) || phone_number.length > 9 &&
			phone_number.match(/^(\(?\d{2}\)?) ?9?\d{4}-?\d{4}$/);
	}, "Número de telefone ou celular inválido.");

	/**
	 * Faz o usuario digitar nome e sobrenome corretamente
	 */
	$.validator.addMethod("nome_test", function(value, element) {
		return this.optional(element) || /[A-zÀ-ú']{1,}\s[A-zÀ-ú']{2,}'?-?[A-zÀ-ú']{1,}\s?([A-zÀ-ú']{2,})?/.test(value);
	}, "Por favor, insira seu nome e sobrenome.");

	<?php
	$cadastro = $STORE['config']['cadastro'];
	$endereco = $STORE['config']['endereco'];
	?>

	$("#form-cadastro").validate({
		debug: true,
		errorClass: "text-danger ft11px",
		errorElement: "span",
		rules: {
			"cadastro[email]": {
				required: <?php echo $cadastro['email']['required'] ? 'true' : 'false' ?>,
				maxlength: 50,
				email: true,
				check_mail: true
			},
			"cadastro[nome]": {
				required: <?php echo $cadastro['nome']['required'] ? 'true' : 'false' ?>,
				nome_test: true
			},
			"cadastro[sobrenome]": {
				required: <?php echo $cadastro['sobrenome']['required'] ? 'true' : 'false' ?>,
				minlength: 4,
				maxlength: 40
			},
			"cadastro[data_nascimento]": {
				required: <?php echo $cadastro['data_nascimento']['required'] ? 'true' : 'false' ?>,
				minlength: 14
			},
			"cadastro[cpfcnpj]": {
				required: <?php echo $cadastro['cpfcnpj']['required'] ? 'true' : 'false' ?>,
				minlength: 14,
				maxlength: 21,
				cpfcnpj_test: true
			},
			"cadastro[rg]": {
				required: <?php echo $cadastro['rg']['required'] ? 'true' : 'false' ?>,
				maxlength: 21
			},
			"cadastro[telefone]": {
				required: <?php echo $cadastro['telefone']['required'] ? 'true' : 'false' ?>,
				telcel_test: true
			},
			"cadastro[celular]": {
				required: <?php echo $cadastro['celular']['required'] ? 'true' : 'false' ?>,
				telcel_test: true
			},
			"cadastro[operadora]": {
				required: <?php echo $cadastro['operadora']['required'] ? 'true' : 'false' ?>,
				minlength: 14,
				maxlength: 15,
			},
			"cadastro[sexo]": {
				required: <?php echo $cadastro['sexo']['required'] ? 'true' : 'false' ?>
			},

			// "cadastro[senha_false]": 		{ required: <?php echo $cadastro['senha']['required'] ? 'true' : 'false' ?>, minlength: 6, maxlength: 12 },
			// "cadastro[senha_confirm]": 		{ required: <?php echo $cadastro['senha']['required'] ? 'true' : 'false' ?>, minlength: 6, maxlength: 12, equalTo: "#senha_false" },

			"cadastro[senha_real]": {
				required: true,
				minlength: 6,
				maxlength: 12
			},
			"cadastro[senha_confirm]": {
				required: true,
				minlength: 6,
				maxlength: 12,
				equalTo: "#senha_real"
			},

			"endereco[cep]": {
				required: <?php echo $endereco['cep']['required'] ? 'true' : 'false' ?>
			},
			"endereco[nome]": {
				required: <?php echo $endereco['nome']['required'] ? 'true' : 'false' ?>
			},
			"endereco[receber]": {
				required: <?php echo $endereco['receber']['required'] ? 'true' : 'false' ?>
			},
			// "endereco[nomeendereco]": 	{ required: <?php echo $endereco['nomeendereco']['required'] ? 'true' : 'false' ?> },
			"endereco[complemento]": {
				required: false,
				maxlength: 30
			},
			"endereco[endereco]": {
				required: <?php echo $endereco['endereco']['required'] ? 'true' : 'false' ?>,
				maxlength: 50
			},
			"endereco[numero]": {
				required: <?php echo $endereco['numero']['required'] ? 'true' : 'false' ?>,
				maxlength: 5,
				number: true
			},
			"endereco[bairro]": {
				required: <?php echo $endereco['bairro']['required'] ? 'true' : 'false' ?>,
				maxlength: 30
			},
			"endereco[cidade]": {
				required: <?php echo $endereco['cidade']['required'] ? 'true' : 'false' ?>,
				maxlength: 30
			},
			"endereco[uf]": {
				required: <?php echo $endereco['uf']['required'] ? 'true' : 'false' ?>,
				maxlength: 2
			}
		},
		messages: {
			"cadastro[nome]": {
				required: "<?php echo $cadastro['nome']['text_required'] ?>"
			},
			"cadastro[email]": {
				required: "<?php echo $cadastro['email']['text_required'] ?>",
				maxlength: "Máximo de {0} caracterers permitido!",
				email: "Digite um e-mail válido"
			},
			"cadastro[data_nascimento]": {
				required: "<?php echo $cadastro['data_nascimento']['text_required'] ?>",
				minlength: "Sua data de nascimento esta incorreta!"
			},
			"cadastro[cpfcnpj]": {
				required: "<?php echo $cadastro['cpfcnpj']['text_required'] ?>",
				minlength: "Campo requer mínimo de {0} caracteres",
				maxlength: "Campo requer máximo de {0} caracteres"
			},
			"cadastro[rg]": {
				required: "<?php echo $cadastro['rg']['text_required'] ?>",
				maxlength: "Campo requer máximo de {0} caracteres"
			},
			"cadastro[telefone]": {
				required: "<?php echo $cadastro['telefone']['text_required'] ?>"
			},
			"cadastro[celular]": {
				required: "<?php echo $cadastro['celular']['text_required'] ?>"
			},
			"cadastro[operadora]": {
				required: "<?php echo $cadastro['operadora']['text_required'] ?>"
			},
			"cadastro[sexo]": {
				required: "<?php echo $cadastro['sexo']['text_required'] ?>"
			},

			"cadastro[senha_real]": {
				required: "<?php echo $cadastro['senha_real']['text_required'] ?>",
				minlength: "Senha requer {0} caracteres",
				maxlength: "Senha requer {0} caracteres"
			},
			"cadastro[senha_confirm]": {
				required: "<?php echo $cadastro['senha_confirm']['text_required'] ?>",
				minlength: "Senha requer {0} caracteres",
				maxlength: "Senha requer {0} caracteres",
				equalTo: "As senhas não conferem!"
			},

			"endereco[cep]": {
				required: "<?php echo $endereco['cep']['text_required'] ?>"
			},
			"endereco[nome]": {
				required: "<?php echo $endereco['nome']['text_required'] ?>"
			},
			"endereco[receber]": {
				required: "<?php echo $endereco['receber']['text_required'] ?>"
			},
			// "endereco[nomeendereco]": 	{ required: "<?php echo $endereco['nomeendereco']['text_required'] ?>" },
			"endereco[complemento]": {
				maxlength: "Máximo de {0} caracterers permitido!"
			},
			"endereco[endereco]": {
				required: "<?php echo $endereco['endereco']['text_required'] ?>",
				maxlength: "Máximo de {0} caracterers permitido!"
			},
			"endereco[numero]": {
				required: "<?php echo $endereco['numero']['text_required'] ?>",
				maxlength: "Máximo de {0} caracterers permitido!"
			},
			"endereco[bairro]": {
				required: "<?php echo $endereco['bairro']['text_required'] ?>",
				maxlength: "Máximo de {0} caracterers permitido!"
			},
			"endereco[cidade]": {
				required: "<?php echo $endereco['cidade']['text_required'] ?>",
				maxlength: "Máximo de {0} caracterers permitido!"
			},
			"endereco[uf]": {
				required: "<?php echo $endereco['uf']['text_required'] ?>",
				maxlength: "Digite apenas {0} caracterers"
			}
		},
		highlight: function(element, errorClass, validClass) {
			$(element).parent().parent().addClass("has-error").removeClass("has-info");
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parent().parent().removeClass("has-error").addClass("has-info");
		},
		submitHandler: function(form, validator) {
			var FormDataCadastro = $(form).serialize(),
				ModalSite = $("#modal-site"),
				ErrorError = $("#error");

			ModalSite.modal("show");

			$.ajax({
				url: window.location.href,
				type: "post",
				data: FormDataCadastro,
				beforeSend: function() {
					ModalSite.find("p").html("Salvando seu cadastro...");
					ErrorError.html();
				},
				success: function(str) {

					var list = $("<div/>", {
							html: str
						}),
						ErrorCadastroHref = list.find("#location_href");
					ErrorCadastro = list.find("#error").find("span").find("span");

					if (ErrorCadastroHref.length > 0 && ErrorCadastroHref.html() !== "") {
						ModalSite.find("p").html([ErrorCadastroHref.html()]);
						reject("Promise ajax1 rejected");
						return false;
					}

					if (ErrorCadastro.length > 0 && ErrorCadastro.html() !== "") {
						$("#form-cadastro").html(list.find("#form-cadastro").html());
						ModalSite.find("p").html("Campos com (*) são obrigatórios!");
						reject("Promise ajax1 rejected");
						return false;
					}

					$("#form-cadastro").find("#email").attr({
						name: "email"
					});
					$("#form-cadastro").find("#senha_confirm").attr({
						name: "senha"
					});
					$("#form-cadastro").find("input[name=acao]").val("NewCheckoutLogin");

					var DataLogin = FormValidate.find("input[name=acao], input[name=email], input[name=senha]").serialize();

					$.ajax({
						url: window.location.href,
						type: "post",
						data: DataLogin,
						beforeSend: function() {
							ModalSite.find("p").html("Aguarde, Fazendo login...");
							ErrorError.html();
						},
						success: function(str) {
							var list = $("<div/>", {
								html: str
							});
							$("#error").html(list.find("#error").html());
						},
						complete: function() {},
						error: function() {}
					});
				},
				complete: function() {},
				error: function() {}
			});
		}
	});
</script>
<?php $str['script_manual'] .= ob_get_clean(); 