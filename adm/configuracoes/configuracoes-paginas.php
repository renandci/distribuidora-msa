<?php 
include '../topo.php';

/**
 * Cadastrar dados
 */
if( ! empty( $GET['acao'] ) && $GET['acao'] == 'descricao_cadastrar') {
	ConfiguracoesPaginas::action_cadastrar_editar($POST, 'cadastrar', 'pagina');
    header('Location: /adm/configuracoes/configuracoes-paginas.php');
    return;
}

/**
 * Editar dados
 */
if( ! empty( $GET['acao'] ) && $GET['acao'] == 'descricao_editar') {
	ConfiguracoesPaginas::action_cadastrar_editar($POST, 'alterar', 'pagina');
    header('Location: /adm/configuracoes/configuracoes-paginas.php');
    return;
}

/**
 * Excluir
 */
if( isset($GET['acao']) && $GET['acao'] == 'descricao_excluir' ) {
    ConfiguracoesPaginas::action_cadastrar_editar([ 'ConfiguracoesPaginas' => [ $GET['id'] => ['pagina' => ''] ] ], 'delete', 'pagina');
    header('Location: /adm/configuracoes/configuracoes-paginas.php');
    return;
}

/**
 * Remover em massa
 */
if( count( $POST['ConfiguracoesPaginas'] ) > 0 ) {
    ConfiguracoesPaginas::action_cadastrar_editar($POST, 'delete', 'pagina');
    header('Location: /adm/configuracoes/configuracoes-paginas.php');
    return;
}

?>
<style>	
	body{ background-color: #f1f1f1 }
</style>
<div class="mt50 panel panel-default">
    <div class="panel-heading panel-store text-uppercase">Configurações de Páginas</div>
	<div id="div-edicao" class="panel-body">
		<table width="100%" border="0" cellpadding="10" cellspacing="0">
			<tbody>
				<?php
				switch( $GET['acao'] ) :
					case 'Editar':
					case 'Cadastrar':
						$id = (INT)$GET['id'];
						$descricao = "";
						if( ! empty( $id ) && $id > 0 ) {
							$descricao = ConfiguracoesPaginas::find( $id );
						}
					?>
						<tr>
							<td>
								<form class="container mt10" action='/adm/configuracoes/configuracoes-paginas.php?acao=<?php echo !empty($GET['acao']) && $GET['acao'] == 'Cadastrar' ? 'descricao_cadastrar' : 'descricao_editar'?>' method='post'>
									<div class="row">
										<div class="col-md-10">	
											<b class="show mb5 text-left">Título da página (links):</b>
											<input type='text' value='<?php echo $descricao->pagina?>' name='ConfiguracoesPaginas[<?php echo $id?>][pagina]' class='w100'/>
										</div>
										<div class="col-md-2">	
											<b class="show mb5 text-left">Ordem:</b>
											<input type='text' value='<?php echo $descricao->ordem?>' name='ConfiguracoesPaginas[<?php echo $id?>][ordem]' class='w100'/>
										</div>
										<div class='col-md-12 mt25'>
											<b class="show mb5 text-left">Descrição da página:</b>
											<textarea name='ConfiguracoesPaginas[<?php echo $id?>][descricao]' id='descricao' class='w100 descricao' style='height: 505px'>
												<?php echo $descricao->descricao;?>
											</textarea>
										</div>
									</div>
									<button class='btn btn-primary btn-cadastros-descricao-descricao' type='submit'>salvar</button> 
									<a href="/adm/configuracoes/configuracoes-paginas.php" class="btn btn-danger mb5 mt5">voltar</a>
									
									<?php ob_start(); ?>
									<script>
//										$(document).ready(function() {
											/**
											 * Exemplo para upload de imagens do editor de texto
											 * @example https://www.tinymce.com/docs/demo/file-picker/
											 */
											tinyMCE.init({
												selector: "#descricao",
												language: "pt_BR",
												entity_encoding: "raw",
												menubar: 'view',
                                                importcss_append: true,
                                                preview_styles: true,
                                                content_css: "/public/css/adm/css.min.css",
												toolbar1 : "newdocument cut copy paste | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | format styleselect fontselect fontsizeselect",
												toolbar2 : "undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media | forecolor backcolor | insertdatetime preview | code template codesample visualblocks",
												plugins: [
													"image imagetools advlist autolink autosave link lists charmap print preview hr anchor pagebreak spellchecker",
													"code searchreplace wordcount visualblocks visualchars fullscreen insertdatetime media nonbreaking",
													"table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern", 
													"template importcss codesample visualblocks"
												],
												// invalid_elements: 'img,script,html,head,body,iframe,embed,object,param,video,audio,source,track,canvas,map,area,math,svg,applet,frame,frameset,bgsound,button,datalist,optgroup,option,keygen,output,progress,meter,form,input,select',

												template_replace_values: {
													nome: "Digite seu nome *",
													email: "E-mail de contato *",
													telefone: "Telefone de contato *",
													celular: "Celular de contato",
													cidade: "Digite o nome da sua cidade",
													assunto: "Digite um assunto *",
													mensagem: "Digite sua Mensagem *"
												},
                                                codesample_languages: [
                                                    {text: 'HTML/XML', value: 'markup'},
                                                    {text: 'JavaScript', value: 'javascript'},
                                                    {text: 'CSS', value: 'css'},
                                                    {text: 'PHP', value: 'php'},
                                                    // {text: 'Ruby', value: 'ruby'},
                                                    // {text: 'Python', value: 'python'},
                                                    // {text: 'Java', value: 'java'},
                                                    // {text: 'C', value: 'c'},
                                                    // {text: 'C#', value: 'csharp'},
                                                    // {text: 'C++', value: 'cpp'}
                                                ],
												templates: [
													{ 
														title: "Formulario de contato", 
														content: "Com esse formulario, você pode enviar um e-mail para sua caixa de correspondencia via website, lembre-se que será usado seu e-mail de cadastro feito na configuração do site.",
														url: "<?php echo URL_BASE?>templates/_all/loja-contato.php"
													}
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
							//                    image_list_url: "<?php echo url_base?>imgs/tiny-mce/index.php",
												image_list: [
													<?php
													$js = '';
													foreach (glob( '../public/imgs/tiny-mce/{*.jpg}', GLOB_BRACE ) as $name => $url) {
														$js .= '{ title: "'. end(explode('/', $url)) .'", value: "'.$url.'"},';
													}
													echo rtrim($js, ',');
													?>
												]
											});
//										});
									</script>
									<?php
									$SCRIPT['script_manual'] .= ob_get_clean();
									
									?>
								</form>
							</td>
						</tr>
					<?php
					break;
					default:
					?>
					<tr class="ocultar">
						<td colspan="5">
							<form action="/adm/configuracoes/configuracoes-paginas.php" method="post" class="formulario-cores">
								<input name="pesquisar" type="text" class="w65"/>
								<button type="submit" class="btn btn-primary">
									<i class="fa fa-search"></i>
								</button>
								<a href="/adm/configuracoes/configuracoes-paginas.php?acao=Cadastrar" class="btn btn-primary" <?php echo _P( 'configuracoes-paginas', $_SESSION['admin']['id_usuario'], 'incluir' )?>>cadastrar</a> 
								<!--<button class="btn btn-danger" type="button" data-id="btn-excluir-varios" data-href="configuracoes-paginas.php" <?php echo _P( 'configuracoes-paginas', $_SESSION['admin']['id_usuario'], 'excluir' )?>>excluir seleção</button>-->
							</form>
						</td>
					</tr>

						<tr class='plano-fundo-adm-003'>
							<td><b>Links/Páginas</b></td>
							<td><b>Ordem</b></td>
							<td align='center'><b>Ações</b></td>
						</tr>
						<?php
						$i			= 0;
						$maximo 	= 30;
						$pesquisar 	= isset($GET['pesquisar']) ? " and pagina like '%".(string)$GET['pesquisar']."%' " : "";
						$pag 		= isset( $GET['pag'] ) && $GET['pag'] <> '' ? $GET['pag'] : 1;
						$inicio 	= (($pag * $maximo) - $maximo);
						$busca 		= "select * from configuracoes_paginas where 0 = 0 {$pesquisar} order by ordem desc";
						$total 		= (ceil( ConfiguracoesPaginas::find_by_sql($busca) ) / $maximo );
						$busca 		.= " limit {$inicio}, {$maximo}";
						
						$sql 		= ConfiguracoesPaginas::find_by_sql( $busca );
						foreach( $sql as $rs ) { $rs = $rs->to_array(); ?>
						
						<tr <?php echo ($i % 2) ? "style=\"background-color:#f3f3f3;\"":"";?> class="formulario<?php echo $rs['id'];?> in-hover">
							<td>
								<?php echo $rs['pagina'];?>
							</td>
							<td align="center" width='1%' nowrap="nowrap">
								<?php echo $rs['ordem'];?>
							</td>
							<td align="center" width='1%' nowrap="nowrap">
								<a href='configuracoes-paginas.php?acao=Editar&id=<?php echo $rs['id'];?>' class='btn btn-primary btn-sm'>editar</a> 
								<a href='configuracoes-paginas.php?acao=descricao_excluir&id=<?php echo $rs['id'];?>' class='btn btn-primary btn-sm btn-excluir-descricao-descricao'>excluir</a>
							</td>
						</tr>
						<?php ++$i; } ?>
						<tr>
							<td colspan="2">
								<div class="paginacao paginacao-add">
									<?php
									if( $total > 0 )
									{
										for( $i = $pag - 1, $limiteDeLinks = $i + 5; $i <= $limiteDeLinks; ++$i )
										{
											if($i < 1)
											{
												$i = 1;
												$limiteDeLinks = 5;
											}
										
											if($limiteDeLinks > $total)
											{
												$limiteDeLinks = $total; 
												$i = $limiteDeLinks - 4;
											}

											if($i < 1)
											{
												$i = 1;
												$limiteDeLinks = $total;
											}
											
											if($i == $pag)
											{
												echo "<span class=\"at plano-fundo-adm-001\">{$i}</span>";
											}
											else
											{							
												echo "<a href=\"/adm/configuracoes/configuracoes-paginas.php?pag={$i}&pesquisar={$GET['pesquisar']}\">{$i}</a>";
											}
										}
									}
									?>
								</div>
							</td>
						</tr>
						<?php
					break;
				endswitch;
				?>
			</tbody>
		</table>
	</div>
<script>
    $(document).on("click", "a", function(){
        var href = this.href || e.target.href;		
        if( href.search('excluir') > '0')
            if( ! confirm("Deseja realmente excluir!") ) return false;

    });
</script>
<?php include '../rodape.php';