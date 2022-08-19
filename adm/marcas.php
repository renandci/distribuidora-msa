<?php
include 'topo.php';

/**
 * Cadastra
 */
if( isset($_GET['acao']) && $_GET['acao'] === 'cadastrar' ) {
	
	$filename = null;
	$imagem = ! empty( $_FILES ) ? current( $_FILES ) : [];
	
	if( $imagem['size'] > 0 ) {
		
		$dirname = '../' . URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/brands/';
		if( is_dir($dirname) === false ) {
			if ( ! mkdir( $dirname, 0, true ) ) {
				
			}
		}
		
		$ext = pathinfo($imagem['name'], PATHINFO_EXTENSION);
		$basename = sprintf('%s', time()); 
		$filename = sprintf('%s.%0.8s', $basename, $ext);

		$WideImageTmp = WideImage\WideImage::load($imagem['tmp_name']);
		$WideImage140x140 = $WideImageTmp->resize(145, 145);
		
		$WideSquare = WideImage\WideImage::load('../public/imgs/_quadro.jpg');
		$WideSquare140x140 = $WideSquare->resize(145, 145);
		
		$WideSquare140x140->merge($WideImage140x140, 'center', 'center', 95)->asGrayscale()->saveToFile($dirname . $filename);	
		
		$WideSquare->destroy();
		$WideImageTmp->destroy();
		
	}
	
	$POST['Marcas'][0]['imagem'] = $filename;
	
    Marcas::action_cadastrar_editar($POST, 'cadastrar', 'marcas');
    header('Location: /adm/marcas.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Editar
 */
if( isset($GET['acao']) && $GET['acao'] === 'editar' ) {
	
	$filename = null;
	$imagem = ! empty( $_FILES ) ? current( $_FILES ) : [];
	
	if( $imagem['size'] > 0 ) {
		
		$id = (int)filter_input(INPUT_POST, 'id');
		
		$rws = Marcas::find($id);
		
		$dirname = '../' . URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/brands/';
		if( is_dir($dirname) === false ) {
			if ( ! mkdir( $dirname, 0, true ) ) {		
			}
		}
		
		$imagem['name'] = !empty($rws->imagem) ? $rws->imagem : $imagem['name'];
		
		$ext = pathinfo($imagem['name'], PATHINFO_EXTENSION);
		$basename = sprintf('%s', time()); 
		$filename = !empty($rws->imagem) ? $rws->imagem : sprintf('%s.%0.8s', $basename, $ext);

		$WideImageTmp = WideImage\WideImage::load($imagem['tmp_name']);
		$WideImage140x140 = $WideImageTmp->resize(145, 145);
		
		$WideSquare = WideImage\WideImage::load('../public/imgs/_quadro.jpg');
		$WideSquare140x140 = $WideSquare->resize(145, 145);
		
		$WideSquare140x140->merge($WideImage140x140, 'center', 'center', 95)->asGrayscale()->saveToFile($dirname . $filename);	
		
		$WideSquare->destroy();
		$WideImageTmp->destroy();
		
		$POST['Marcas'][$id]['imagem'] = $filename;
	}
	
    Marcas::action_cadastrar_editar($POST, 'alterar', 'marcas');
    header('Location: /adm/marcas.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Excluir
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir' ) {
    Marcas::action_cadastrar_editar([ 'Marcas' => [ $GET['id'] => ['excluir' => 1] ] ], 'excluir', 'marcas');
    header('Location: /adm/marcas.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Excluir Imagem
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir_img' ) {
	
	$rws = Marcas::find((int)$GET['id']);
	
	if( ! empty( $rws->id ) ) {
		$dirname = '../' . URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/brands';
		array_map('unlink', [ implode('/', [$dirname, $rws->imagem])]);
	}
	
    Marcas::action_cadastrar_editar([ 'Marcas' => [ $GET['id'] => ['imagem' => null] ] ], 'alterar', 'marcas');
    header('Location: /adm/marcas.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Remover em massa
 */
if( count( $POST['Marcas'] ) > 0 ) {
    Marcas::action_cadastrar_editar($POST, 'excluir', 'marcas');
    header('Location: /adm/marcas.php?codigo_id=' . $GET['codigo_id']);
    return;
}


$TOTAL_CADASTROS_GERAL = Marcas::count();
$TOTAL_CADASTROS_ATIVOS = Marcas::count(['conditions'=>['excluir=?', 0]]);
$TOTAL_CADASTROS_DESATIVOS = Marcas::count(['conditions'=>['excluir=?', 1]]);

$GET_STATUS = isset( $POST['status'] ) && $POST['status'] != '' ? $POST['status'] : ( isset( $GET['status'] ) && $GET['status'] != '' ? $GET['status'] : '' );
$GET_PESQUISAR = isset( $GET['pesquisar'] ) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : ( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '' );
?>
<div class="panel panel-default">
    <div class="panel-heading panel-store text-uppercase ocultar">MARCAS</div>
	<div id="div-edicao" class="panel-body">
		<style>	
            body{ background-color: #f1f1f1 }
			.ocultos{ display: none; }
		</style>
		<table width="100%" border="0" cellpadding="8" cellspacing="0" id="tabela-marcas">
			<tbody>
				<tr class="ocultar">
					<td colspan="4">
						<form action="/adm/marcas.php?codigo_id=<?php echo $GET['codigo_id']?>" method="post" class="formulario-marcas">
							<div class="clearfix mb15" style="line-height: 17px;">
								<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> marcas cadastradas</span>
							</div>
							<input name="pesquisar" type="text" class="w50"/>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
							<button class="btn btn-primary" type="button" <?php echo _P('marcas', $_SESSION['admin']['id_usuario'], 'incluir')?> onclick="$('#formulario').slideToggle(0);">
                                cadastrar
                            </button>
							<button class="btn btn-danger" type="button" data-action="btn-excluir-varios" data-href="/adm/marcas.php?codigo_id=<?php echo $GET['codigo_id']?>" <?php echo _P( 'marcas', $_SESSION['admin']['id_usuario'], 'excluir' )?>>
                                excluir seleção
                            </button>
						</form>
					</td>
				</tr>
				
				<tr id="formulario" class="ocultos ocultar">
					<td colspan="4">
                        <form class="formulario-marcas" action="/adm/marcas.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=cadastrar" method="post" enctype="multipart/form-data">
							<div class="clearfix fieldset mt15 mb15">
								<div class="col-lg-4">
									<p>Nome da marca:</p>
									<input type="text" value="" name="Marcas[0][marcas]" class="w100"/>
								</div>
                                <div class="row mb15"></div>
								<div class="col-lg-8">
									<p>Disponibilidade de postagem:</p>
									<input type="text" value="" name="Marcas[0][disponib_entrega]" class="w100"/>
								</div>
								<div class="col-lg-8 mt15">
									<div class="row">
										<div class="col-md-3">
											<img src="<?php echo Imgs::src('sem-foto-produto.gif', 'imgs')?>" class="img-responsive" id="imagem">
										</div>
										<div class="col-md-9">
											<p>Imagem de icone (opcional):</p>
											<input type="file" value="" name="imagem" class="w100" onchange="$(this).preview_img({img: 'imagem', width: 155})"/>
										</div>
									</div>
								</div>
								<div class="col-lg-12 mt15">
                                    <button type="submit" class="btn btn-primary btn-cadastros-marcas" <?php echo _P('marcas', $_SESSION['admin']['id_usuario'], 'alterar|incluir')?>>
                                        salvar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="$('#formulario').slideToggle(0);">
										cancela
									</button>
								</div>
							</div>
						</form>
					</td>
				</tr>
			
				<tr class="plano-fundo-adm-003 ocultar">
					<td bgcolor="#ffffff" align="center" nowrap="nowrap" width="1%">
						<input type="checkbox" data-action="selecionados-exclusao-all" class="selecionados-exclusao-all" id="label" value=""/>
						<label for="label" class="input-checkbox"></label>
					</td>
					<td>Icon</td>
					<td>Marca</td>
					<td align='center'>Ações</td>
				</tr>
				
				<?php
				$i = 0;
				
				$maximo = 25;
				
				$pag = isset( $GET['pag'] ) && $GET['pag'] != '' ? $GET['pag'] : 1;
				
				$inicio = (($pag * $maximo) - $maximo);
				
				$conditions = array();
				
				// $conditions['select'] = sprintf('marcas.*, (SELECT 1 FROM produtos WHERE produtos.codigo_id=%u and produtos.id_marca = marcas.id) as test', (int)$GET['codigo_id']);

				$conditions['conditions'] = sprintf('marcas.excluir = 0 and marcas.loja_id=%u ', $CONFIG['loja_id']);
				
				$conditions['conditions'] .= isset( $GET_PESQUISAR ) && $GET_PESQUISAR != '' ? sprintf('and marcas.marcas like "%%%s%%" ', addslashes($GET_PESQUISAR)) : '';
					
				// $conditions['conditions'] .= isset( $GET['codigo_id'] ) && $GET['codigo_id'] > 0 ? sprintf(' AND id NOT IN(SELECT produtos.id_marca FROM produtos WHERE produtos.codigo_id=%u AND produtos.excluir = 0) ', (int)$GET['codigo_id']) : '';
				
				$total = ceil(Marcas::count($conditions) / $maximo);
				
				$conditions['order'] = 'marcas.marcas asc';
				
				$conditions['limit'] = $maximo;
				
				$conditions['offset'] = ($maximo * ($pag - 1));
				
				$result = Marcas::all( $conditions );
				
				foreach ( $result as $rs ) { ?>
				<tr class="lista-zebrada in-hover formulario<?php echo $rs->id;?> ocultar" <?php echo ($i % 2) ? 'style="background-color:#f3f3f3"': ''?>>
					<td nowrap="nowrap" width="1%">
                        <input type="checkbox" name="Marcas[<?php echo $rs->id;?>][excluir]" id="label<?php echo $rs->id?>" value="1" data-action="selecionados-exclusao"/>
						<label for="label<?php echo $rs->id?>" class="input-checkbox"></label>
					</td>
					<td nowrap="nowrap" width="1%">
						<img src="<?php echo Imgs::src((!empty($rs->imagem) ? sprintf('brands/%s', $rs->imagem):'sem-foto-produto.gif'), 'imgs')?>" width="75px">
					</td>
					<td>
						<?php echo $rs->marcas?>
						<?php echo !empty($GET['id_marca'])  && $GET['id_marca'] == $rs->id ? '<span class="pull-right btn btn-info btn-xs ft10px">adicionado</span>': null?>
					</td>
					<td align="center" nowrap="nowrap" width="1%">
						<a href="javascript: void(0);" class="btn btn-primary btn-sm" onclick="$('#tabela-marcas #formulario<?php echo $rs->id?>').slideToggle(0);" <?php echo _P('marcas', $_SESSION['admin']['id_usuario'], 'alterar')?>>editar</a> 
						<a href="/adm/marcas.php?codigo_id=<?php echo $GET['codigo_id']?>&id=<?php echo $rs->id?>&acao=excluir" class="btn btn-danger btn-sm btn-excluir-modal" <?php echo _P('marcas', $_SESSION['admin']['id_usuario'], 'excluir')?>>excluir</a>
					</td>
				</tr>
				<tr class="formulario<?php echo $rs->id;?> ocultos" id="formulario<?php echo $rs->id;?>">
					<td colspan="4">
						<form class="formulario-marcas col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-1 fieldset" action="/adm/marcas.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=editar" method="post" enctype="multipart/form-data">
							<div class="clearfix fieldset mt15 mb15">
								<div class="col-lg-4">
									<p>Nome da marca:</p>
									<input type="text" value="<?php echo $rs->marcas;?>" name="Marcas[<?php echo $rs->id;?>][marcas]" class="w100"/>
								</div>
                                <div class="row mb15"></div>
								<div class="col-lg-8">
									<p>Disponibilidade de postagem:</p>
									<input type="text" value="<?php echo $rs->disponib_entrega;?>" name="Marcas[<?php echo $rs->id;?>][disponib_entrega]" class="w100"/>
								</div>
								<div class="col-lg-8 mt15">
									<div class="row">
										<div class="col-md-3">
											<a href="/adm/marcas.php?acao=excluir_img&id=<?php echo $rs->id?>" class="btn-excluir-modal fa fa-trash" style="position:absolute;top:0;right:0;"></a>
											<img src="<?php echo Imgs::src((!empty($rs->imagem) ? sprintf('brands/%s', $rs->imagem):'sem-foto-produto.gif'), 'imgs')?>" class="img-responsive" id="img_<?php echo $rs->id;?>">
										</div>
										<div class="col-md-9">
											<p>Imagem de icone (opcional):</p>
											<input type="file" value="" name="imagem" class="w100"  onchange="$(this).preview_img({img: 'img_<?php echo $rs->id;?>', width: 155})"/>
											<input type="hidden" value="<?php echo $rs->id;?>" name="id"/>
										</div>
									</div>
								</div>
								<div class="col-lg-12 mt15">
                                    <button type="submit" class="btn btn-primary btn-cadastros-marcas" <?php echo _P('marcas', $_SESSION['admin']['id_usuario'], 'alterar|incluir')?>>
                                        salvar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="$('#tabela-marcas #formulario<?php echo $rs->id;?>').slideToggle(0);">cancela</button>
								</div>
							</div>
						</form>
					</td>
				</tr>
				<?php ++$i; } ?>
				<tr class="ocultar">
					<td colspan="4">
						<div class="paginacao paginacao-add">
							<?php
							if( $total > 0 )
							{
								for( $i = $pag - 5, $limiteDeLinks = $i + 10; $i <= $limiteDeLinks; ++$i )
								{
									if($i < 1) {
										$i = 1;
										$limiteDeLinks = 9;
									}
								
									if($limiteDeLinks > $total) {
										$limiteDeLinks = $total; 
										$i = $limiteDeLinks - 10;
									}

									if($i < 1) {
										$i = 1;
										$limiteDeLinks = $total;
									}
									
									if($i == $pag) {
										echo "<span class=\"at plano-fundo-adm-001\">{$i}</span>";
									}
									else {
										$data = http_build_query(array_replace($GET, ['pag' => $i]));
										echo sprintf('<a href="/adm/marcas.php?%s" class="btn-paginacao">%s</a>', $data, $i);
									}
								}
							}
							?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<script>
	$(document).on("click", "a", function(){
		var href = this.href || e.target.href;		
		if( href.search('excluir') > '0' || href.search('excluir_img') > '0' )
			if( ! confirm("Deseja realmente excluir!") ) return false;
	
	});
</script>
<?php
include 'rodape.php';