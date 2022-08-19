<?php
include '../topo.php';

$acao = filter_input(INPUT_POST, 'acao');

switch ($acao) {
	case 'JadLogConf':
		$id = (int)filter_input(INPUT_POST, 'id');
		$email = filter_input(INPUT_POST, 'email');
		$nome = filter_input(INPUT_POST, 'nome');
		$fantasia = filter_input(INPUT_POST, 'fantasia');
		$remet = filter_input(INPUT_POST, 'remet');
		$pass = filter_input(INPUT_POST, 'pass');
		$cnpjCpf = filter_input(INPUT_POST, 'cnpjCpf');
		$ie = filter_input(INPUT_POST, 'ie');
		$endereco = filter_input(INPUT_POST, 'endereco');
		$numero = filter_input(INPUT_POST, 'numero');
		$compl = filter_input(INPUT_POST, 'compl');
		$bairro = filter_input(INPUT_POST, 'bairro');
		$cidade = filter_input(INPUT_POST, 'cidade');
		$uf = filter_input(INPUT_POST, 'uf');
		$cep = filter_input(INPUT_POST, 'cep');
		$fone = filter_input(INPUT_POST, 'fone');
		$cel = filter_input(INPUT_POST, 'cel');
		$codcli = filter_input(INPUT_POST, 'codcli');
		$ponto = filter_input(INPUT_POST, 'ponto');
		$vlColeta = filter_input(INPUT_POST, 'vlColeta');
		$contaCorrente = filter_input(INPUT_POST, 'contaCorrente');
		$nrContrato = filter_input(INPUT_POST, 'nrContrato');
		$token = filter_input(INPUT_POST, 'token');

		$JadLog = JadLog::action_cadastrar_editar([
			'JadLog' => [$id => [
				'email' => $email,
				'nome' => $nome,
				'fantasia' => $fantasia,
				'remet' => $remet,
				'pass' => $pass,
				'cnpjcpf' => $cnpjCpf,
				'ie' => $ie,
				'endereco' => $endereco,
				'numero' => $numero,
				'compl' => $compl,
				'bairro' => $bairro,
				'cidade' => $cidade,
				'uf' => $uf,
				'cep' => $cep,
				'fone' => $fone,
				'cel' => $cel,
				'codcli' => $codcli,
				'ponto' => $ponto,
				'vlColeta' => $vlColeta,
				'contaCorrente' => $contaCorrente,
				'nrContrato' => $nrContrato,
				'token' => $token,
			]]
		], 'alterar', 'usuario');

		// $servicos = filter_input(INPUT_POST, 'servicos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
		
		// if ($JadLog['id'] > 0) {
		// 	foreach ($servicos as $k => $v) {
		// 		$servico = explode('*', $v);
		// 		$servico_int = $servico[0];
		// 		$servico_text = $servico[1];

		// 		$JadLogServicosCount = JadLogServicos::count(['conditions' => ['servico_int=? and servico_text=? and id_jadlog=?', $servico_int, $servico_text, $JadLog['id']]]);
		// 		if ($JadLogServicosCount == 0) {
		// 			$JadLogServicos = new JadLogServicos();
		// 			$JadLogServicos->loja_id = $CONFIG['loja_id'];
		// 			$JadLogServicos->id_jadlog = $id;
		// 			$JadLogServicos->servico_int = $servico_int;
		// 			$JadLogServicos->servico_text = $servico_text;
		// 			$JadLogServicos->save();
		// 		}
		// 	}

		// }
		header('Location: /adm/jadlog/jadlog.php');
		return;

	break;

	case 'JadLogServicos':
		$id = (int)filter_input(INPUT_POST, 'id');
		$servicos = filter_input(INPUT_POST, 'servicos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
		
		if ($id > 0) {

			JadLogServicos::delete_all(array('conditions' => array('id_jadlog' => $id)));

			foreach ($servicos as $k => $v) {
				$servico = explode('*', $v);
				$servico_int = $servico[0];
				$servico_text = $servico[1];

				$JadLogServicosCount = JadLogServicos::count(['conditions' => ['servico_int=? and servico_text=? and id_jadlog=?', $servico_int, $servico_text, $id]]);
				if ($JadLogServicosCount == 0) {
					$JadLogServicos = new JadLogServicos();
					$JadLogServicos->loja_id = $CONFIG['loja_id'];
					$JadLogServicos->id_jadlog = $id;
					$JadLogServicos->servico_int = $servico_int;
					$JadLogServicos->servico_text = $servico_text;
					$JadLogServicos->save();
				}
			}

			header('Location: /adm/jadlog/jadlog.php');
			return;
		}

	break;

	case 'JadLogLogo':

		
	    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
		

	    $logo_temp = $_FILES;

		if( ! empty( $logo_temp['logo_loja']['size'] ) ) {
			$logo = $logo_temp['logo_loja'];
		}

		if( empty( $logo_temp['logo_loja']['size'] ) ) {
			header('Location: /adm/jadlog/jadlog.php?error=error_image');
			return;
		}

		$CAMINHO = URL_VIEWS_BASE_PUBLIC_UPLOAD . '/imgs/';

		$ext_pathinfo = pathinfo($logo['name']);
		$ext = $ext_pathinfo['extension'];

		$NOVO_NOME_IMAGEM = $id . '-jadlog.' . $ext;
		
		// Envia o logo favicon ico
		$WideImageTmpName = WideImage\WideImage::load( $logo['tmp_name'] );
		$WideImageTmpName->saveToFile( $CAMINHO . $NOVO_NOME_IMAGEM );
		$WideImageTmpName->destroy();
		if( Configuracoes::action_cadastrar_editar([ 'JadLog' => [ $id => [ 'logo_loja' => $NOVO_NOME_IMAGEM ] ] ], 'alterar', 'usuario') ) {
			header('Location: /adm/jadlog/jadlog.php');
			return;
		}
	break;
}

$JadLog = JadLog::first(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
$JadLogCount = (int)count($JadLog->to_array());
if ($JadLogCount == 0) {
	JadLog::action_cadastrar_editar([
		'JadLog' => [0 => [
			'email' => '',
		]]
	], 'cadastrar', 'email');

	header('Location: /adm/jadlog/jadlog.php');
	return;
}
?>
<style>
	.formulario p {
		margin: 5px 0 7px 0;
		font-weight: 500;
	}

	body {
		background-color: #f1f1f1
	}
	.select2-container--default .select2-selection--multiple {
		background-color: #ffffff;
		border: 1px solid rgba(0, 0, 0, 0.1);
		-webkit-border-radius: 2px;
		border-radius: 2px;
		cursor: text;
		min-height: 22px;
	}
	.select2-selection {
		height: auto !important;
	}
</style>

<div class="row">
		<form class="col-sm-7 col-xs-12" action="/adm/jadlog/jadlog.php" method="post">
			<div class="panel panel-default">
				<div class="panel-heading panel-store text-uppercase">Configurações - Etiquetas</div>
				<div class="panel-body">
					<div id="formulario" class="formulario row">
						<input type="hidden" name="acao" value="JadLogConf"/>
						<input type="hidden" name="id" value="<?php echo $JadLog->id;?>"/>
						<div class="form-group col-sm-5">
							<label for="" class="">E-mail:</label>
							<input type="text" name="email" value="<?php echo $JadLog->email;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-7">
							<label for="" class="">Nome:</label>
							<input type="nome" name="nome" value="<?php echo $JadLog->nome;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-4">
							<label for="" class="">Fantasia:</label>
							<input type="fantasia" name="fantasia" value="<?php echo $JadLog->fantasia;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-8">
							<label for="" class="">Remetente:</label>
							<input type="remet" name="remet" value="<?php echo $JadLog->remet;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-3">
							<label for="" class="">Senha:</label>
							<input type="pass" name="pass" value="<?php echo $JadLog->pass;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-7">
							<label for="" class="">CNPJ da Empresa:</label>
							<input type="cnpjCpf" name="cnpjCpf" value="<?php echo $JadLog->cnpjcpf;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-7">
							<label for="" class="">IE:</label>
							<input type="ie" name="ie" value="<?php echo $JadLog->ie;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-5">
							<label for="" class="">Endereço (Coleta):</label>
							<input type="endereco" name="endereco" value="<?php echo $JadLog->endereco;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-3">
							<label for="" class="">Número:</label>
							<input type="numero" name="numero" value="<?php echo $JadLog->numero;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-4">
							<label for="" class="">Complemento:</label>
							<input type="compl" name="compl" value="<?php echo $JadLog->compl;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-5">
							<label for="" class="">Bairro:</label>
							<input type="bairro" name="bairro" value="<?php echo $JadLog->bairro;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-5">
							<label for="" class="">Cidade:</label>
							<input type="cidade" name="cidade" value="<?php echo $JadLog->cidade;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-2">
							<label for="" class="">UF:</label>
							<input type="uf" name="uf" value="<?php echo $JadLog->uf;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-4">
							<label for="" class="">CEP:</label>
							<input type="cep" name="cep" value="<?php echo $JadLog->cep;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-4">
							<label for="" class="">Telefone:</label>
							<input type="fone" name="fone" value="<?php echo $JadLog->fone;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-4">
							<label for="" class="">Celular:</label>
							<input type="cel" name="cel" value="<?php echo $JadLog->cel;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-3">
							<label for="" class="">Código do Cliente:</label>
							<input type="codcli" name="codcli" value="<?php echo $JadLog->codcli;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-3">
							<label for="" class="">Ponto de Coleta:</label>
							<input type="ponto" name="ponto" value="<?php echo $JadLog->ponto;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-3">
							<label for="" class="">Valor de Coleta:</label>
							<input type="vlColeta" name="vlColeta" value="<?php echo $JadLog->vlcoleta;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-3">
							<label for="" class="">CC (Con</label>ta Corrente):
							<input type="contaCorrente" name="contaCorrente" value="<?php echo $JadLog->contacorrente;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-3">
							<label for="" class="">Número do Contrato:</label>
							<input type="nrContrato" name="nrContrato" value="<?php echo $JadLog->nrcontrato;?>" class="form-control"/>
						</div>

						<div class="form-group col-sm-12">
							<label for="" class="">Token de Acesso:</label>
							<input type="token" name="token" value="<?php echo $JadLog->token;?>" class="form-control"/>
						</div>
					</div>
					<div class="text-center">
						<button type="submit" class="mt15 btn btn-primary">Salvar Configurações</button>
					</div>
				</div>
			</div>
		</form>
		
		<div class="col-sm-5 col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading panel-store text-uppercase">RUBRICA</div>
				<form class="panel-body" action="/adm/jadlog/jadlog.php" method="post" enctype="multipart/form-data">
					<input type="hidden" name="id" value="<?php echo $JadLog->id;?>" />
					<input type="hidden" name="acao" value="JadLogLogo"/>
					<div id="logo" class="formulario clearfix">
						<div class="row">
							<div class="text-center col-sm-3 col-xs-3">
								<?php if (!empty($JadLog->id) && $JadLog->id != '') {?>
									<img src="<?php echo Imgs::src("{$JadLog->id}-jadlog.png", 'imgs')?>" class="img-responsive center-block">
								<?php } else {?>
									<img src="<?php echo Imgs::src('logo-etiqueta.png', 'public')?>" class="img-responsive center-block">
								<?php }?>
							</div>
							<div class="col-sm-9 col-xs-9">
								<label for="" class="">Logo mobile:</label>
								<input type="file" name="logo_loja" />
							</div>
						</div>
					</div>
					<button type="submit" class="mt15 btn btn-primary">Salvar Logo</button>
				</form>
			</div>
		</div>

		<?php 
		$servico_by = [];
		$servico = $JadLog->etiquetas_servicos;
		if( count($servico) > 0 )
			foreach( $servico as $arr )
				$servico_by[] = sprintf('%s*%s', $arr->servico_int, $arr->servico_text);
		?>
		<form class="col-sm-7 col-xs-12" action="/adm/jadlog/jadlog.php" method="post">
			<input type="hidden" name="acao" value="JadLogServicos"/>
			<input type="hidden" name="id" value="<?php echo $JadLog->id;?>"/>
			<div class="panel panel-default">
				<div class="panel-heading panel-store text-uppercase">Tipos de Serviços</div>
				<div class="panel-body">
					<p>Serviços para postagem dos objetos:</p>
					<select name="servicos[]" multiple="multiple" size="5" style="width: 100%;">
						<option value="">Selecione uma opção</option>
						<option value="0*EXPRESSO"<?php echo in_array('0*EXPRESSO', $servico_by) ? ' selected':''?>>EXPRESSO</option>
						<option value="3*.PACKAGE"<?php echo in_array('3*.PACKAGE', $servico_by) ? ' selected':''?>>.PACKAGE</option>
						<option value="4*RODOVIÁRIO"<?php echo in_array('4*RODOVIÁRIO', $servico_by) ? ' selected':''?>>RODOVIÁRIO</option>
						<option value="5*ECONÔMICO"<?php echo in_array('5*ECONÔMICO', $servico_by) ? ' selected':''?>>ECONÔMICO</option>
						<option value="6*DOC"<?php echo in_array('6*DOC', $servico_by) ? ' selected':''?>>DOC</option>
						<option value="7*CORPORATE"<?php echo in_array('7*CORPORATE', $servico_by) ? ' selected':''?>>CORPORATE</option>
						<option value="9*.COM"<?php echo in_array('9*.COM', $servico_by) ? ' selected':''?>>.COM</option>
						<option value="10*INTERNACIONAL"<?php echo in_array('10*INTERNACIONAL', $servico_by) ? ' selected':''?>>INTERNACIONAL</option>
						<option value="12*CARGO"<?php echo in_array('12*CARGO', $servico_by) ? ' selected':''?>>CARGO</option>
						<option value="14*EMERGÊNCIAL"<?php echo in_array('14*EMERGÊNCIAL', $servico_by) ? ' selected':''?>>EMERGÊNCIAL</option>
						<option value="40*PICKUP"<?php echo in_array('40*PICKUP', $servico_by) ? ' selected':''?>>PICKUP</option>
						<?php
						try {
							$acao 	 	 = filter_input(INPUT_GET, 'acao');
							$altura  	 = filter_input(INPUT_GET, 'altura');
							$largura 	 = filter_input(INPUT_GET, 'largura');
							$comprimento = filter_input(INPUT_GET, 'comprimento');
							$peso 		 = filter_input(INPUT_GET, 'peso');
							$cep 		 = filter_input(INPUT_GET, 'cep');
						} catch (exception $e) {
							print_r($e);
						}
						?>
					</select>
					<div class="clearfix text-center mt10">
						<button type="submit" class="ml20 btn btn-primary">Salvar</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<?php ob_start();?>
<script>
	// function busca_cidade( a, b ) {
	// var cep = a;
	// $.ajax({
	// url: "../",
	// type: "post",
	// data: { acao : "BuscaCidade", cep : cep },
	// dataType: "json",
	// beforeSend: function() {
	// $("input[name=cidade]").val("Carregando...");
	// $("input[name=uf]").val("");
	// }, 
	// success: function( str ) {
	// $("input[name=cidade]").val( str.cidade );
	// $("input[name=uf]").val( str.uf );
	// }, 
	// error: function( x,m,t ){ 
	// alert( x.responseText ); 
	// }
	// });
	// }

	// mascara = function (str) {	
	// if (str.value.length > 14)                       
	// str.value = cartao_postagem(str.value);
	// else                           
	// str.value = cpf(str.value);
	// };

	// function cpf(valor) {
	// valor = valor.replace(/\D/g, "");                   
	// valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
	// valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
	// valor = valor.replace(/(\d{3})(\d)$/, "$1-$2");     
	// return valor;
	// }

	// function cartao_postagem(valor) {
	// valor = valor.replace(/\D/g, "");
	// valor = valor.replace(/^(\d{2})(\d)/, "$1.$2");
	// valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
	// valor = valor.replace(/\.(\d{3})(\d)/, ".$1/$2");
	// valor = valor.replace(/(\d{4})(\d)/, "$1-$2");              
	// return valor;
	// }

	// $(function(){
	// var SPMaskBehavior = function (val) {
	// return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
	// },
	// spOptions = {
	// onKeyPress: function(val, e, field, options) {
	// field.mask(SPMaskBehavior.apply({}, arguments), options);
	// }
	// };

	// $("input[name=cep]").mask("00000-000", { onComplete : busca_cidade });
	// $("input[name=cep]").mask("00000-000", { onComplete : busca_cidade });
	// });
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();
include '../rodape.php';
