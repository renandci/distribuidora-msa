<?php
include dirname(__FILE__) . '/../topo.php';

$arquivo = dirname(__FILE__) . '/../../app/settings.inc';

if ( isset($_SERVER['REQUEST_METHOD'], $POST['config']['cadastro']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

	foreach ($POST as $key => $value)
		foreach ($value as $key1 => $value1)
			foreach ($value1 as $key2 => $value2)
				foreach ($value2 as $key3 => $value3)
					$setting[$key][$key1][$key2][$key3] = $value3;

	if (file_put_contents($arquivo, '<?php return ' . var_export($setting, true) . ';')) {
		header('Location: /adm/clientes/clientes-configuracao.php');
		return;
	}
}
?>
<style>
	body {
		background-color: #f1f1f1
	}
</style>
<div id="div-edicao">
	<form action="/adm/clientes/clientes-configuracao.php" method="post" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">CONFIGURÇÕES DE CADASTRO <small>Selecine as condições de cadastro para os usúarios</small></div>
					<div class="panel-body">
						<div class="col-lg-4 col-md-6 col-sm-12">
							<label class="show mb5">Mostrar tipo de Pessoa</label>
							<select name="config[cadastro][tipopessoa][status]" style="width: 100%">
								<option value="1" <?php echo $STORE['config']['cadastro']['tipopessoa']['status'] == true ? ' selected' : '' ?>>Sim</option>
								<option value="0" <?php echo $STORE['config']['cadastro']['tipopessoa']['status'] == false ? ' selected' : '' ?>>Não</option>
							</select>
						</div>
						<div class="col-lg-4 col-md-6 col-sm-12">
							<label class="show mb5" for="config_1">Campo obrigatório</label>
							<select name="config[cadastro][tipopessoa][required]" id="config_1" style="width: 100%">
								<option value="1" <?php echo $STORE['config']['cadastro']['tipopessoa']['required'] == true ? ' selected' : '' ?>>Sim</option>
								<option value="0" <?php echo $STORE['config']['cadastro']['tipopessoa']['required'] == false ? ' selected' : '' ?>>Não</option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-12 mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Nome</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Nome</label>
								<select name="config[cadastro][nome][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['nome']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['nome']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[cadastro][nome][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['nome']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['nome']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo e atributos</label>
								<input type="text" name="config[cadastro][nome][text-attr]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['nome']['text-attr']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[cadastro][nome][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['nome']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[cadastro][nome][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['nome']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">E-mail</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">E-mail</label>
								<select name="config[cadastro][email][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['email']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['email']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[cadastro][email][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['email']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['email']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[cadastro][email][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['email']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[cadastro][email][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['email']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-12 col-md-12 col-sm-12"></div>

			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">CNPJ/CPF</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">CNPJ/CPF</label>
								<select name="config[cadastro][cpfcnpj][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['cpfcnpj']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['cpfcnpj']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[cadastro][cpfcnpj][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['cpfcnpj']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['cpfcnpj']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo em Atributes</label>
								<input type="text" name="config[cadastro][cpfcnpj][text-attr]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['cpfcnpj']['text-attr']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[cadastro][cpfcnpj][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['cpfcnpj']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[cadastro][cpfcnpj][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['cpfcnpj']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">RG/IE</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">RG</label>
								<select name="config[cadastro][rg][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['rg']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['rg']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[cadastro][rg][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['rg']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['rg']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo em Atributes</label>
								<input type="text" name="config[cadastro][rg][text-attr]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['rg']['text-attr']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[cadastro][rg][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['rg']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[cadastro][rg][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['rg']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-12 col-md-12 col-sm-12"></div>

			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Data de Nascimento</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Data Nascimento</label>
								<select name="config[cadastro][data_nascimento][status]" style="width: 100%">
									<option value="1" <?php echo $CONFIG['insta_link']['cadastro']['data_nascimento']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['data_nascimento']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[cadastro][data_nascimento][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['data_nascimento']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['data_nascimento']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[cadastro][data_nascimento][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['data_nascimento']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[cadastro][data_nascimento][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['data_nascimento']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Sexo</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Sexo</label>
								<select name="config[cadastro][sexo][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['sexo']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['sexo']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[cadastro][sexo][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['sexo']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['sexo']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[cadastro][sexo][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['sexo']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[cadastro][sexo][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['sexo']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Telefone/Celular</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Telefone/Celular</label>
								<select name="config[cadastro][telefone][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['telefone']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['telefone']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[cadastro][telefone][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['telefone']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['telefone']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[cadastro][telefone][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['telefone']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[cadastro][telefone][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['telefone']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Celular/Telefone</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Celular</label>
								<select name="config[cadastro][celular][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['celular']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['celular']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[cadastro][celular][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['celular']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['celular']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[cadastro][celular][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['celular']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[cadastro][celular][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['celular']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Operadora</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Operadora</label>
								<select name="config[cadastro][operadora][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['operadora']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['operadora']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[cadastro][operadora][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['operadora']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['operadora']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[cadastro][operadora][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['operadora']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[cadastro][operadora][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['operadora']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Cidade</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Cidade</label>
								<select name="config[cadastro][cidade][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['cidade']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['cidade']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[cadastro][cidade][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['cidade']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['cidade']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[cadastro][cidade][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['cidade']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[cadastro][cidade][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['cidade']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">UF</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">UF</label>
								<select name="config[cadastro][uf][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['uf']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['uf']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[cadastro][uf][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['cadastro']['uf']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['cadastro']['uf']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[cadastro][uf][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['uf']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[cadastro][uf][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['cadastro']['uf']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Configuração de Endereço</div>
					<div class="panel-body">
						<label class="mb5">Adicionar endereço de entrega?</label>
						<select name="config[endereco][configure][status]" style="width: 120px">
							<option value="1" <?php echo $STORE['config']['endereco']['configure']['status'] == true ? ' selected' : '' ?>>Sim</option>
							<option value="0" <?php echo $STORE['config']['endereco']['configure']['status'] == false ? ' selected' : '' ?>>Não</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">CEP</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">CEP</label>
								<select name="config[endereco][cep][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['cep']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['cep']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[endereco][cep][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['cep']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['cep']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[endereco][cep][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['cep']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[endereco][cep][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['cep']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Nome do Destinatário</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Nome do Destinatário</label>
								<select name="config[endereco][nome][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['nome']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['nome']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[endereco][nome][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['nome']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['nome']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[endereco][nome][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['nome']['text']) ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[endereco][nome][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['nome']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Recebedor</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Nome do Recebedor</label>
								<select name="config[endereco][receber][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['receber']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['receber']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[endereco][receber][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['receber']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['receber']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[endereco][receber][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['receber']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[endereco][receber][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['receber']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Endereço</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Endereço</label>
								<select name="config[endereco][endereco][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['endereco']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['endereco']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[endereco][endereco][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['endereco']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['endereco']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[endereco][endereco][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['endereco']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[endereco][endereco][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['endereco']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Nr</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Número</label>
								<select name="config[endereco][numero][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['numero']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['numero']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[endereco][numero][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['numero']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['numero']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[endereco][numero][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['numero']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[endereco][numero][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['numero']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Bairro</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Bairro</label>
								<select name="config[endereco][bairro][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['bairro']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['bairro']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[endereco][bairro][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['bairro']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['bairro']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[endereco][bairro][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['bairro']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[endereco][bairro][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['bairro']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Complemento</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Complemento</label>
								<select name="config[endereco][complemento][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['complemento']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['complemento']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[endereco][complemento][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['complemento']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['complemento']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[endereco][complemento][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['complemento']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[endereco][complemento][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['complemento']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Referências</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Referências</label>
								<select name="config[endereco][referencia][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['referencia']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['referencia']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[endereco][referencia][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['referencia']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['referencia']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[endereco][referencia][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['referencia']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[endereco][referencia][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['referencia']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">Cidade</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Cidade</label>
								<select name="config[endereco][cidade][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['cidade']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['cidade']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[endereco][cidade][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['cidade']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['cidade']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[endereco][cidade][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['cidade']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[endereco][cidade][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['cidade']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 clearfix mt15">
				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">UF</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">UF</label>
								<select name="config[endereco][uf][status]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['uf']['status'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['uf']['status'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 mb15">
								<label class="show mb5">Campo obrigatório</label>
								<select name="config[endereco][uf][required]" style="width: 100%">
									<option value="1" <?php echo $STORE['config']['endereco']['uf']['required'] == true ? ' selected' : '' ?>>Sim</option>
									<option value="0" <?php echo $STORE['config']['endereco']['uf']['required'] == false ? ' selected' : '' ?>>Não</option>
								</select>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb15">
								<label class="show mb5">Descrição do campo</label>
								<input type="text" name="config[endereco][uf][text]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['uf']['text']); ?>" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<label class="show mb5">Descrição de erros</label>
								<input type="text" name="config[endereco][uf][text_required]" style="width: 100%" value="<?php echo htmlentities($STORE['config']['endereco']['uf']['text_required']); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix text-center mt15 col-sm-12">
				<button type="submit" class="btn btn-primary">salvar</button>
			</div>
		</div>
	</form>
</div>
<?php
include dirname(__FILE__) . '/../rodape.php';
