<?php
include 'topo.php';

/**
 * Cadastra
 */
if( isset($GET['acao']) && $GET['acao'] === 'cadastrar' ) {
    OpcoesTipo::action_cadastrar_editar($POST, 'cadastrar', 'if');
    header('Location: /adm/opcoes.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Editar
 */
if( isset($GET['acao']) && $GET['acao'] === 'editar' ) {
    OpcoesTipo::action_cadastrar_editar($POST, 'alterar', 'id');
    header('Location: /adm/opcoes.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Excluir
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir' ) {
    OpcoesTipo::action_cadastrar_editar([ 'OpcoesTipo' => [ $GET[id] => ['excluir' => 1] ] ], 'excluir', 'id');
    header('Location: /adm/opcoes.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Remover em massa
 */
if( count( $POST['Marcas'] ) > 0 ) {
    OpcoesTipo::action_cadastrar_editar($POST, 'excluir', 'id');
    header('Location: /adm/opcoes.php?codigo_id=' . $GET['codigo_id']);
    return;
}

$TOTAL_CADASTROS_ATIVOS = OpcoesTipo::count(['conditions'=>['excluir=? and loja_id=?', 0, $CONFIG['loja_id']]]);
// $TOTAL_CADASTROS_GERAL = OpcoesTipo::count();
// $TOTAL_CADASTROS_DESATIVOS = OpcoesTipo::count(['conditions'=>['excluir=?', 1]]);

$GET_STATUS = isset( $POST['status'] ) && $POST['status'] != '' ? $POST['status'] : ( isset( $GET['status'] ) && $GET['status'] != '' ? $GET['status'] : '' );
$GET_PESQUISAR = isset( $GET['pesquisar'] ) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : ( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '' );

?>
<div class="tag-opcoes clearfix panel panel-default">
	<div class="panel-heading panel-store text-uppercase">OPÇÕES</div>
	<div id="div-edicao" class="panel-body">
		<style>
			body{ background-color: #f1f1f1 }
			.ocultos{ display: none; }
		</style>
		<table width="100%" border="0" cellpadding="10" cellspacing="0">
			<tbody>
				<tr class="ocultar">
					<td colspan="3">
						<form action="/adm/opcoes.php?codigo_id=<?php echo $GET['codigo_id']?>" method="post" class="formulario-opcoes_tipo">
							<div class="clearfix mb15" style="line-height: 17px;">
								<!--
								<input type="radio" id="ativo" name="status" value="0" <?php echo isset($GET_STATUS) && $GET_STATUS == '0' ? 'checked' : ''?>/>
								<label for="ativo" class="input-radio"></label>
								Ativos 
								<input type="radio" id="desativar" name="status" value="1" <?php echo isset( $GET_STATUS ) && $GET_STATUS == '1' ? 'checked' : ''?>/>
								<label for="desativar" class="input-radio"></label>
								Desativados	| 
								<span class="cor-001">Total:</span> <span class="ft18px"><?php echo $TOTAL_CADASTROS_GERAL?></span> | 
								<span class="cor-001">Total Ativas:</span> <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> | 
								<span class="cor-001">Total Desativadas:</span> <span class="ft18px"><?php echo $TOTAL_CADASTROS_DESATIVOS?></span> 
								-->
								<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> menus cadastrados</span> 
							</div>
							<input name="pesquisar" type="text" class="w65"/>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
							<button class="btn btn-primary" type="button" onclick="$('.ocultar, .formulario00').slideToggle(0);" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'incluir' )?>>cadastrar</button> 
                            <button class="btn btn-danger" type="button" data-action="btn-excluir-varios" data-href="/adm/opcoes.php?codigo_id=<?php echo $GET['codigo_id']?>" <?php echo _P( 'opcoes-tipo', $_SESSION['admin']['id_usuario'], 'excluir' )?>>
                                excluir seleção
                            </button>
						</form>
					</td>
				</tr>
				
				<tr id="formulario" class="ocultos formulario00">
					<td colspan="3">
						<form class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-1 fieldset" action="/adm/opcoes.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=cadastrar" method="post">
							<div class="clearfix">
								<div class='pull-left w50 mr15 mb15'>
									<label>Ordem da opção:</label>
									<input type="text" name="OpcoesTipo[0][ordem]"/>
								</div>
								<div class='pull-left w50 mr15 mb15'>
									<label>Opção:</label>
									<input type="text" name="OpcoesTipo[0][tipo]" class="w100"/>
								</div>
							</div>
							<button type="submit" class="btn btn-primary btn-cadastros-opcoes_tipo" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'incluir' )?>>salvar</button>
							<button type="button" class="btn btn-danger" onclick="$('.ocultar, .formulario00').slideToggle(0);" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'excluir' )?>>cancela</button>
						</form>
					</td>
				</tr>
			
				<tr class="plano-fundo-adm-003 ocultar">
					<td bgcolor="#ffffff" align="center" nowrap="nowrap" width="1%">
						<input type="checkbox" data-action="selecionados-exclusao-all" class="selecionados-exclusao-all" id="label" value=""/>
						<label for="label" class="input-checkbox"></label>
					</td>
					<td>Opções</td>
					<td align='center'>Ações</td>
				</tr>
				
				<?php
				$i = 0;
				$maximo = 25;
				$pag = isset( $GET['pag'] ) && $GET['pag'] != '' ? $GET['pag'] : 1;
				$inicio = ( $pag * $maximo  ) - $maximo;
				
				$where = sprintf('opcoes_tipo.loja_id=%u ',  $CONFIG['loja_id']);
				
				$where .= isset( $POST['status'] ) && $POST['status'] != '' ? queryInjection( 'and opcoes_tipo.excluir = %u ',  $POST['status'])  : 'and opcoes_tipo.excluir = 0 ';
				
				$where .= isset( $GET['codigo_id'] ) && $GET['codigo_id'] > 0 
					? queryInjection( 
						' AND opcoes.id NOT IN(SELECT produtos_menus.id_grupo FROM produtos_menus WHERE produtos_menus.codigo_id = %u ) ', 
							$GET['codigo_id'] ) : '';
							
				$where .= isset( $GET_PESQUISAR ) && $GET_PESQUISAR != '' ? queryInjection( ' and opcoes_tipo.tipo like "%s" ',  "%{$GET_PESQUISAR}%")  : '';
				
				// echo
				$busca = "select opcoes_tipo.* from opcoes_tipo where {$where} ORDER BY opcoes_tipo.tipo asc";
				$total = ceil( OpcoesTipo::find_num_rows($busca) / $maximo );
				$busca .= " limit {$inicio}, {$maximo}";					
				$result = OpcoesTipo::find_by_sql( $busca );			
				foreach( $result as $rs ) { $rs = $rs->to_array(); ?>
				<tr class="lista-zebrada in-hover formulario<?php echo $rs['id'];?> ocultar" <?php echo ($i % 2) ? 'style="background-color:#f3f3f3"': ''?>>
					<td nowrap="nowrap" width="1%">
						<input type="checkbox" name="OpcoesTipo[<?php echo $rs['id'];?>][excluir]" id="label<?php echo $rs['id']?>" value="1" data-action="selecionados-exclusao"/>
						<label for="label<?php echo $rs['id']?>" class="input-checkbox"></label>
					</td>
					<td>
						<?php echo $rs['tipo'] ?>
					</td>					
					<td align="center" nowrap="nowrap" width="1%">
						<a href="/adm/opcoes.php?codigo_id=<?php echo $GET['codigo_id'];?>&grupoid=<?php echo $rs['id']?>" class="btn btn-warning btn-sm btn-adicionar-novo-tipo<?php echo '' == $GET['codigo_id'] ? ' hidden' : ''?>" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'incluir' )?>>adicionar</a>
						<a href='javascript: void(0);' class="btn btn-primary btn-sm" onclick="$('.ocultar, .formulario<?php echo $rs['id']?>').slideToggle(0);" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'alterar' )?>>editar</a> 
						<a href='/adm/opcoes.php?codigo_id=<?php echo $GET['codigo_id']?>&id=<?php echo $rs['id']?>&acao=excluir' class='btn btn-danger btn-sm btn-cadastros-opcoes_tipo' <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'excluir' )?>>excluir</a>
					</td>
				</tr>
				<tr class="formulario<?php echo $rs['id'];?> ocultos" id="formulario<?php echo $rs['id'];?>">
					<td colspan="3">
						<form class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-1 fieldset" action="/adm/opcoes.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=editar" method="post">
							<div class="clearfix">
								<div class='pull-left w50 mr15 mb15'>
									<label>Ordem da opção:</label>
									<input type="text" value="<?php echo $rs['ordem'];?>" name="OpcoesTipo[<?php echo $rs['id'];?>][ordem]"/>
								</div>
								<div class='pull-left w50 mr15 mb15'>
									<label>Opção:</label>
									<input type="text" value="<?php echo $rs['tipo'];?>" name="OpcoesTipo[<?php echo $rs['id'];?>][tipo]" class="w100"/>
								</div>
							</div>							
                            <input type="hidden" name="OpcoesTipo[<?php echo $rs['id'];?>][id]" value="<?php echo $rs['id'];?>"/>
							<button type="submit" class="btn btn-primary btn-cadastros-opcoes_tipo">salvar</button>
							<button type="button" class="btn btn-danger" onclick="$('.ocultar, .formulario<?php echo $rs['id'];?>').slideToggle(0);">cancela</button>
						</form>
					</td>
				</tr>
				<?php ++$i; } ?>
				<tr>
					<td colspan="3">
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
										echo "<a href=\"/adm/opcoes.php?pesquisar={$GET_PESQUISAR}&status={$GET_STATUS}&codigo_id={$GET['codigo_id']}&pag={$i}\" class='btn-paginacao'>{$i}</a>";
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
		if( href.search('excluir') > '0')
			if( ! confirm("Deseja realmente excluir!") ) return false;
	
	});
</script>
<?php
include 'rodape.php';
