<?php
include 'topo.php';

/**
 * Cadastra
 */
if( isset($GET['acao']) && $GET['acao'] === 'cadastrar' ) {
    SubGrupos::action_cadastrar_editar($POST, 'cadastrar', 'subgrupo');
    header('Location: /adm/sub-grupos.php?codigo_id=' . $GET['codigo_id'] . '&id_grupo=' . $GET['id_grupo']);
    return;
}

/**
 * Editar
 */
if( isset($GET['acao']) && $GET['acao'] === 'editar' ) {
    SubGrupos::action_cadastrar_editar($POST, 'alterar', 'subgrupo');
    header('Location: /adm/sub-grupos.php?codigo_id=' . $GET['codigo_id'] . '&id_grupo=' . $GET['id_grupo'] );
    return;
}

/**
 * Excluir
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir' ) {
    SubGrupos::action_cadastrar_editar([ 'SubGrupos' => [ $GET['id'] => ['excluir' => 1] ] ], 'excluir', 'subgrupo');
    
    $Menus = ProdutosMenus::all(['conditions' => ['id_subgrupo=?', $GET['id']]]);
    foreach ($Menus as $val) {
        ProdutosMenus::action_cadastrar_editar([ 'ProdutosMenus' => [ $val->id => ['id_subgrupo' => $val->id_subgrupo]]], 'delete', 'codigo_id');
    }
    
    header('Location: /adm/sub-grupos.php?codigo_id=' . $GET['codigo_id'] . '&id_grupo=' . $GET['id_grupo']);
    return;
}

/**
 * Remover em massa
 */
if( count( $POST['SubGrupos'] ) > 0 ) {
    SubGrupos::action_cadastrar_editar($POST, 'excluir', 'subgrupo');
    
    foreach ($POST['SubGrupos'] as $id => $nulls ) {
        $Menus = ProdutosMenus::all(['conditions' => ['id_subgrupo=?', $id]]);
        foreach ($Menus as $val) {
            ProdutosMenus::action_cadastrar_editar([ 'ProdutosMenus' => [ $val->id => ['id_subgrupo' => $val->id_subgrupo]]], 'delete', 'codigo_id');
        }
    }
    
    header('Location: /adm/sub-grupos.php?codigo_id=' . $GET['codigo_id']);
    return;
}

$TOTAL_CADASTROS_ATIVOS = SubGrupos::count(['conditions'=>['excluir=? and loja_id=?', 0, $CONFIG['loja_id']]]);
// $TOTAL_CADASTROS_GERAL = SubGrupos::count();
// $TOTAL_CADASTROS_DESATIVOS = SubGrupos::count(['conditions'=>['excluir=?', 1]]);

$GET_STATUS = isset( $POST['status'] ) && $POST['status'] != '' ? $POST['status'] : ( isset( $GET['status'] ) && $GET['status'] != '' ? $GET['status'] : '' );
$GET_PESQUISAR = isset( $GET['pesquisar'] ) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : ( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '' );
?>

<div class="tag-opcoes panel panel-default">
	<div class="panel-heading panel-store text-uppercase">SUB MENUS</div>
	<div id="div-edicao" class="panel-body">
		<style>
			body{ background-color: #f1f1f1 }
			.ocultos{ display: none; }
		</style>
		<table width="100%" border="0" cellpadding="8" cellspacing="0">
			<tbody>
				<tr class="ocultar">
					<td colspan="4" >
						<form action="/adm/sub-grupos.php?id_grupo=<?php echo $GET['id_grupo']?>&codigo_id=<?php echo $GET['codigo_id']?>" method="post" class="formulario-subgrupos">
							<div class="clearfix mb15" style="line-height: 17px;">
								<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> sub menus cadastrados</span> 
							</div>
							<input name="pesquisar" type="text" class="w50"/>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
							<button class="btn btn-primary" type="button" onclick="$('.ocultar').slideToggle(0);" <?php echo _P($PgAt, $_SESSION['admin']['id_usuario'], 'incluir')?>>cadastrar</button>
							<button class="btn btn-danger" type="button" data-action="btn-excluir-varios" data-href="/adm/sub-grupos.php?codigo_id=<?php echo $GET['codigo_id']?>" <?php echo _P( 'subgrupos', $_SESSION['admin']['id_usuario'], 'excluir' )?>>
                                excluir seleção
                            </button>
						</form>
					</td>
				</tr>
				
				<tr id="formulario" class="ocultos ocultar">
					<td colspan="4">
						<form class="formulario-subgrupos" action="/adm/sub-grupos.php?id_grupo=<?php echo $GET['id_grupo']?>&codigo_id=<?php echo $GET['codigo_id']?>&acao=cadastrar" method="post">
                            <fieldset class="mb15">
                                <div class="row">
									<div class="col-md-6">
										<div class="mb15 col-xs-12">
											<label>Nome do sub menu:</label>
											<input type="text" name='SubGrupos[0][subgrupo]' class="w100"/>
										</div>
										<div class='col-xs-3'>
											<label>Ordem:</label>
											<input type="text" name='SubGrupos[0][ordem]' class="w100"/>
										</div>
									</div>
									<div class="col-md-6">
										<fieldset class="mb15">
											<legend>Informações para SEO</legend>
											<div class='show w100 mb15'>
												<label>Palavras chave: <span class="info-title tooltip" title="Palavras chaves para sistemas de buscas (google).">?</span></label>
												<input type="text" name='SubGrupos[0][subgrupo_keywords]' class="w50 count-input" maxlength="200"/>
											</div>
											<div class='show w100 mb15'>
												<label>Descrição: <span class="info-title tooltip" title="Prévia descrição para os sistema de buscas (google).">?</span></label>
												<textarea name='SubGrupos[0][subgrupo_description]' class="w100 count-input" maxlength="250"></textarea>
											</div>
										</fieldset>
									</div>
									<div class="col-md-12 text-center">
										<button type="submit" class="btn btn-primary btn-cadastros-subgrupos" <?php echo _P($PgAt, $_SESSION['admin']['id_usuario'], 'incluir' )?>>
											salvar
										</button>
										<button type="button" class="btn btn-danger" onclick="$('.ocultar').slideToggle(0);" <?php echo _P($PgAt, $_SESSION['admin']['id_usuario'], 'excluir')?>>
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
					<td>Sub Menu</td>
					<td>Referência</td>
					<td align='center'>Ordem</td>
					<td align='center'>Ações</td>
				</tr>
				
				<?php
				$arr_id = [];
                if(isset($GET['codigo_id']) && $GET['codigo_id'] > 0)
                    foreach(ProdutosMenus::all(['conditions' => ['codigo_id=? and id_grupo=?', $GET['codigo_id'], (int)$GET['id_grupo']]]) as $g)
						$arr_id[] = $g->id_subgrupo;
						
				$i = 0;
				
				$maximo = 25;
				
				$pag = isset( $GET['pag'] ) && $GET['pag'] != '' ? $GET['pag'] : 1;
				
				$inicio = (($pag * $maximo) - $maximo);
				
				$conditions = array();
				
				// $conditions['select'] = sprintf('subgrupos.*, (SELECT id_subgrupo FROM produtos_menus WHERE codigo_id=%u AND id_grupo=%u AND id_subgrupo = subgrupos.id GROUP BY 1) as test ', $GET['codigo_id'], $GET['id_grupo']);

				$conditions['conditions'] = sprintf('excluir = 0 and loja_id=%u', $CONFIG['loja_id']);
				
				$conditions['conditions'] .= isset( $GET_PESQUISAR ) && $GET_PESQUISAR != '' ? sprintf(' and subgrupo like "%%%s%%" ', $GET_PESQUISAR)  : '';

				$total = ceil(SubGrupos::count($conditions) / $maximo);
				
				$conditions['order'] = 'subgrupo asc, ordem desc';
				
				$conditions['limit'] = $maximo;
				
				$conditions['offset'] = ($maximo * ($pag - 1));
				
				$result = SubGrupos::all($conditions);
				
				foreach( $result as $rws ) { ?>
				<tr class="formulario<?php echo $rws->id;?> ocultar" <?php echo ($i % 2) ? 'style="background-color:#f3f3f3"': ''?>>
					<td nowrap="nowrap" width="1%">
						<input type="checkbox" name="SubGrupos[<?php echo $rws->id;?>][excluir]" id="label<?php echo $rws->id?>" value="1" data-action="selecionados-exclusao"/>
						<label for="label<?php echo $rws->id?>" class="input-checkbox"></label>
					</td>
					<td>
						<?php echo $rws->parent->id > 0 ? '<span class="ml25"></span>': null?>
						<?php echo $rws->subgrupo?>
						<?php echo !empty($arr_id) && in_array($rws->id, $arr_id) ? '<span class="pull-right btn btn-info btn-xs ft10px">adicionado</span>':null?>
					</td>
					<td align="center" nowrap="nowrap" width="1%">
						<?php echo $rws->parent->subgrupo ?>
					</td>
					<td align="center" nowrap="nowrap" width="1%">
						<?php echo $rws->ordem ?>
					</td>
					<td align="center" nowrap="nowrap" width="1%">
						<a href="/adm/produtos/produtos-grupos-subgrupos.php?id_grupo=<?php echo $GET['id_grupo']?>&id_subgrupo=<?php echo $rws->id?>&codigo_id=<?php echo $GET['codigo_id'];?>" class="btn btn-warning btn-sm btn-adicionar-novo-sub-grupo<?php echo $GET['id_grupo']==''? ' hidden' : ''?>" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'incluir' )?>>adicionar</a>
                        
						<a href='javascript: void(0);' class="btn btn-primary btn-sm" onclick="$('.formulario<?php echo $rws->id?>').slideToggle(0);" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'alterar' )?>>editar</a> 
						
                        <a href='/adm/sub-grupos.php?id_grupo=<?php echo $GET['id_grupo']?>&codigo_id=<?php echo $GET['codigo_id']?>&id=<?php echo $rws->id?>&acao=excluir' class='btn btn-danger btn-sm btn-cadastros-grupos' <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'excluir' )?>>excluir</a>
                        
					</td>
				</tr>
				<tr class="formulario<?php echo $rws->id;?> ocultos lista-zebrada" id='formulario<?php echo $rws->id;?>'>
					<td colspan="4">
						<form class="formulario-subgrupos" action="/adm/sub-grupos.php?id_grupo=<?php echo $GET['id_grupo']?>&codigo_id=<?php echo $GET['codigo_id']?>&acao=editar" method="post">
							<fieldset class="mb15" style="background-color: #fff">
                                <div class="row">
									<div class="col-md-6">
										<div class="mb15 col-xs-12">
											<label>Nome do sub menu:</label>
											<input type='text' value='<?php echo $rws->subgrupo;?>' name='SubGrupos[<?php echo $rws->id;?>][subgrupo]' class="w100"/>
										</div>
										<div class='col-xs-9'>
											<label>Referência do menu:</label>
											<select name="SubGrupos[<?php echo $rws->id;?>][parent_id]" style="width: 100%;">
												<option value="0">Selecione uma opção</option>
												<?php foreach( $result as $rws1 ) { ?>
													<option value="<?php echo $rws1->id?>" <?php echo $rws1->id == $rws->parent_id ? ' selected':''?>><?php echo $rws1->subgrupo?></option>
												<?php } ?>
											</select>
										</div>
										<div class='col-xs-3'>
											<label>Ordem:</label>
											<input type='text' value='<?php echo $rws->ordem;?>' name='SubGrupos[<?php echo $rws->id;?>][ordem]' class="w100"/>
										</div>
									</div>
									<div class="col-md-6">
										<fieldset class="mb15">
											<legend>Informações para SEO</legend>
											<div class='show w100 mb15'>
												<label>Palavras chave: <span class="info-title tooltip" title="Palavras chaves para sistemas de buscas (google).">?</span></label>
												<input type="text" name='SubGrupos[<?php echo $rws->id;?>][subgrupo_keywords]' value='<?php echo $rws->subgrupo_keywords;?>' class="w50 count-input" maxlength="200"/>
											</div>
											<div class='show w100 mb15'>
												<label>Descrição: <span class="info-title tooltip" title="Prévia descrição para os sistema de buscas (google).">?</span></label>
												<textarea name='SubGrupos[<?php echo $rws->id;?>][subgrupo_description]' class="w100 count-input" maxlength="250"><?php echo $rws->subgrupo_description;?></textarea>
											</div>
										</fieldset>
									</div>
									<div class="col-md-12 text-center">
										<button type="submit" class="btn btn-primary btn-cadastros-subgrupos" <?php echo _P($PgAt, $_SESSION['admin']['id_usuario'], 'incluir')?>>
											salvar
										</button>
										<button type="button" class="btn btn-danger" onclick="$('.formulario<?php echo $rws->id;?>').slideToggle(0);" <?php echo _P($PgAt, $_SESSION['admin']['id_usuario'], 'excluir')?>>
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
					<td colspan="4" class="ocultar">
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
										echo sprintf('<a href="/adm/sub-grupos.php?%s" class="btn-paginacao">%s</a>', $data, $i);
										// echo "<a href=\"/adm/sub-grupos.php?pesquisar={$GET_PESQUISAR}&status={$GET_STATUS}&codigo_id={$GET['codigo_id']}&id_grupo={$GET['id_grupo']}&pag={$i}\" class='btn-paginacao'>{$i}</a>";
									}
								}
							}
							?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
        <script>$(".count-input").counter();</script>
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
