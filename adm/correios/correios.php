<?php
include '../topo.php';

switch ($POST['acao']) {
	case 'CorreiosConfi':
		$id                 = (int)filter_input(INPUT_POST, 'id');
		$usuario 			= filter_input(INPUT_POST, 'usuario');
		$senha 			    = filter_input(INPUT_POST, 'senha');
		$cod_admin    		= filter_input(INPUT_POST, 'cod_admin');
		$nro_contrato 		= filter_input(INPUT_POST, 'nro_contrato');
		$cartao_postagem    = filter_input(INPUT_POST, 'cartao_postagem');
		$cnpj_empresa 		= filter_input(INPUT_POST, 'cnpj_empresa');
		$ano_contrato 		= filter_input(INPUT_POST, 'ano_contrato');
		$diretoria 			= filter_input(INPUT_POST, 'diretoria');
		$setting_mode 		= filter_input(INPUT_POST, 'setting_mode');

		$Correios = Correios::action_cadastrar_editar([
			'Correios' => [$id => [
				'usuario' => $usuario,
				'senha' => $senha,
				'cod_admin' => $cod_admin,
				'nro_contrato' => $nro_contrato,
				'cartao_postagem' => $cartao_postagem,
				'cnpj_empresa' => $cnpj_empresa,
				'ano_contrato' => $ano_contrato,
				'diretoria' => $diretoria,
				'setting_mode' => $setting_mode
			]]
		], 'alterar', 'usuario');

		// $servicos = filter_input(INPUT_POST, 'servicos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
		
		// if ($Correios['id'] > 0) {
		// 	foreach ($servicos as $k => $v) {
		// 		$servico = explode('*', $v);
		// 		$servico_int = $servico[0];
		// 		$servico_text = $servico[1];

		// 		$CorreiosServicosCount = CorreiosServicos::count(['conditions' => ['servico_int=? and servico_text=? and id_correios=?', $servico_int, $servico_text, $Correios['id']]]);
		// 		if ($CorreiosServicosCount == 0) {
		// 			$CorreiosServicos = new CorreiosServicos();
		// 			$CorreiosServicos->loja_id = $CONFIG['loja_id'];
		// 			$CorreiosServicos->id_correios = $id;
		// 			$CorreiosServicos->servico_int = $servico_int;
		// 			$CorreiosServicos->servico_text = $servico_text;
		// 			$CorreiosServicos->save();
		// 		}
		// 	}

			header('Location: /adm/correios/correios.php');
			return;
		// }

	break;

	case 'CorreiosServicos':
		$id = (int)filter_input(INPUT_POST, 'id');
		$servicos = filter_input(INPUT_POST, 'servicos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
		
		if ($id > 0) {

			CorreiosServicos::delete_all(array('conditions' => array('id_correios' => $id)));

			foreach ($servicos as $k => $v) {
				$servico = explode('*', $v);
				$servico_int = $servico[0];
				$servico_text = $servico[1];

				$CorreiosServicosCount = CorreiosServicos::count(['conditions' => ['servico_int=? and servico_text=? and id_correios=?', $servico_int, $servico_text, $id]]);
				if ($CorreiosServicosCount == 0) {
					$CorreiosServicos = new CorreiosServicos();
					$CorreiosServicos->loja_id = $CONFIG['loja_id'];
					$CorreiosServicos->id_correios = $id;
					$CorreiosServicos->servico_int = $servico_int;
					$CorreiosServicos->servico_text = $servico_text;
					$CorreiosServicos->save();
				}
			}

			header('Location: /adm/correios/correios.php');
			return;
		}
	break;

	case 'CorreiosLogos':

		$CAMINHO = URL_VIEWS_BASE_PUBLIC_UPLOAD . '/imgs/';

		$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
		$Configuracoes = Correios::find($id);
		$ConfiguracoesArray = $Configuracoes->to_array();
		extract($ConfiguracoesArray);

		$logo_temp = $_FILES;

		if (!empty($logo_temp['logo_loja']['size'])) {
			$logo = $logo_temp['logo_loja'];
		}

		if (empty($logo_temp['logo_loja']['size'])) {
			header('Location: /adm/correios/correios.php?error=error_image');
			return;
		}

		$ext_pathinfo = pathinfo((!empty($logo_loja) ? $logo_loja : $logo['name']));
		$ext = $ext_pathinfo['extension'];

		
		// Envia o logo favicon ico
		if (!empty($logo_temp['logo_loja']['size'])) {
			
			$WideImageTmpName = WideImage\WideImage::load($logo['tmp_name']);

			$NOVO_NOME_IMAGEM = (!empty($logo_loja) ? $logo_loja : uniqid(time()) . '.' . $ext);

			$WideImageTmpName->saveToFile($CAMINHO . $NOVO_NOME_IMAGEM);
			$WideImageTmpName->destroy();

			if (Configuracoes::action_cadastrar_editar(['Correios' => [$id => ['logo_loja' => $NOVO_NOME_IMAGEM]]], 'alterar', 'usuario')) {
				header('Location: /adm/correios/correios.php');
				return;
			}
		}
	break;
}

$Correios = Correios::first(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
$ConfiguracoesFretesEnvios = ConfiguracoesFretesEnvios::first(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
if (count($Correios) == 0) {

	Correios::action_cadastrar_editar([
		'Correios' => [0 => [
			'usuario' => 'sigep',
			'senha' => 'n5f9t8',
			'cod_admin' => '17000190',
			'nro_contrato' => '9992157880',
			'cartao_postagem' => '0067599079',
			'cnpj_empresa' => '34028316000103',
			'ano_contrato' => '',
			'diretoria' => '10'
		]]
	], 'cadastrar', 'usuario');

	header('Location: /adm/correios/correios.php');
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
	<form class="col-sm-7 col-xs-12" action="/adm/correios/correios.php" method="post">
		<input type="hidden" name="acao" value="CorreiosConfi" />
		<input type="hidden" name="id" value="<?php echo $Correios->id;?>" />
		<div class="panel panel-default">
			<div class="panel-heading panel-store text-uppercase">Configurações - Etiquetas</div>
			<div class="panel-body">
				<div id="formulario" class="formulario row">					
					<div class="col-md-12">
						<label>Produção/Homologação:</label>
						<input type="checkbox" name="setting_mode" id="e_all" value="1" <?php echo  !empty($Correios->setting_mode) ? 'checked' : '' ?> />
						<label for="e_all" class="input-checkbox"></label>
						Selecione o modo p/ Produção/Homologação:
						<hr/>
					</div>
					
					<div class="form-group col-sm-3">
						<label for="" class="">Usuário:</label>
						<input type="text" name="usuario" value="<?php echo  $Correios->usuario; ?>" class="form-control"/>
					</div>
					<div class="form-group col-sm-3">
						<label for="" class="">Senha:</label>
						<input type="password" name="senha" value="<?php echo  $Correios->senha; ?>" class="form-control"/>
					</div>
					<div class="form-group col-sm-4">
						<label for="" class="">Cód. Administrativo:</label>
						<input type="text" name="cod_admin" value="<?php echo  $Correios->cod_admin; ?>" class="form-control"/>
					</div>
					<div class="form-group col-sm-2">
						<label for="" class="">Diretoria:</label>
						<input type="text" name="diretoria" value="<?php echo  $Correios->diretoria; ?>" class="form-control"/>
					</div>
					<div class="form-group col-sm-3">
						<label for="" class="">Nr. Contrato:</label>
						<input type="text" name="nro_contrato" value="<?php echo  $Correios->nro_contrato; ?>" class="form-control"/>
					</div>
					<div class="form-group col-sm-3">
						<label for="" class="">Cartão de Postagem:</label>
						<input type="text" name="cartao_postagem" value="<?php echo  $Correios->cartao_postagem; ?>" class="form-control"/>
					</div>
					<div class="form-group col-sm-6">
						<label for="" class="">CNPJ da Empresa:</label>
						<input type="text" name="cnpj_empresa" value="<?php echo  $Correios->cnpj_empresa; ?>" class="form-control"/>
					</div>
					<div class="col-sm-12 text-center mt10">
						<hr/>
						<button type="submit" class="ml20 btn btn-primary">Salvar Configurações</button>
					</div>
				</div>
			</div>
		</div>
	</form>

	<div class="col-sm-5 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading panel-store text-uppercase">RUBRICA</div>
			<form class="panel-body" action="/adm/correios/correios.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="id" value="<?php echo $Correios->id;?>"/>
				<input type="hidden" name="acao" value="CorreiosLogos" />
				<div id="logo" class="formulario clearfix">
					<div class="row">
						<div class="text-center col-sm-3 col-xs-3">
							<?php if (!empty($Correios->logo_loja) && $Correios->logo_loja != '') { ?>
								<img src="<?php echo  Imgs::src($Correios->logo_loja, 'imgs') ?>" class="img-responsive center-block">
							<?php } else { ?>
								<img src="<?php echo  Imgs::src('logo-etiqueta.png', 'public') ?>" class="img-responsive center-block">
							<?php } ?>
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

	<form class="col-sm-7 col-xs-12" action="/adm/correios/correios.php" method="post">
		<input type="hidden" name="acao" value="CorreiosServicos" />
		<input type="hidden" name="id" value="<?php echo $Correios->id;?>" />
		<div class="panel panel-default">
			<div class="panel-heading panel-store text-uppercase">Tipos de Serviços</div>
			<div class="panel-body">
				<label for="" class="">Serviços para postagem dos objetos:</label>
				<select name="servicos[]" multiple="multiple" size="5" style="width: 100%;">
					<option value="">Selecione uma opção</option>
					<?php
					try {
						// http_response_code(400);
						$acao 	 	 = filter_input(INPUT_GET, 'acao');
						$altura  	 = filter_input(INPUT_GET, 'altura');
						$largura 	 = filter_input(INPUT_GET, 'largura');
						$comprimento = filter_input(INPUT_GET, 'comprimento');
						$peso 		 = filter_input(INPUT_GET, 'peso');
						$cep 		 = filter_input(INPUT_GET, 'cep');

						// Resulta em calcular o frete para o produtos
						if (!empty($acao) && $acao == 'FreteCalcular') :
							$params = new \PhpSigep\Model\CalcPrecoPrazo();

							$params->setAccessData($AccessDataCorreios);
							$params->setCepOrigem($CONFIG['cep']);
							$params->setCepDestino($cep);

							$dimensao = new \PhpSigep\Model\Dimensao();
							$dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);
							$dimensao->setAltura($altura); // em centímetros
							$dimensao->setLargura($largura); // em centímetros
							$dimensao->setComprimento($comprimento); // em centímetros
							
							// Adiciona os dados somente para os correios
							
							$servico_by = null;
							foreach( $ConfiguracoesFretesEnvios->envios_correios as $int ) {
								$servico_by[] = new \PhpSigep\Model\ServicoDePostagem($int);
							}
							
							$params->setAjustarDimensaoMinima(true);
							$params->setServicosPostagem($servico_by);
							$params->setDimensao($dimensao);
							$params->setPeso($peso); // 150 gramas

							$phpSigep = new PhpSigep\Services\SoapClient\Real();
							$result = $phpSigep->calcPrecoPrazo($params);
							
							$array_servicos = [];
							if (!$result->hasError() || ( $result->hasError() && $result->getErrorCode() == 11 ) ) :

								$servicos = $result->getResult();
								foreach ($servicos as $servico) :
									$codigo = trim($servico->getServico()->getCodigo());
									$descricao = trim($servico->getServico()->getNome());
									$valor_br = number_format($servico->getValor(), 2, ',', '.');
									$valor_us = $servico->getValor();
									if ($valor_us > 0) : ?>
										<option value="<?php echo $codigo?>" <?php echo (in_array(sprintf('%s*%s', $codigo, $descricao), $servico_by) ? ' selected':'')?>>
											<?php echo  $descricao ?> - <?php echo  $codigo ?> | Valor R$: <?php echo  $valor_br ?>
										</option>
									<?php
									endif;
								endforeach;
							else :
								
							endif;
						else :

							$phpSigep = new PhpSigep\Services\SoapClient\Real();
							$result = @$phpSigep->buscaCliente($AccessDataCorreios);

							if (!$result->hasError()) :

								$servico_by = [];
								$servico = $Correios->etiquetas_servicos;
								if( count($servico) > 0 )
									foreach( $servico as $arr )
										$servico_by[] = sprintf('%s*%s', $arr->servico_int, $arr->servico_text);
								
								// @var $buscaClienteResult \PhpSigep\Model\BuscaClienteResult 
								$buscaClienteResult = $result->getResult();

								// $resultServicos = Lojas::query('select * from correios_servicos where loja_id=?', [ $CONFIG['loja_id'] ]);
								// $array_servicos = $resultServicos->fetchAll();
								$array_servicos = [];

								// Anula as chancelas antes de imprimir o resultado, porque as chancelas não estão é liguagem humana
								$servicos = $buscaClienteResult->getContratos()->cartoesPostagem->servicos;
								foreach ($servicos as &$servico) :
									$servico->servicoSigep->chancela->chancela = 'Chancelas anulada via código.'; 
									$servico_ai_to_sofrendo = sprintf('%s*%s', trim($servico->codigo), trim($servico->descricao));
									?>
									<option value="<?php echo  $servico_ai_to_sofrendo ?>" <?php echo (in_array($servico_ai_to_sofrendo, $servico_by) ? ' selected':'')?>>
										<?php echo  trim($servico->descricao) ?> - <?php echo  trim($servico->codigo); ?>
									</option>
								<?php
								endforeach;
							endif;
						endif;
					} catch (exception $e) {
						print_r($e);
					}
					?>
				</select>
				<div class="clearfix text-center mt10">
					<hr/>
					<button type="submit" class="ml20 btn btn-primary">Salvar Serviços</button>
				</div>
			</div>
		</div>
	</form>
</div>

<?php ob_start(); ?>
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
