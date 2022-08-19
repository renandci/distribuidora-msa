<?php
include 'topo.php';

/**
 * Cadastra
 */
if( isset($GET['acao']) && $GET['acao'] === 'cadastrar' ) {
    Grupos::action_cadastrar_editar($POST, 'cadastrar', 'grupo');
    header('Location: /adm/grupos.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Editar
 */
if( isset($GET['acao']) && $GET['acao'] === 'editar' ) {
    Grupos::action_cadastrar_editar($POST, 'alterar', 'grupo');
    header('Location: /adm/grupos.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Excluir
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir' ) {
    Grupos::action_cadastrar_editar([ 'Grupos' => [ $GET['id'] => ['excluir' => 1] ] ], 'excluir', 'grupo');
    $Menus = ProdutosMenus::all(['conditions' => ['id_grupo=?', $GET['id']]]);
    foreach ($Menus as $val) {
        ProdutosMenus::action_cadastrar_editar([ 'ProdutosMenus' => [ $val->id => ['id_grupo' => $val->id_grupo]]], 'delete', 'codigo_id');
    }
    header('Location: /adm/grupos.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Remover em massa
 */
if( count( $POST['Grupos'] ) > 0 ) {
    Grupos::action_cadastrar_editar($POST, 'excluir', 'grupo');
    foreach ($POST['Grupos'] as $id => $nulls ) {
        $Menus = ProdutosMenus::all(['conditions' => ['id_grupo=?', $id]]);
        foreach ($Menus as $val) {
            ProdutosMenus::action_cadastrar_editar([ 'ProdutosMenus' => [ $val->id => ['id_grupo' => $val->id_grupo]]], 'delete', 'codigo_id');
        }

    }
    header('Location: /adm/grupos.php?codigo_id=' . $GET['codigo_id']);
    return;
}

$TOTAL_CADASTROS_GERAL = Grupos::count();
$TOTAL_CADASTROS_ATIVOS = Grupos::count(['conditions'=>['excluir=? and loja_id=?', 0, $CONFIG['loja_id']]]);
$TOTAL_CADASTROS_DESATIVOS = Grupos::count(['conditions'=>['excluir=?', 1]]);

$GET_STATUS = isset( $POST['status'] ) && $POST['status'] != '' ? $POST['status'] : ( isset( $GET['status'] ) && $GET['status'] != '' ? $GET['status'] : '' );
$GET_PESQUISAR = isset( $GET['pesquisar'] ) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : ( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '' );


// printf('<pre>%s</pre>', print_r(Pedidos::find(275)->nfe_notas, 1));
// AULA 228

?>

<div class="tag-opcoes clearfix panel panel-default">
	<div class="panel-heading panel-store text-uppercase">MENUS</div>
	<div id="div-edicao" class="panel-body">
		<style>
			body{ background-color: #f1f1f1 }
			.ocultos{ display: none; }
		</style>
		<table width="100%" border="0" cellpadding="10" cellspacing="0">
			<tbody>
				<tr class="ocultar">
					<td colspan="4">
						<form action="/adm/grupos.php?codigo_id=<?php echo $GET['codigo_id']?>" method="post" class="formulario-grupos">
							<div class="clearfix mb15" style="line-height: 17px;">
								<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> menus cadastrados</span> 
							</div>
							<input name="pesquisar" type="text" class="w50"/>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
							<button class="btn btn-primary" type="button" onclick="$('.ocultar').slideToggle(0);" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'incluir' )?>>cadastrar</button>
							<button class="btn btn-danger" type="button" data-action="btn-excluir-varios" data-href="/adm/grupos.php?codigo_id=<?php echo $GET['codigo_id']?>" <?php echo _P( 'grupos', $_SESSION['admin']['id_usuario'], 'excluir' )?>>
                                excluir seleção
                            </button>
						</form>
					</td>
				</tr>
				
				<tr id="formulario" class="ocultos ocultar">
					<td colspan="4">
						<form class="formulario-grupos" action="/adm/grupos.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=cadastrar" method="post">
							<input type="hidden" name="Grupos[0][loja_id]" value="<?php echo $CONFIG['loja_id']?>" class="hidden"/>
                            <fieldset class="mb15">
								<div class="row">
									<div class="col-md-6">
										<div class="mb15 col-xs-12">
											<label>Nome do menu:</label>
											<input type="text" name="Grupos[0][grupo]" class="w100"/>
										</div>
										<div class="col-xs-3">
											<label>Ordem:</label>
											<input type="text" name="Grupos[0][ordem]" class="w100"/>
										</div>
										<div class="col-xs-9">
											<label>
												Icon:
												<span class="info-title tooltip" title="Você pode adicionar um icone nos menus, quando as fontes no seu site estão instaladas. As fontes devem ser fa, fontello, icon etc..">?</span>
											</label>
											<input type="text" name="Grupos[0][grupo_icon]" class="w100"/>
										</div>
									</div>
									<div class="col-md-6">
										<fieldset class="mb15">
											<legend>Informações para SEO</legend>
											<div class="show w100 mb15">
												<label>Palavras chave: <span class="info-title tooltip" title="Palavras chaves para sistemas de buscas (google).">?</span></label>
												<input type="text" name="Grupos[0][grupo_keywords]" class="w100 count-input" maxlength="200"/>
											</div>
											<div class="show w100 mb15">
												<label>Descrição: <span class="info-title tooltip" title="Prévia descrição para os sistema de buscas (google).">?</span></label>
												<textarea name="Grupos[0][grupo_description]" class="w100 count-input" maxlength="250"></textarea>
											</div>
										</fieldset>
									</div>
									<div class="col-md-12 text-center">
										<button type="submit" class="btn btn-primary btn-cadastros-grupos" <?php echo _P( $PgAt, $_SESSION["admin"]["id_usuario"], "incluir" )?>>
											salvar
										</button>
										<button type="button" class="btn btn-danger" onclick="$('.ocultar').slideToggle(0);" <?php echo _P( $PgAt, $_SESSION["admin"]["id_usuario"], "excluir" )?>>
											cancela
										</button>
									</div>
								</div>
                            </fieldset>
						</form>
					</td>
				</tr>
			
				<tr class="plano-fundo-adm-003 ocultar">
					<td bgcolor="#ffffff" align="center" nowrap="nowrap" width="1%">
						<input type="checkbox" data-action="selecionados-exclusao-all" class="selecionados-exclusao-all" id="label" value=""/>
						<label for="label" class="input-checkbox"></label>
					</td>
					<td>Nome do menu</td>
					<td align='center'>Ordem</td>
					<td align='center'>Ações</td>
				</tr>
				
				<?php
				$i = 0;
				
				$arr_id = [];
                if(isset($GET['codigo_id']) && $GET['codigo_id'] > 0)
                    foreach(ProdutosMenus::all(['conditions' => ['codigo_id=?', $GET['codigo_id']]]) as $g)
                        $arr_id[] = $g->id_grupo;

				$maximo = 25;
				
				$pag = isset( $GET['pag'] ) && $GET['pag'] != '' ? $GET['pag'] : 1;
				
				$inicio = (($pag * $maximo) - $maximo);
				
				$conditions = array();
				
				// $conditions['select'] = sprintf('grupos.*, (SELECT id_grupo FROM produtos_menus WHERE codigo_id=%u AND id_grupo = grupos.id GROUP BY 1) as test ', $GET['codigo_id'], $GET['id_grupo']);

				$conditions['conditions'] = sprintf('excluir = 0 and loja_id=%u', $CONFIG['loja_id']);
				$conditions['conditions'] .= isset( $GET_PESQUISAR ) && $GET_PESQUISAR != '' ? sprintf(' and grupo like "%%%s%%" ', $GET_PESQUISAR)  : '';
				
				// $conditions['conditions'] .= isset( $GET['codigo_id'] ) && $GET['codigo_id'] > 0 
				// 	? queryInjection(' and id not in(SELECT produtos_menus.id_grupo FROM produtos_menus WHERE produtos_menus.codigo_id = %u) ', $GET['codigo_id']) : '';
				
				$total = ceil( Grupos::count($conditions) / $maximo );
				
				$conditions['order'] = 'grupos.ordem asc, grupos.grupo asc';
				
				$conditions['limit'] = $maximo;
				
				$conditions['offset'] = ($maximo * ($pag - 1));
				
				$result = Grupos::all( $conditions );
				
				foreach( $result as $rs ) { $rs = $rs->to_array(); ?>

				<tr class="lista-zebrada in-hover formulario<?php echo $rs['id'];?> ocultar" <?php echo ($i % 2) ? 'style="background-color:#f3f3f3"': ''?>>
					<td nowrap="nowrap" width="1%">
						<input type="checkbox" name="Grupos[<?php echo $rs['id'];?>][excluir]" id="label<?php echo $rs['id']?>" value="1" data-action="selecionados-exclusao"/>
						<label for="label<?php echo $rs['id']?>" class="input-checkbox"></label>
					</td>
					<td>
						<?php echo $rs['grupo']?>
						<?php echo !empty($arr_id) && in_array($rs['id'], $arr_id) ? '<span class="pull-right btn btn-info btn-xs ft10px">adicionado</span>': null?>
					</td>
					<td align="center" nowrap="nowrap" width="1%">
						<?php echo $rs['ordem'] ?>
					</td>
					<td align="center" nowrap="nowrap" width="1%">
						<!--
						<a href="/adm/grupos-produto-destaque.php?produtoid=<?php echo $rs['produto_id'];?>&grupoid=<?php echo $rs['id']?>" class="btn btn-warning<?php echo $rs['produto_id'] > 0 ? '':'-default';?> btn-sm btn-produto-destaque<?php echo $GET['codigo_id'] ? ' hidden' : ''?>" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'incluir' )?>>produto destaque</a>
						-->
						<!-- <a href="/adm/grupos.php?codigo_id=<?php echo $GET['codigo_id'];?>&grupoid=<?php echo $rs['id']?>" class="btn btn-warning btn-sm btn-adicionar-novo-grupo<?php echo '' == $GET['codigo_id'] ? ' hidden' : ''?>" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'incluir' )?>>adicionar</a> -->
                        
						<a href='javascript: void(0);' class="btn btn-primary btn-sm" onclick="$('.formulario<?php echo $rs['id']?>').slideToggle(0);" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'alterar' )?>>editar</a> 
                        
						<a href='/adm/grupos.php?codigo_id=<?php echo $GET['codigo_id']?>&id=<?php echo $rs['id']?>&acao=excluir' class='btn btn-danger btn-sm btn-cadastros-grupos' <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'excluir' )?>>excluir</a>
					</td>
				</tr>
				<tr class="formulario<?php echo $rs['id'];?> ocultos lista-zebrada" id='formulario<?php echo $rs['id'];?>'>
					<td colspan="4">
						<form class="formulario-grupos" action="/adm/grupos.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=editar" method="post">
							<fieldset class="mb15" style="background-color: #fff">
								<div class="row">
									<div class="col-md-6">
										<div class="mb15 col-xs-12">
											<label>Nome do menu:</label>
											<input type="text" value="<?php echo $rs['grupo'];?>" name="Grupos[<?php echo $rs['id'];?>][grupo]" class="w100"/>
										</div>
										<div class="col-xs-3">
											<label>Ordem:</label>
											<input type="text" value="<?php echo $rs['ordem'];?>" name="Grupos[<?php echo $rs['id'];?>][ordem]" class="w100"/>
										</div>
										<div class="col-xs-9">
											<label>
												Icon:
												<span class="info-title tooltip" title="Você pode adicionar um icone nos menus, quando as fontes no seu site estão instaladas. As fontes devem ser fa, fontello, icon etc..">?</span>
											</label>
											<input type="text" value="<?php echo $rs['grupo_icon'];?>" name="Grupos[<?php echo $rs['id'];?>][grupo_icon]" class="w100"/>
										</div>
									</div>
									<div class="col-md-6">
										<fieldset class="mb15">
											<legend>Informações para SEO</legend>
											<div class="show w100 mb15">
												<label>Palavras chave: <span class="info-title tooltip" title="Palavras chaves para sistemas de buscas (google).">?</span></label>
												<input type="text" value="<?php echo $rs["grupo_keywords"];?>" name="Grupos[<?php echo $rs["id"];?>][grupo_keywords]" class="w50 count-input" maxlength="200"/>
											</div>
											<div class="show w100 mb15">
												<label>Descrição: <span class="info-title tooltip" title="Prévia descrição para os sistema de buscas (google).">?</span></label>
												<textarea name="Grupos[<?php echo $rs["id"];?>][grupo_description]" class="w100 count-input" maxlength="250"><?php echo $rs["grupo_description"];?></textarea>
											</div>
										</fieldset>
									</div>
									<div class="col-md-12 text-center">
										<button type="submit" class="btn btn-primary btn-cadastros-grupos" <?php echo _P( $PgAt, $_SESSION["admin"]["id_usuario"], "incluir" )?>>
											salvar
										</button>
										<button type="button" class="btn btn-danger" onclick="$('.formulario<?php echo $rs["id"];?>').slideToggle(0);" <?php echo _P( $PgAt, $_SESSION["admin"]["id_usuario"], "excluir" )?>>
											cancela
										</button>
									</div>
								</div>
							</fieldset>
						</form>
					</td>
				</tr>
				<?php
				++$i;
				}
				?>
				<tr>
					<td colspan="4">
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
									
									if($i == $pag) {
										echo "<span class=\"at plano-fundo-adm-001\">{$i}</span>";
									}
									else {
										$data = http_build_query(array_replace($GET, ['pag' => $i]));
										echo sprintf('<a href="/adm/grupos.php?%s" class="btn-paginacao">%s</a>', $data, $i);
										// echo "<a href=\"/adm/grupos.php?pesquisar={$GET_PESQUISAR}&status={$GET_STATUS}&codigo_id={$GET['codigo_id']}&pag={$i}\" class='btn-paginacao'>{$i}</a>";
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
                /**
                 * Abre a janela de consulta de produtos
                 */
                $("#div-edicao").on("click", "a.btn-produto-destaque", function(e){
                    e.preventDefault();
                    $.ajax({
                        url: this.href||e.target.href,
                        beforeSend: function(){
                            JanelaModal.dialog({title: "Produto de Destaque", autoOpen:true}).html([
                               $("<h2>",{
                                   class: "text-center",
                                   html: "Carregando os produtos...",
                               })
                            ]);
                        },
                        success: function(a){
                            var l = $("<div/>", {html: a});
                            JanelaModal.dialog({title: "Produto de Destaque"}).html(l.find("#div-edicao").html());
                        }
                    });
                });

                /**
                 * Submit para buscar o produto
                 */
                JanelaModal.on("submit", "a.__paginacao", function(e){
                    e.preventDefault();
                    $.ajax({
                        url: this.href||e.target.action,
                        success: function(a){
                            var l = $("<div/>", {html: a});
                            JanelaModal.dialog().html(l.find("#div-edicao").html());
                        }
                    });
                });
                /**
                 * Paginação de dados dentro da tela modal
                 */
                JanelaModal.on("click", "a.__paginacao", function(e){
                     e.preventDefault();
                    $.ajax({
                        url: this.href||e.target.href,
                        success: function(a){
                            var l = $("<div/>", {html: a});
                            JanelaModal.dialog().html(l.find("#div-edicao").html());
                        }
                    });
                });

                /**
                 * Adiciona ou remove o produto em destaque
                 */
                JanelaModal.on("click", "a.btn_produto_adicionar_remover", function(e){
                    e.preventDefault();
                    $.ajax({
                        url: this.href||e.target.href,
                        beforeSend: function(){
                            JanelaModal.dialog({title: "Produto de Destaque", autoOpen:true}).html([
                               $("<h2>",{
                                   class: "text-center",
                                   html: "Carregando os produtos...",
                               })
                            ]);
                        },
                        error: function(a,b,c){
                            JanelaModal.dialog({title: "Error"}).html(a.responseText+"\<br/>"+b+"<br/>"+c);
                        },
                        success: function(a){
                            console.log( a );
                            var l = $( "<div/>" , { html: a });
                            JanelaModal.dialog({title: "Produto de Destaque"}).html(l.find("#div-edicao").html());
                        }
                    });
                });
            });
            $(".count-input").counter();
            <?php
            $JSqueeze = new Patchwork\JSqueeze();
            $content = $JSqueeze->squeeze(ob_get_clean(), true, false, false);
            // echo $content;
            ?>
        </script>
	</div>
</div>
<script>
	$(document).on("click", "a", function(){
		var href = this.href || e.target.href;		
		if( href.search('excluir') > '0')
			if( ! confirm("Deseja realmente excluir!") ) return false;
	
	});
</script>
<?php
include 'rodape.php';
