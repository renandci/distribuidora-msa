<?php
include '../topo.php';

/**
 * Cadastrar
 */
if( isset($GET['acao']) && $GET['acao'] === 'cadastrar' ) {
    ProdutosDescricoesAbas::action_cadastrar_editar($POST, 'cadastrar', 'aba');
	header('Location: /adm/produtos/produtos-descricoes-abas.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Editar
 */
if( isset($GET['acao']) && $GET['acao'] === 'editar' ) {
    ProdutosDescricoesAbas::action_cadastrar_editar($POST, 'alterar', 'aba');
	header('Location: /adm/produtos/produtos-descricoes-abas.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Excluir
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir' ) {
    ProdutosDescricoesAbas::action_cadastrar_editar([ 'ProdutosDescricoesAbas' => [ $GET['id'] => ['codigo_id' => $GET['codigo_id']] ] ], 'delete', 'aba');
	header('Location: /adm/produtos/produtos-descricoes-abas.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Remover em massa
 */
if( isset( $POST['ProdutosDescricoesAbas'] ) && count($POST['ProdutosDescricoesAbas']) > 0 ) {
    ProdutosDescricoesAbas::action_cadastrar_editar($POST, 'delete', 'aba');
	header('Location: /adm/produtos/produtos-descricoes-abas.php?codigo_id=' . $GET['codigo_id']);
    return;
}

$ProdutosDescricoesAbas = ProdutosDescricoesAbas::all([ 'order' => 'aba asc', 'group' => 'aba' ]);
//$TOTAL_CADASTROS_GERAL = ProdutosDescricoesAbas::count(['conditions' => ['codigo_id=?', $GET['codigo_id']]]);
//$TOTAL_CADASTROS_ATIVOS = ProdutosDescricoesAbas::count(['conditions' => ['codigo_id=?', $GET['codigo_id']]]);
//$TOTAL_CADASTROS_DESATIVOS = ProdutosDescricoesAbas::count(['conditions' => ['excluir=?', 1]]);

$GET_STATUS = isset( $POST['status'] ) && $POST['status'] != '' ? $POST['status'] : ( isset( $GET['status'] ) && $GET['status'] != '' ? $GET['status'] : '' );
$GET_PESQUISAR = isset( $GET['pesquisar'] ) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : ( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '' );
?>
<div class="tag-opcoes clearfix">
	<h2>DESCRIÇÕES ABAS</h2>
	<div id="abaDescricoes">
        <style>
            .ocultos{
                display: none;
            }
        </style>
		<table width="100%" border="0" cellpadding="10" cellspacing="0">
			<tbody>
				<tr class="ocultar">
					<td colspan="3">
						<form action="/adm/produtos/produtos-descricoes-abas.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=cadastrar" method="post" class="formulario-descricao" id="00_form">
							<div class="clearfix mb15" style="line-height: 17px;">
								<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> descrições cadastradas</span>
							</div>
							<input name="pesquisar" type="text" class="w55"/>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
							<button class="btn btn-primary" type="button" data-init="editor-00" onclick="$('.ocultar').slideToggle(0);" <?php echo _P( 'produtos-descricoes-abas', $_SESSION['admin']['id_usuario'], 'incluir' )?>>cadastrar</button>

                            <button class="btn btn-danger" type="button" data-action="btn-excluir-varios" data-href="/adm/produtos/produtos-descricoes-abas.php?codigo_id=<?php echo $GET['codigo_id']?>" <?php echo _P( 'produtos-descricoes-abas', $_SESSION['admin']['id_usuario'], 'excluir' )?>>
                                excluir seleção
                            </button>
						</form>
					</td>
				</tr>
				
				<tr id="formulario" class="ocultos ocultar">
					<td colspan="3">
						<form class="formulario-produtos_descricoes" action="/adm/produtos/produtos-descricoes-abas.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=cadastrar" method="post">
                            <input type="hidden" name="ProdutosDescricoesAbas[0][codigo_id]" value="<?php echo $GET['codigo_id']?>"/>
							<div class="clearfix">
								<div class="w20 mr15 pull-left">
									<p>Nome da Aba:</p>
                                    <select name="ProdutosDescricoesAbas[0][aba]" style="width: 100%">
                                    <?php foreach ( $ProdutosDescricoesAbas as $abas ){ ?>
                                        <option value="<?php echo $abas->aba?>"><?php echo $abas->aba?></option>
                                    <?php } ?>
                                    </select>
                                    <span class="info-title tooltip" title="Selecione uma descrição já cadastrada ou digite e aperte enter para cadastra uma nova aba.">?</span>
								</div>
<!--								<div class="w10 mr15 pull-left">
									<p>Ordem da Aba:</p>
									<input type="text" name="ProdutosDescricoesAbas[0][ordem]" class="w100"/>
								</div>-->
							</div>
							<div class="show w100 mb15">
								<p>Descricão da Aba:</p>
								<textarea name="ProdutosDescricoesAbas[0][descricao]" class="w100 produtos-descricao" rows="15" id="editor-00"></textarea>
							</div>
							
							<button type="submit" class="btn btn-primary btn-cadastros-produtos_descricoes" 
                                <?php echo _P( 'produtos-descricoes-abas', $_SESSION['admin']['id_usuario'], 'alterar|incluir' )?>>salvar</button>
							<button type="button" class="btn btn-danger" onclick="$('.ocultar').slideToggle(0);">cancelar</button>
						</form>
					</td>
				</tr>
			
				<tr class="plano-fundo-adm-003 ocultar">
					<td bgcolor="#ffffff" align="center" nowrap="nowrap" width="1%">
                        <input type="checkbox" data-action="selecionados-exclusao-all" class="selecionados-exclusao-all" id="label" value=""/>
						<label for="label" class="input-checkbox"></label>
					</td>
					<td>Abas</td>
					<td align='center'>Ações</td>
				</tr>
				
				<?php
				$i = 0;
				$maximo = 25;
				$pag = ! empty( $GET['pag'] ) && $GET['pag'] > 0 ? (INT)$GET['pag'] : 1;
				$inicio = (( $pag * $maximo ) - $maximo);
                
                $conditions['conditions'] = ['codigo_id=?', $GET['codigo_id']];
                
                /**
                 * Pesquisar dados
                 */
                if( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ) {
                    $conditions['conditions'] = ['codigo_id=? and aba like ? or (descricao like ?)', $GET['codigo_id'], "%{$POST['pesquisar']}%", "%{$POST['pesquisar']}%"];
                }

				$total = ceil((ProdutosDescricoesAbas::count($conditions) / $maximo));
                $conditions['order'] = 'aba asc';
				$conditions['limit'] = $maximo;
				$conditions['offset'] = $inicio;
                
                $aba = '';
				$result = ProdutosDescricoesAbas::all($conditions);
				foreach( $result as $rs ) { ?>
                <?php $rs = $rs->to_array(); ?>
                <?php if( $aba != $rs['aba'] ) { $aba = $rs['aba']; ?>
				<tr class="in-hover formulario<?php echo $rs['id'];?> ocultar">
                    <td colspan="3" class="ft18px">
                        ABA: <?php echo $rs['aba'];?>
                    </td>
                </tr>
                <?php } ?>
				<tr class="lista-zebrada in-hover formulario<?php echo $rs['id'];?> ocultar">
					<td nowrap="nowrap" width="1%">

                        <input type="checkbox" name="ProdutosDescricoesAbas[<?php echo $rs['id'];?>][excluir]" id="label<?php echo $rs['id']?>" value="1" data-action="selecionados-exclusao"/>
						<label for="label<?php echo $rs['id'];?>" class="input-checkbox"></label>
					</td>
					<td>
						<?php echo substr(strip_tags($rs['descricao']), 0, 155);?>...
					</td>
					<td align="center" nowrap="nowrap" width="1%">
						<a href='javascript: void(0);' class="btn btn-primary btn-sm" data-init="editor-<?php echo $rs['id']?>" onclick="$('.formulario<?php echo $rs['id']?>').slideToggle(0);" <?php echo _P( 'produtos-descricoes-abas', $_SESSION['admin']['id_usuario'], 'alterar' )?>>editar</a> 
						<a href='/adm/produtos/produtos-descricoes-abas.php?codigo_id=<?php echo $GET['codigo_id']?>&id=<?php echo $rs['id']?>&acao=excluir' class='btn btn-primary btn-sm btn-excluir-modal' <?php echo _P( 'produtos-descricoes-abas', $_SESSION['admin']['id_usuario'], 'excluir' )?> data-excluir>excluir</a>
					</td>
				</tr>
				<tr class="formulario<?php echo $rs['id'];?> ocultos lista-zebrada" id='formulario<?php echo $rs['id'];?>'>
					<td colspan="3">
						<form class="formulario-produtos-descricoes" action="/adm/produtos/produtos-descricoes-abas.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=editar" method="post">
                            <input type="hidden" name="ProdutosDescricoesAbas[<?php echo $rs['id'];?>][codigo_id]" value="<?php echo $GET['codigo_id']?>"/>
							<div class="clearfix">
								<div class='pull-left w30 mr15'>
									<p>Nome da Aba:</p>
                                    <select name="ProdutosDescricoesAbas[<?php echo $rs['id'];?>][aba]" style="width: 100%">
                                    <?php foreach ( $ProdutosDescricoesAbas as $abas ){ ?>
                                        <option value="<?php echo $abas->aba?>" <?php echo $rs['aba'] == $abas->aba ? 'selected':''?>>
                                            <?php echo $abas->aba?>
                                        </option>
                                    <?php } ?>
                                    </select>
								</div>
								<div class='pull-left w10'>
									<p>Ordem da Aba:</p>
									<input type='text' value='<?php echo $rs['ordem'];?>' name='ProdutosDescricoesAbas[<?php echo $rs['id'];?>][ordem]' class="w100"/>
								</div>
							</div>
							<div class='show w100 mb15'>
								<p>Descricão da Aba:</p>
								<textarea name='ProdutosDescricoesAbas[<?php echo $rs['id'];?>][descricao]' class='w100 produtos-descricao' rows='15' id='editor-<?php echo $rs['id']?>'><?php echo $rs['descricao'];?></textarea>
							</div>
							
							<button type="submit" class="btn btn-primary btn-cadastros-produtos_descricoes init-editor" <?php echo _P('produtos-descricoes-abas', $_SESSION['admin']['id_usuario'], 'alterar')?>>
                                salvar
                            </button>
							<button type="button" class="btn btn-danger destroy-editor" onclick="$('.formulario<?php echo $rs['id'];?>').slideToggle(0);">
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
									
									if($i == $pag)
									{
										echo "<span class=\"at plano-fundo-adm-001\">{$i}</span>";
									}
									else
									{							
										echo "<a href=\"/adm/produtos/produtos-descricoes-abas.php?codigo_id={$GET['codigo_id']}&pag={$i}\" class='btn-paginacao'>{$i}</a>";
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
    <script>
        $(document).on("click", "a", function(){
            var href = this.href || e.target.href;		
            if( href.search('excluir') > '0')
                if( ! confirm("Deseja realmente excluir!") ) return false;

        });
    </script>
</div>
<?php
include '../rodape.php';