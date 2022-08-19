<?php

$UF = ['AC'=>'Acre', 'AL'=>'Alagoas', 'AM'=>'Amazonas', 'AP'=>'Amapá','BA'=>'Bahia','CE'=>'Ceará','DF'=>'Distrito Federal','ES'=>'Espírito Santo','GO'=>'Goiás','MA'=>'Maranhão','MT'=>'Mato Grosso','MS'=>'Mato Grosso do Sul','MG'=>'Minas Gerais','PA'=>'Pará','PB'=>'Paraíba','PR'=>'Paraná','PE'=>'Pernambuco','PI'=>'Piauí','RJ'=>'Rio de Janeiro','RN'=>'Rio Grande do Norte','RO'=>'Rondônia','RS'=>'Rio Grande do Sul','RR'=>'Roraima','SC'=>'Santa Catarina','SE'=>'Sergipe','SP'=>'São Paulo','TO'=>'Tocantins'];

include '../topo.php';

// Redigitar um caminho com arquivo falso para file_exists
$dir = sprintf('%s/imgs/', URL_VIEWS_BASE_PUBLIC_UPLOAD);
$dir_array = glob(sprintf('%sregras-frete{*.jpg,*.png}', $dir), GLOB_BRACE);
$dir_img_frete = ($dir_array[0]??'false');
$img_frete = ltrim(substr($dir_img_frete, -17), '/');

if( isset($GET['acao']) && $GET['acao'] == 'excluir_imagem' ) {
	$name = pathinfo($GET['img'], PATHINFO_FILENAME);
	$ext = pathinfo($GET['img'], PATHINFO_EXTENSION);
	if( file_exists($GET['img']) && $name == 'regras-frete' )
		unlink($GET['img']);
	
	header('location: /adm/configuracoes/configuracoes-fretes.php');
	return;
}

if( isset($GET['bool']) && $GET['bool'] != '' ) {

	$id = (int)$GET['id'];
	$bool = (int)$GET['bool'];

	ConfiguracoesFretesGratis::action_cadastrar_editar(['ConfiguracoesFretesGratis' => [$id => ['retirada' => $bool]]], 'alterar', 'id');

	header('location: /adm/configuracoes/configuracoes-fretes.php');
	return;
}

if( isset($POST['acao']) && $POST['acao'] == 'ImagemFrete' ) {

	$IMG = current($_FILES);

	if( $IMG['error'] > 0 ) {
		header('location: /adm/configuracoes/configuracoes-fretes.php');
		return;
	}

	$ext = pathinfo($IMG['name'], PATHINFO_EXTENSION);

	$WideImage = WideImage\WideImage::load($IMG['tmp_name']);
	if( $WideImage->getWidth() > 616 || $WideImage->getHeight() > 616 ) {
		$_SESSION['error'] = 'Imagem não atende as dimensões especificadas!';
		header('location: /adm/configuracoes/configuracoes-fretes.php');
		return;
	}

	// Tenta remover a antiga
	if( file_exists($img_frete) )
		unlink($img_frete);

	$filename = sprintf('%s.%s', 'regras-frete', $ext);

	$WideImage->saveToFile( $dir . $filename );
	$WideImage->destroy();

	$_SESSION['error'] = 'Salvo com sucesso!';
	header('location: /adm/configuracoes/configuracoes-fretes.php');
	return;
}


if(is_string($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST' && ($POST['descricao'] != '' || $POST['uf'] != '') ) {
    $id          = is_string($POST['id']) && $POST['id'] != '' ? $POST['id'] : null;
    $uf_array    = is_array($POST['uf']) && $POST['uf'] != '' ? $POST['uf'] : null;
    $descricao   = is_string($POST['descricao']) && $POST['descricao'] != '' ? $POST['descricao'] : '';
    $cep_ini     = is_string($POST['cep_ini']) && $POST['cep_ini'] != '' ? soNumero($POST['cep_ini']) : null;
    $cep_fin     = is_string($POST['cep_fin']) && $POST['cep_fin'] != '' ? soNumero($POST['cep_fin']) : null;
    $dias        = is_string($POST['dias']) && $POST['dias'] != '' ? $POST['dias'] : null;
    $retirada    = is_string($POST['retirada']) && $POST['retirada'] != '' ? $POST['retirada'] : null;
    $frete_valor = is_string($POST['frete_valor']) && $POST['frete_valor'] != '' ? dinheiro($POST['frete_valor']) : null;

	foreach ($uf_array as $uf) 
	{
		if( $id > 0 ) {
			$ConfiguracoesFretesGratis = ConfiguracoesFretesGratis::find($id);
		} else {
			$ConfiguracoesFretesGratis = new ConfiguracoesFretesGratis();
		}

		$ConfiguracoesFretesGratis->uf = $uf;
		$ConfiguracoesFretesGratis->descricao = $descricao;
		$ConfiguracoesFretesGratis->cep_ini = $cep_ini;
		$ConfiguracoesFretesGratis->cep_fin = $cep_fin;
		$ConfiguracoesFretesGratis->frete_valor = $frete_valor;
		$ConfiguracoesFretesGratis->dias = $dias;
		
		if($retirada == 2)
			$ConfiguracoesFretesGratis->retirada = $retirada;
		
		$ConfiguracoesFretesGratis->save_log();
	}

	header('Location: /adm/configuracoes/configuracoes-fretes.php');
	return; 
}

/**
 * Deleta dados em massa.
 */
if( isset( $POST['campos'] ) && count($POST['campos']) > 0 ) 
{
    $c = 0;
    foreach( $POST['campos'] as $key => $value ) {
        if( ConfiguracoesFretesGratis::action_cadastrar_editar(['ConfiguracoesFretesGratis' => [ $value['value'] => [ 'id' => $value['value'] ] ] ], 'delete', 'descricao') ) {
            $c++;
        }
    }
    if( $c > 0 ) {
        header('Location: /adm/configuracoes/configuracoes-fretes.php');
        return;
    }
}


if( isset( $GET['acao'], $GET['id'] ) && ( 'excluir' == $GET['acao'] && '0' < $GET['id'] ) ) 
{
    if( ConfiguracoesFretesGratis::action_cadastrar_editar(['ConfiguracoesFretesGratis' => [ $GET['id'] => [ 'id' => $GET['id'] ] ] ], 'delete', 'descricao') ) {
        header('Location: /adm/configuracoes/configuracoes-fretes.php');
        return;
    }
}

?>
<style>
	body{ background-color: #f1f1f1 }
</style>

	<?php  
	switch ($GET['acao'])
	{
		case 'editar':
		case 'cadastrar':
			$frete_valor = 0;
			$ID = isset($GET['id']) && $GET['id'] != '' ? (INT)$GET['id']: NULL;
			if( $ID > 0 ) {
				$ConfiguracoesFretesGratis = ConfiguracoesFretesGratis::find($ID);
				extract( $ConfiguracoesFretesGratis->to_array() );
			}
			?>
			<form action="/adm/configuracoes/configuracoes-fretes.php" method="post" id="formulario-frete" class="col-lg-8 col-lg-offset-2">

				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase">DESCRIÇÃO</div>
					<div class="panel-body">
						<label>DESCRIÇÃO:</label>
						<input type="text" name="descricao" value="<?php echo $descricao?>" maxlength="255" class="form-control"/>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading panel-store text-uppercase clearfix">
						<span class="pull-left mr15">
							POR ESTADO: 
							<input type="radio" id="somente-estados" value="estado" name="selecao" <?php echo $uf ? ' checked' : 'checked'?>/>
							<label for="somente-estados" class="input-radio" style="color: #fff"></label>
						</span>
						<span class="pull-left">
							POR CEP: 
							<input type="radio" id="somente-cep" value="cep" name="selecao" <?php echo $cep_ini ? ' checked' : ''?>/>
							<label for="somente-cep" class="input-radio" style="color: #fff"></label>
						</span>
						<span class="pull-right">
							FAIXA DE RETIRADA: 
							<input type="checkbox" id="retirar-cep" value="2" name="retirada" <?php echo $retirada == 2 ? ' checked' : ''?>/>
							<label for="retirar-cep" class="input-checkbox" style="color: #fff"></label>
						</span>
					</div>
					<div class="panel-body">
						<div id="faixa-estado" class="form-group">
							<label>Estado</label>
							<select name="uf[]" id="estados" class="form-control"<?php echo ! $id ? ' multiple=""':''?>>
								<option value="">Selecione</option>
								<?php foreach ($UF as $key => $vals) : ?>
									<option value="<?php echo $key?>" <?php echo ($key == $uf ? ' selected':'')?>><?php echo $key . ' - ' . $vals?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div id="faixa-cep" class="row" style="display: none;">
							<span class="form-group col-lg-4 col-xs-12">
								<label>CEP INICIAL:</label>
								<input type="text" name="cep_ini" class="ceps form-control" size="20" tabindex="2" value="<?php echo $cep_ini?>"/>
							</span>
							<span class="form-group col-lg-4 col-xs-12">
								<label>CEP FINAL:</label>
								<input type="text" name="cep_fin" class="ceps form-control" size="20" tabindex="3" value="<?php echo $cep_fin?>"/>
							</span>
							<span class="form-group col-lg-7 col-xs-12" id="faixa-entrega" style="display: <?php echo $retirada != 2 ? 'none' : null?>;">
								<label>Dias para entrega:</label>
								<input type="text" name="dias" class="form-control" tabindex="4" value="<?php echo $dias?>"/>
							</span>
						</div>

						<div class="row">
							<div class="form-group col-lg-3 col-xs-12">
								<label>Valor</label>
								<input type="text" name="frete_valor" size="15" tabindex="4" value="<?php echo number_format($frete_valor,2,',','.')?>" class="text-right preco-mask form-control"/>
							</div>
						</div>
					</div>
				</div>
				<hr/>
				<button type="submit" class="btn btn-primary">salvar</button>
				<a href="/adm/configuracoes/configuracoes-fretes.php" class="btn btn-warning">voltar</a>
				<input type="hidden" value="<?php echo $id?>" name="id"/>
			</form>
			<?php ob_start(); ?>
			<script>
				$("#formulario-frete").on("click", "input[type=radio]", function(e) {
					var elem = $(this).val();
					if(elem === "estado") {
						$("#faixa-cep").fadeOut(10, function(){ 
							<?php if( $id == 0 ) { ?>
							$(this).find("input").val(""); 
							<?php } ?>
						});
						$("#faixa-estado").fadeIn(10);
					} else {
						$("#faixa-estado").fadeOut(10, function(){ 
							<?php if( $id == 0 ) { ?>
							$(this).find("input").val(""); 
							<?php } ?>
						});
						$("#faixa-cep").fadeIn(10);
					}
				});

				$("#formulario-frete").on("click", "input[type=checkbox]", function(e) {
					var elem_checked = $(this).is(":checked"),
						elem_value = $("input[name=selecao]:checked", "#formulario-frete").val();

					if(elem_value === "estado") {
						e.preventDefault();
						alert("Voçe deve selecionar uma faixa de cep");
						return;
					}

					if(!elem_checked) {
						$("#faixa-entrega").fadeOut(10, function(){ $(this).find("input").val(""); });
					} 
					else {
						$("#faixa-entrega").fadeIn(10);
					}
				});

				$("input.ceps").mask("99999-999");
				$("input.preco-mask").mask("#.##0,00", { reverse: true });

				<?php if( $id > 0 ) { ?>
				$("input[type=radio]").trigger("click");
				// $("input[type=checkbox]").trigger("click");
				<?php } ?>
			</script> 
			<?php
			$SCRIPT['script_manual'] .= ob_get_clean();		
		break;
		
		default :
		
			$TOTAL_CADASTROS_GERAL = ConfiguracoesFretesGratis::count(['conditions' => ['loja_id = ?', $CONFIG['loja_id']]]);

			$GET_STATUS = isset($POST['status']) && $POST['status'] != '' ? $POST['status'] : (isset($GET['status']) && $GET['status'] != '' ? $GET['status'] : '');
			$GET_PESQUISAR = isset($GET['pesquisar']) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : (isset($POST['pesquisar']) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '');
			?>
			<div id="div-edicao" class="panel panel-default">
				<div class="panel-heading panel-store text-uppercase">Lista de Fretes <small>gere fretes grátis para algumas regiões ou para um cep específico</small></div>
				<div class="panel-body">
					<table class="table" cellpadding="8" cellspacing="0" width="100%">
						<tbody>
							<tr>
								<td colspan="4">
									<div class="row">
										<div class="col-sm-7">
											<form action="/adm/configuracoes/configuracoes-fretes.php" method="post">
												<div class="clearfix mb15">
													<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_GERAL?></span> fretes cadastrados</span> 
												</div>
												<input name="pesquisar" type="text" class="form-control mr15 pull-left" style="max-width: 350px"/>
												<button type="submit" class="btn btn-primary">
													<i class="fa fa-search"></i>
												</button>
												<a href="/adm/configuracoes/configuracoes-fretes.php?acao=cadastrar" class="btn btn-primary" data-btn="acoes">cadastrar</a>
												<button class="btn btn-danger" type="button" data-id="btn-excluir-varios" data-href="/adm/configuracoes/configuracoes-fretes.php">excluir seleção</button>
											</form>
										</div>
										<div class="col-sm-5" style="background-color: #f1f1f1;">
											<?php if(file_exists(sprintf('%s%s', $dir, $img_frete))) { ?>
											<img src="<?php echo Imgs::src($img_frete, 'imgs')?>" width="185px" style="width: 185px;" class="mb15 mt15 center-block">
											<a href="/adm/configuracoes/configuracoes-fretes.php?acao=excluir_imagem&img=<?php echo sprintf('%s%s', $dir, $img_frete)?>"  class="btn btn-xs btn-danger" style="position: absolute; top: 0; right: 0; margin: 15px; z-index: 4;">excluir imagem</a>
											<?php } ?>
											<form action="/adm/configuracoes/configuracoes-fretes.php" class="text-center" method="post" id="frete-image" enctype="multipart/form-data">
												<input name="acao" type="hidden" style="display: none;" value="ImagemFrete">
												<input name="input-file" type="file" style="display: none;" id="input-file">
												<label for="input-file" class="btn btn-info btn-xs"><i class="fa fa-image"></i> adicionar/editar uma imagem</label>
												<button type="submit" class="hidden"></button>
											</form>
											<small class="show">Adicione uma imagem personalizada para mostrar os fretes grátis para seus clientes<br/>NOTA: A imagem deve ter sua dimensão de 616x281</small>
										</div>
									</div>
								</td>
							</tr>
							<tr class="plano-fundo-adm-003 ocultar">
								<td bgcolor="#ffffff" align="center" nowrap="nowrap" width="1%">
									<input type="checkbox" name="selecionados-exclusao-all" class="selecionados-exclusao-all" id="label" value=""/>
									<label for="label" class="input-checkbox"></label>
								</td>
								<td>Descrição</td>
								<td align="center" nowrap="nowrap" width="1%">Retirar em mãos</td>
								<td align="center">Valor</td>
								<td align="center">Ações</td>
							</tr>
							<?php
							if( isset( $GET_PESQUISAR ) && $GET_PESQUISAR != '' ) {
								$sql = 'SELECT * FROM configuracoes_fretes_gratis WHERE (descricao like ? OR (uf like ?)) and loja_id = ? ORDER BY id DESC';
								$sql_array = [ "%{$GET_PESQUISAR}%", "%{$GET_PESQUISAR}%", $CONFIG['loja_id'] ];
							} else {
								$sql = 'SELECT * FROM configuracoes_fretes_gratis WHERE loja_id = ? ORDER BY id DESC';
								$sql_array = [ $CONFIG['loja_id'] ];
							}
							
							$result = ConfiguracoesFretesGratis::find_by_sql($sql, $sql_array);
							
							if( count( $result ) > 0 ) 
							{
								foreach( $result as $rs ) {
									extract( $rs->to_array() );
									$cep_ini = wordwrap($cep_ini, 5, '-', true);
									$cep_fin = wordwrap($cep_fin, 5, '-', true);
									?>
									<tr class="lista-zebrada in-hover">
										<td nowrap="nowrap" width="1%">
											<input type="checkbox" name="selecionados-exclusao" id="label<?php echo $id?>" value="<?php echo $id?>"/>
											<label for="label<?php echo $id?>" class="input-checkbox"></label>
										</td>
										<td align="left">
											<?php echo sprintf('%s: %s%s', $descricao, ( ! empty( $cep_ini ) ? $cep_ini . '/' : '' ), ( ! empty( $cep_fin ) ? $cep_fin : '' )); ?>
											<?php echo sprintf('%s', ( ! empty( $uf ) ? ' - ' . $uf : '' )); ?>
										</td>
										<td align="center" nowrap="nowrap" width="1%" class="ft18px">
											<?php if(in_array($retirada,  ['0', '1'])) { ?>
											<a href="/adm/configuracoes/configuracoes-fretes.php?id=<?php echo $id?>&bool=<?php echo $retirada == 0 ? 1:0?>" class="btn btn-xs btn-<?php echo $retirada == 0 ? 'primary':'success'?>">
												<?php echo $retirada == 0 ? 'não':'sim'?>
											</a>
											<?php } ?>
										</td>
										<td align="center" nowrap="nowrap" width="1%" class="ft18px">
											R$: <?php echo number_format($frete_valor,2,',','.');?>
										</td>
										<td align="center" nowrap="nowrap" width="1%">
											<a href="/adm/configuracoes/configuracoes-fretes.php?acao=editar&id=<?php echo $id?>" class="btn btn-primary btn-sm">editar</a>
											<a href="/adm/configuracoes/configuracoes-fretes.php?acao=excluir&id=<?php echo $id?>" class="btn btn-danger btn-sm">excluir</a>
										</td>
									</tr>
								<?php } 
							} ?>
						</tbody>
					</table>
				</div>
			</div>
			<?php
			break;
		}
		?>
<?php ob_start(); ?>
<script>

	/**
     * Envia uma foto para upload
     */
	$("#div-edicao").on("change", "input[type=file]", function() {
		// valida a extensao da imagem
		if( !validaExtensao(  this.id  ) ) { 
			return false;
		}
		
		var FormFoto = $(this).parents(), 
			FormAction = $(FormFoto).attr("action"),
			FormDataSerialize = $(FormFoto).serializeArray();

			// console.log(FormDataSerialize);
			// return false;
			
		$(FormFoto).ajaxSubmit({
			url: FormAction,
			data: FormDataSerialize,
			resetForm: true,
			cache: false,
			type: "post",
			uploadProgress: function(event,position,total,percentComplete){
				$("#status-alteracao").fadeIn(0).html('Enviando imagem '+percentComplete+'%');
			},
			success: function( str ) {
				var list = $("<div/>", { html: str });
				$("#div-edicao").html( list.find("#div-edicao").html() );
			},
			complete: function() {
			},
			error: function(x,t,m){ 
				alert("Não consegui enviar a imagem!\nTente recarregar a página.");
				console.log( x.responseText + '\n' + t + '\n' + m ); 
			}
		});
	});

	$(document).on("click", "a", function(){
		var href = this.href || e.target.href;		
		if( href.search('excluir') > '0')
			if( ! confirm("Deseja realmente excluir!") ) return false;
	
	});
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();
include '../rodape.php';