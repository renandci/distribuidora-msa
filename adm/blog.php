<?php
include 'topo.php';

$dirname = '../' . URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/blog/';

/**
 * Cadastra
 */
if( isset($_GET['acao']) && $_GET['acao'] === 'cadastrar' ) {
	
	$filename = null;
	$imagem = ! empty( $_FILES ) ? current( $_FILES ) : [];
	
	$Blog = Blog::action_cadastrar_editar($POST, 'cadastrar', 'blog');

	// Pega a primeira ocorrencia do files
	if( $imagem['size'][0] > 0 ) {
		
		$id = (int)$Blog['id'];
		
		if( is_dir($dirname) === false ) {
			if ( ! mkdir( $dirname, 0, true ) ) {		
			}
		}
		
		$ImgCount = count($imagem['size']);
		for($i = 0; $i < $ImgCount; $i++ ) 
		{
			$ext = pathinfo($imagem['name'][$i], PATHINFO_EXTENSION);
			$basename = sprintf('%s', ($i + 1 * time())); 
			$filename = sprintf('%s.%0.8s', $basename, $ext);
			
			$WideImageTmp = WideImage\WideImage::load($imagem['tmp_name'][$i]);
			$WideImage140x140 = $WideImageTmp->resize(411, 276);
			
			$WideSquare = WideImage\WideImage::load( $dirname . 'quadro-284-316.jpg' );
			$WideSquare140x140 = $WideSquare->resize(411, 276);

			// // imagem com escala com cores cinzas
			// $WideSquare140x140->merge($WideImage140x140, 'center', 'center', 95)->asGrayscale()->saveToFile($dirname . $filename);	
			$WideSquare140x140->merge($WideImage140x140, 'center', 'center', 95)->saveToFile($dirname . $filename);	
			
			$BlogImagens = new BlogImagens();
			$BlogImagens->id_blog = $id;
			$BlogImagens->imagem = $filename;
			$BlogImagens->save();

			$WideSquare140x140->destroy();
			$WideImageTmp->destroy();
			$WideSquare->destroy();
		}
	}
	
    header('Location: /adm/blog.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Editar
 */
if( isset($GET['acao']) && $GET['acao'] === 'editar' ) {
	
	$filename = null;
	$imagem = ! empty( $_FILES ) ? current( $_FILES ) : [];

	// Pega a primeira ocorrencia do files
	if( $imagem['size'][0] > 0 ) {
		
		$id = (int)filter_input(INPUT_POST, 'id');
		
		// $rws = Blog::find($id);
		
		if( is_dir($dirname) === false ) {
			if ( ! mkdir( $dirname, 0, true ) ) {		
			}
		}
		
		// busca todas as imagens em cadastro tentando deletar
		$BlogImagens = BlogImagens::all(['conditions'=>['id_blog=?', $id]]);
		$BlogImagensCount = count($BlogImagens);
		if($BlogImagensCount > 0) {
			
			$imgs_del = [];
			
			foreach($BlogImagens as $loop) {
				$imgs_del[] = $dirname . $loop->imagem;
				$loop->delete();
			}

			// tenta remover as imagens
			if( file_exists( $dirname ) )
				array_map('unlink', $imgs_del);
		}
		
		$ImgCount = count($imagem['size']);
		for($i = 0; $i < $ImgCount; $i++ ) 
		{
			$ext = pathinfo($imagem['name'][$i], PATHINFO_EXTENSION);
			$basename = sprintf('%s', ($i + 1 * time())); 
			$filename = sprintf('%s.%0.8s', $basename, $ext);
			
			$WideImageTmp = WideImage\WideImage::load($imagem['tmp_name'][$i]);
			$WideImage140x140 = $WideImageTmp->resize(411, 276);
			
			$WideSquare = WideImage\WideImage::load( $dirname . 'quadro-284-316.jpg' );
			$WideSquare140x140 = $WideSquare->resize(411, 276);

			// // imagem com escala com cores cinzas
			// $WideSquare140x140->merge($WideImage140x140, 'center', 'center', 95)->asGrayscale()->saveToFile($dirname . $filename);	
			$WideSquare140x140->merge($WideImage140x140, 'center', 'center', 95)->saveToFile($dirname . $filename);	
			
			$BlogImagens = new BlogImagens();
			$BlogImagens->id_blog = $id;
			$BlogImagens->imagem = $filename;
			$BlogImagens->save();

			$WideSquare140x140->destroy();
			$WideImageTmp->destroy();
			$WideSquare->destroy();
		}
	}
	
    Blog::action_cadastrar_editar($POST, 'alterar', 'blog');
    header('Location: /adm/blog.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Excluir
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir' ) {
    Blog::action_cadastrar_editar([ 'Blog' => [ $GET['id'] => ['excluir' => 1] ] ], 'excluir', 'blog');
    header('Location: /adm/blog.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Excluir Imagem
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir_img' ) {
	
	$rws = Blog::find((int)$GET['id']);
	
	if( ! empty( $rws->id ) ) {
		$dirname = '../' . URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/blog';
		array_map('unlink', [ implode('/', [$dirname, $rws->imagem])]);
	}
	
    Blog::action_cadastrar_editar([ 'Blog' => [ $GET['id'] => ['imagem' => null] ] ], 'alterar', 'blog');
    header('Location: /adm/blog.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Remover em massa
 */
if( count( $POST['Blog'] ) > 0 ) {
    Blog::action_cadastrar_editar($POST, 'excluir', 'blog');
    header('Location: /adm/blog.php?codigo_id=' . $GET['codigo_id']);
    return;
}


$TOTAL_CADASTROS_GERAL = Blog::count();
$TOTAL_CADASTROS_ATIVOS = Blog::count(['conditions'=>['excluir=?', 0]]);
$TOTAL_CADASTROS_DESATIVOS = Blog::count(['conditions'=>['excluir=?', 1]]);

$GET_STATUS = isset( $POST['status'] ) && $POST['status'] != '' ? $POST['status'] : ( isset( $GET['status'] ) && $GET['status'] != '' ? $GET['status'] : '' );
$GET_PESQUISAR = isset( $GET['pesquisar'] ) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : ( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '' );
?>
<div class="tag-opcoes clearfix">
	<h2>Blog</h2>
	<div id="div-edicao">
		<style>
			.ocultos{
				display: none;
			}
			.img-preview {
				display: block;
				height: auto;
				width: 75px;
				position: relative;
				float: left;
				margin: 5px;
				z-index: 0;
				font-family: 'FontAwesome';
				text-align: center;
				font-size: 30px;
				color: #000;
			}
			.img-preview::after {
				content: '\f1f8';
				width: 100%;
				height: 100%;
				background-color: transparent;
				background-image: url(<?php echo Imgs::src('overlay-box.png', 'public')?>);
				position: absolute;
				top: 0;
				left: 0;
				margin: 0;
				z-index: 1;
				display: none;
			}
			/* .img-preview:hover::after {
				display: block;
			} */
		</style>
		<table width="100%" border="0" cellpadding="8" cellspacing="0" id="tabela-blog">
			<tbody>
				<tr class="ocultar">
					<td colspan="4">
						<form action="/adm/blog.php?codigo_id=<?php echo $GET['codigo_id']?>" method="post" class="formulario-blog">
							<div class="clearfix mb15" style="line-height: 17px;">
								<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> blog cadastradas</span>
							</div>
							<input name="pesquisar" type="text" class="w50"/>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
							<button class="btn btn-primary btn-cad-or-edit" type="button" <?php echo _P('blog', $_SESSION['admin']['id_usuario'], 'incluir')?> onclick="$('#formulario').slideToggle(0);">
                                cadastrar
                            </button>
							<button class="btn btn-danger" type="button" data-action="btn-excluir-varios" data-href="/adm/blog.php?codigo_id=<?php echo $GET['codigo_id']?>" <?php echo _P('blog', $_SESSION['admin']['id_usuario'], 'excluir')?>>
                                excluir seleção
                            </button>
						</form>
					</td>
				</tr>
				
				<tr id="formulario" class="ocultos ocultar">
					<td colspan="4">
                        <form class="formulario-blog fieldset col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-1" action="/adm/blog.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=cadastrar" method="post" enctype="multipart/form-data">
							<div class="clearfix mt15 mb15">
								<div class="col-lg-5">
									<p>Titulo:</p>
									<input type="text" name="Blog[0][titulo]" class="w100"/>
								</div>
                                <div class="row mb15"></div>
								<div class="col-lg-12 mt15">
									<div class="row">
										<div class="col-md-12">
											<!-- <img src="<?php echo Imgs::src('sem-foto-produto.gif', 'imgs')?>" class="img-responsive" id="imagem"> -->
										</div>
										<div class="col-md-12">
											<p>Imagem de icone (opcional):</p>
											<input type="file" name="imagem[]" multiple="multiple" class="w100" onchange="$(this).preview_img_clone(event.target.files, {width: 75})"/>
										</div>
									</div>
								</div>
								<div class="col-lg-12">
									<p>Descrição:</p>
									<textarea name="Blog[0][descricao]" class="descricoes" rows="7" style="height: 445px;"></textarea>
								</div>
								<div class="col-lg-12 mt15">
                                    <button type="submit" class="btn btn-primary btn-cadastros-blog" <?php echo _P('blog', $_SESSION['admin']['id_usuario'], 'alterar|incluir')?>>
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
					<!-- <td>Icon</td> -->
					<td>Titulo</td>
					<td align='center'>Ações</td>
				</tr>
				
				<?php
				$i = 0;
				
				$maximo = 25;
				
				$pag = isset( $GET['pag'] ) && $GET['pag'] != '' ? $GET['pag'] : 1;
				
				$inicio = (($pag * $maximo) - $maximo);
				
				$conditions = array();
				
				$conditions['conditions'] = sprintf('excluir = 0 and loja_id=%u', $CONFIG['loja_id']);
				
				$conditions['conditions'] .= isset($GET_PESQUISAR) && $GET_PESQUISAR != '' ? sprintf(' and titulo like "%%%s%%" ', addslashes($GET_PESQUISAR)) : '';
				
				$total = ceil(Blog::count($conditions) / $maximo);
				
				$conditions['order'] = 'titulo asc';
				
				$conditions['limit'] = $maximo;
				
				$conditions['offset'] = ($maximo * ($pag - 1));
				
				$result = Blog::all($conditions);
				
				foreach ( $result as $rs ) { ?>
				<tr class="lista-zebrada in-hover formulario<?php echo $rs->id;?> ocultar" <?php echo ($i % 2) ? 'style="background-color:#f3f3f3"': ''?>>
					<td nowrap="nowrap" width="1%">
                        <input type="checkbox" name="Blog[<?php echo $rs->id;?>][excluir]" id="label<?php echo $rs->id?>" value="1" data-action="selecionados-exclusao"/>
						<label for="label<?php echo $rs->id?>" class="input-checkbox"></label>
					</td>
					<!-- <td nowrap="nowrap" width="1%">
						<?php foreach($rs->blogimg as $img) { ?>
						<img src="<?php echo Imgs::src((!empty($rs->imagem) ? sprintf('blog/%s', $rs->imagem):'sem-foto-produto.gif'), 'imgs')?>" width="75px">
						<img src="<?php echo Imgs::src(sprintf('blog/%s', $img->imagem), 'imgs')?>" width="75px">
						<?php } ?>
					</td> -->
					
					<td>
						<?php echo $rs->titulo?>
						<div class="mt5 clearfix">
							<?php foreach($rs->blogimg as $img) { ?>
								<a href="javascript: void(0)" class="img-preview btn-excluir-modal">
									<img src="<?php echo Imgs::src(sprintf('blog/%s', $img->imagem), 'imgs')?>" width="75px">
								</a>
							<?php } ?>
						</div>
					</td>

					<td align="center" nowrap="nowrap" width="1%">
						<a href="javascript: void(0);" class="btn btn-primary btn-sm btn-cad-or-edit" onclick="$('#tabela-blog #formulario<?php echo $rs->id?>').slideToggle(0);" <?php echo _P('blog', $_SESSION['admin']['id_usuario'], 'alterar')?>>editar</a> 
						<a href="/adm/blog.php?codigo_id=<?php echo $GET['codigo_id']?>&id=<?php echo $rs->id?>&acao=excluir" class="btn btn-danger btn-sm btn-excluir-modal" <?php echo _P('blog', $_SESSION['admin']['id_usuario'], 'excluir')?>>excluir</a>
					</td>
				</tr>
				<tr class="formulario<?php echo $rs->id;?> ocultos" id="formulario<?php echo $rs->id;?>">
					<td colspan="4">
						<form class="formulario-blog col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-1 fieldset" action="/adm/blog.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=editar" method="post" enctype="multipart/form-data">
							<div class="clearfix mt15 mb15">
								<div class="col-lg-5">
									<p>Título:</p>
									<input type="text" value="<?php echo $rs->titulo;?>" name="Blog[<?php echo $rs->id;?>][titulo]" class="w100"/>
								</div>
                                <div class="row mb15"></div>
								<div class="col-lg-8 mb15">
									<div class="row">
										<div class="clearfix" id="img_preview<?php echo $rs->id?>">
											<!-- <?php foreach($rs->blogimg as $img) { ?>
												<div class="col-md-2">
													<a href="/adm/blog.php?acao=excluir_img&id=<?php echo $img->id?>" class="btn-excluir-modal fa fa-trash" style="position:absolute;top:0;right:0;"></a>
													<img src="<?php echo Imgs::src((!empty($img->imagem) ? sprintf('blog/%s', $img->imagem):'sem-foto-produto.gif'), 'imgs')?>" class="img-responsive" id="img_<?php echo $img->id;?>">
												</div>
											<?php } ?> -->
										</div>
										<div class="col-md-12">
											<p>Imagem de icone (opcional):</p>
											<input type="file" name="imagem[]" multiple="multiple" class="w100" onchange="$(this).preview_img_clone(event.target.files, {width: 75})"/>
											<input type="hidden" value="<?php echo $rs->id;?>" name="id"/>
										</div>
									</div>
								</div>
								<div class="col-lg-12">
									<p>Descrição:</p>
									<textarea name="Blog[<?php echo $rs->id;?>][descricao]" class="descricoes" rows="7" style="height: 445px"><?php echo $rs->descricao;?></textarea>
								</div>
								<div class="col-lg-12 mt15">
                                    <button type="submit" class="btn btn-primary btn-cadastros-blog" <?php echo _P('blog', $_SESSION['admin']['id_usuario'], 'alterar|incluir')?>>
                                        salvar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="$('#tabela-blog #formulario<?php echo $rs->id;?>').slideToggle(0);">cancela</button>
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
										echo sprintf('<a href="/adm/blog.php?%s" class="btn-paginacao">%s</a>', $data, $i);
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
<?php ob_start(); ?>
<script>
	$(document).on("click", ".btn-cad-or-edit", function(){
		
		// Se tiver instancia, pare o codigo
		if(typeof(tinyMCE) !== 'undefined') {
			var length = tinyMCE.editors.length;
			for (var i=length; i>0; i--) {
				return false;
			};
		};

		$("textarea[name]").tinymce({
			entity_encoding: "raw",
			language: "pt_BR",
			// selector: "textarea.descricoes",
			toolbar_items_size: "small",
			menubar: false,
			toolbar1: "newdocument cut copy paste | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
			toolbar2: "undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | forecolor backcolor | insertdatetime preview",
			plugins: [
				"advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
				"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
				"table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern"
			],
			paste_data_images: true,
			image_advtab: true,
			image_title: true, 
			relative_urls : false,
			remove_script_host : false,
			convert_urls : true,
			// enable automatic uploads of images represented by blob or data URIs
			automatic_uploads: true,
			// URL of our upload handler (for more details check: https://www.tinymce.com/docs/configure/file-image-upload/#images_upload_url)
			images_upload_url: "<?php echo URL_BASE?>public/imgs/tiny-mce/uploads.php",
			images_upload_base_path: "/public/imgs/tiny-mce/",
			// here we add custom filepicker only to Image dialog
			file_picker_types: "image", 
			image_list: [
				<?php
				$js = '';
				foreach (glob( '../public/imgs/tiny-mce/{*.jpg}', GLOB_BRACE ) as $name => $url) {
					$title = explode('/', $url);
					$js .= '{ title: "'. end($title) .'", value: "'.$url.'"},';
				}
				echo rtrim($js, ',');
				?>
			]
		});
		
		var href = this.href || e.target.href;		
		if( href.search('excluir') > '0' || href.search('excluir_img') > '0' )
			if( ! confirm("Deseja realmente excluir!") ) return false;
	
	});
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();
include 'rodape.php';