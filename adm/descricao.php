<?php
include 'topo.php';

/**
 * Cadastra
 */
if( isset($GET['acao']) && $GET['acao'] === 'cadastrar' ) {
    ProdutosDescricoes::action_cadastrar_editar($POST, 'cadastrar', 'nome');
    header('Location: /adm/descricao.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Editar
 */
if( isset($GET['acao']) && $GET['acao'] === 'editar' ) {
    ProdutosDescricoes::action_cadastrar_editar($POST, 'alterar', 'nome');
    header('Location: /adm/descricao.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Excluir
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir' ) {
    ProdutosDescricoes::action_cadastrar_editar([ 'ProdutosDescricoes' => [ $GET['id'] => ['excluir' => 1] ] ], 'excluir', 'nome');
    header('Location: /adm/descricao.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Remover em massa
 */
if( count( $POST['ProdutosDescricoes'] ) > 0 ) {

	foreach ($POST['ProdutosDescricoes'] as $key => $value) {
		ProdutosDescricoes::action_cadastrar_editar(['ProdutosDescricoes' => [$key => ['excluir' => '1']]], 'excluir', 'nome');
		// printf('<div id="div-edicao"><pre>%s</pre>', print_r($key, 1));
	}

    header('Location: /adm/descricao.php?codigo_id=' . $GET['codigo_id']);
    return;
}

$TOTAL_CADASTROS_GERAL = ProdutosDescricoes::count(['conditions' => [ 'loja_id=?', $CONFIG['loja_id']]]);
$TOTAL_CADASTROS_ATIVOS = ProdutosDescricoes::count(['conditions' => [ 'excluir=? and loja_id=?', 0, $CONFIG['loja_id']]]);
$TOTAL_CADASTROS_DESATIVOS = ProdutosDescricoes::count(['conditions' => ['excluir=? and loja_id=?', 1, $CONFIG['loja_id']]]);

$GET_STATUS = isset( $POST['status'] ) && $POST['status'] != '' ? $POST['status'] : ( isset( $GET['status'] ) && $GET['status'] != '' ? $GET['status'] : '' );
$GET_PESQUISAR = isset( $GET['pesquisar'] ) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : ( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '' );
?>
<div class="panel panel-default">
    <div class="panel-heading panel-store text-uppercase ocultar">DESCRIÇÕES</div>
	<div id="div-edicao" class="panel-body">
		<style>	
            body{ background-color: #f1f1f1 }
			.ocultos{ display: none; }
		</style>
		<table width="100%" border="0" cellpadding="10" cellspacing="0" id="tabela-descricoes">
			<tbody>
				<tr class="ocultar">
					<td colspan="3">
						<form action="/adm/descricao.php?codigo_id=<?php echo $GET['codigo_id']?>" method="post" class="formulario-descricao">
							<div class="clearfix mb15" style="line-height: 17px;">
								<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> descrições cadastradas</span>
							</div>
							<input name="pesquisar" type="text" class="w55"/>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
							<button class="btn btn-primary" type="button" data-init="editor-00" onclick="$('.ocultar').slideToggle(0);" <?php echo _P( 'descricao', $_SESSION['admin']['id_usuario'], 'alterar|incluir' )?>>cadastrar</button>

                            <button class="btn btn-danger" type="button" data-action="btn-excluir-varios" data-href="/adm/descricao.php?codigo_id=<?php echo $GET['codigo_id']?>" <?php echo _P( 'descricao', $_SESSION['admin']['id_usuario'], 'excluir' )?>>
                                excluir seleção
                            </button>
						</form>
					</td>
				</tr>
				
				<tr id="formulario" class="ocultos ocultar">
					<td colspan="3">
						<form class="formulario-produtos-descricoes" action="/adm/descricao.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=cadastrar" method="post">
							<div class="clearfix">
								<div class="w50 mr15">
									<p>Nome da Descricão:</p>
									<input type="text" name="ProdutosDescricoes[0][nome]" class="w100"/>
								</div>
							</div>
							<div class="show w100 mb15">
								<p>Descricão:</p>
								<textarea name="ProdutosDescricoes[0][descricao]" class="w100 produtos-descricao" rows="15" id="editor-00" style="height: 405px"></textarea>
							</div>
							
							<button type="submit" class="btn btn-primary btn-cadastros-produtos_descricoes"  
                                <?php echo _P( 'descricao', $_SESSION['admin']['id_usuario'], 'alterar|incluir' )?>>salvar</button>
                            
							<button type="button" data-editor="00"  class="btn btn-danger destroy-editor" onclick="$('.ocultar').slideToggle(0);">cancelar</button>
						</form>
					</td>
				</tr>
			
				<tr class="plano-fundo-adm-003 ocultar">
					<td bgcolor="#ffffff" align="center" nowrap="nowrap" width="1%">
						<!--<input type="checkbox" data-action="selecionados-exclusao-all" class="selecionados-exclusao-all" id="label" value=""/>-->
                        <input type="checkbox" data-action="selecionados-exclusao-all" class="selecionados-exclusao-all" id="label" value=""/>
						<label for="label" class="input-checkbox"></label>
					</td>
					<td>Descricão</td>
					<td align='center'>Ações</td>
				</tr>
				
				<?php
				$i = 0;
				
				$maximo = 25;
				
				$pag = ! empty( $GET['pag'] ) && $GET['pag'] > 0 ? (int)$GET['pag'] : 1;
				
				$inicio = (( $pag * $maximo ) - $maximo);
				
                $conditions['conditions'] = sprintf('excluir=0 and loja_id=%u ', $CONFIG['loja_id']);
                
                /**
                 * Pesquisar dados
                 */
                if( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ) {
					$conditions['conditions'] .= sprintf('and nome like "%%%s%%"', addslashes($POST['pesquisar']));
                }
                
                // /**
                //  * Outras conditions com pesquisa
                //  */
                // if( isset( $GET['codigo_id'] ) && $GET['codigo_id'] > 0 ) {
                //     $conditions['conditions'] = ['id NOT IN(SELECT id_grupo FROM produtos_menus WHERE codigo_id = ?) and excluir=?', $GET['codigo_id'], 0];
                //     if( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ) {
                //         $conditions['conditions'] = [
                //             'id not in(SELECT id_grupo FROM produtos_menus WHERE codigo_id=?) and excluir=? and nome like ?', $GET['codigo_id'], 0, "%{$POST['pesquisar']}%"
                //         ];
                //     }
                // }
                
				$total = ceil((ProdutosDescricoes::count($conditions) / $maximo));
                $conditions['order'] = 'nome asc';
				$conditions['limit'] = $maximo;
				$conditions['offset'] = ($maximo * ($pag - 1));
                
				$result = ProdutosDescricoes::all($conditions);
				foreach( $result as $rs ) { ?>
				<tr class="lista-zebrada in-hover formulario<?php echo $rs->id;?> ocultar" <?php echo ($i % 2) ? 'style="background-color:#f3f3f3"': ''?>>
					<td nowrap="nowrap" width="1%">
                        <input type="checkbox" name="ProdutosDescricoes[<?php echo $rs->id;?>][excluir]" id="label<?php echo $rs->id?>" value="1" data-action="selecionados-exclusao"/>
						<label for="label<?php echo $rs->id;?>" class="input-checkbox"></label>
					</td>
					<td>
						<?php echo $rs->nome;?>
						<?php echo !empty($GET['id_descricao']) && $GET['id_descricao'] == $rs->id ? '<span class="pull-right btn btn-info btn-xs ft10px">adicionado</span>': null?>
					</td>
					<td align="center" nowrap="nowrap" width="1%">
						<a href='javascript: void(0);' class="btn btn-primary btn-sm" data-init="editor-<?php echo $rs->id?>" onclick="$('.formulario<?php echo $rs->id?>').slideToggle(0);" <?php echo _P( 'descricao', $_SESSION['admin']['id_usuario'], 'alterar' )?>>editar</a> 
						<a href='/adm/descricao.php?codigo_id=<?php echo $GET['codigo_id']?>&id=<?php echo $rs->id?>&acao=excluir' class='btn btn-danger btn-sm btn-excluir-modal' <?php echo _P( 'descricao', $_SESSION['admin']['id_usuario'], 'excluir' )?>>excluir</a>
					</td>
				</tr>
				<tr class="formulario<?php echo $rs->id;?> ocultos lista-zebrada" id='formulario<?php echo $rs->id;?>'>
					<td colspan="3">
						<form class="formulario-produtos-descricoes" action="/adm/descricao.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=editar" method="post">
							<div class="clearfix">
								<div class='pull-left w50 mr15'>
									<p>Nome da Descricão:</p>
									<input type='text' value='<?php echo $rs->nome;?>' name='ProdutosDescricoes[<?php echo $rs->id;?>][nome]' class="w100"/>
								</div>
							</div>
							<div class='show w100 mb15'>
								<p>Descricão:</p>
								<textarea name='ProdutosDescricoes[<?php echo $rs->id;?>][descricao]' class='w100 produtos-descricao' rows='15' id='editor-<?php echo $rs->id?>' style="height: 405px"><?php echo $rs->descricao;?></textarea>
							</div>
							
							<button type="submit" class="btn btn-primary btn-cadastros-produtos_descricoes" 
                                <?php echo _P('descricao', $_SESSION['admin']['id_usuario'], 'alterar' )?>>
                                salvar
                            </button>
                            
							<button type="button" data-editor="<?php echo $rs->id;?>" class="destroy-editor btn btn-danger" onclick="$('.formulario<?php echo $rs->id;?>').slideToggle(0);">
                                cancelar
                            </button>
                            
						</form>
					</td>
				</tr>
				<?php ++$i; } ?>
				<tr class="ocultar">
					<td colspan="3">
						<div class="paginacao paginacao-add">
							<?php
							if( $total > 0 )
							{
								for( $i = $pag - 5, $limiteDeLinks = $i + 10; $i <= $limiteDeLinks; ++$i )
								{
									if($i < 1)
									{
										$i = 1;
										$limiteDeLinks = 9;
									}
								
									if($limiteDeLinks > $total)
									{
										$limiteDeLinks = $total; 
										$i = $limiteDeLinks - 10;
									}

									if($i < 1)
									{
										$i = 1;
										$limiteDeLinks = $total;
									}
									
									if($i == $pag) {
										echo "<span class=\"at plano-fundo-adm-001\">{$i}</span>";
									}
									else {
										$data = http_build_query(array_replace($GET, ['pag' => $i]));
										echo sprintf('<a href="/adm/descricao.php?%s" class="btn-paginacao">%s</a>', $data, $i);
										// echo "<a href=\"/adm/descricao.php?codigo_id={$GET['codigo_id']}&pag={$i}\" class='btn-paginacao'>{$i}</a>";
									}
								}
							}
							?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
        <script>
            <?php ob_start(); ?>
            $(function(){
                $('#tabela-descricoes').on("click", "[data-init]", function(){
                    
                    var textarea = $(this).attr("data-init");                      
                    
                    if (tinyMCE.activeEditor !== null){
                        tinymce.EditorManager.execCommand('mceRemoveEditor', true, textarea);
                    }

                    $("#tabela-descricoes #" + textarea).tinymce({
                        menubar: false,
                        language: "pt_BR",
                        entity_encoding: "raw",
                        toolbar_items_size: "small",
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
	//                    image_list_url: "<?php echo URL_BASE?>imgs/tiny-mce/index.php",
						image_list: [
							<?php
							$js = '';
							foreach (glob( '../public/imgs/tiny-mce/{*.jpg}', GLOB_BRACE ) as $name => $url) {
								$js .= '{ title: "'. $url .'", value: "'.$url.'"},';
							}
							echo rtrim($js, ',');
							?>
						]
                    });
                });
            });
            <?php
            $script = ob_get_clean();
            
            $JSqueeze = new Patchwork\JSqueeze();
            $content = $JSqueeze->squeeze($script, true, false, false);
            echo $content;
            ?>
        </script>
	</div>
    <script>
        $(document).on("click", "a", function(){
            var href = this.href || e.target.href;		
            if( href.search('excluir') > '0')
                if( ! confirm("Deseja realmente excluir!") ) return false;

        });
    </script>
</div>
<?php
include 'rodape.php';