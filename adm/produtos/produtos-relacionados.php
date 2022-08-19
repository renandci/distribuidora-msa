<?php
include '../topo.php';

/**
 * Cadastra
 */
if( isset( $POST['acao'] ) && $POST['acao'] === 'cadastrar_grupo' ) {
    
    ProdutosRelacionadosGrupos::action_cadastrar_editar(['ProdutosRelacionadosGrupos' => [ 0 => [ 'nome_grupo' => $POST['nome_grupo'] ] ] ], 'cadastrar', 'nome_grupo');
    header('Location: /adm/produtos/produtos-relacionados.php?codigo_id=' . $GET['codigo_id'] );
    return;
}

/**
 * Alterar
 */
if( isset( $POST['acao'] ) && $POST['acao'] === 'editar_grupo' ) {
    
    ProdutosRelacionadosGrupos::action_cadastrar_editar(['ProdutosRelacionadosGrupos' => [ $POST['id'] => [ 'nome_grupo' => $POST['nome_grupo'] ] ] ], 'alterar', 'nome_grupo');
    header('Location: /adm/produtos/produtos-relacionados.php?codigo_id=' . $GET['codigo_id'] );
    return;
}

/**
 * Adicionar produto relacionados
 */
if( isset( $GET['acao'] ) && $GET['acao'] === 'adicionar_relacionado' ) {
    ProdutosRelacionados::action_cadastrar_editar(
        ['ProdutosRelacionados' => [ 0 => [ 'grupos_id' => $GET['grupos_id'], 'produtos_id' => $GET['produtos_id'] ] ] ], 'cadastrar', 'produtos_id');
    header("Location: {$_SERVER['HTTP_REFERER']}");
    return;
}

/**
 * Adicionar produto relacionados
 */
if( isset( $GET['acao'] ) && $GET['acao'] === 'remover_relacionado' ) {
    // busca o id primario da tabela em base do produto
    $ID_PROD = ProdutosRelacionados::find('first', ['conditions' => ['produtos_id' => $GET['produtos_id'], 'grupos_id' => $GET['grupos_id'] ]]);
    // remove o produto relacionado
    ProdutosRelacionados::action_cadastrar_editar(['ProdutosRelacionados' => [ $ID_PROD->id => [ 'produtos_id' => $GET['produtos_id'] ] ] ], 'delete', 'produtos_id');
    header("Location: {$_SERVER['HTTP_REFERER']}");
    return;
}


?>
<style>
	body{ background-color: #f1f1f1 }
</style>
<div class="clearfix mb25" id="div-edicao">
	<div class="panel panel-default">
		<div class="panel-heading panel-store">
			Produtos Relacionados 
			<small><?php echo !empty($GET['search']) && $GET['search'] == 1 ? 'Veja os produtos já Relacionados!':'Relacione os produtos!'?></small>
			<a href="/adm/produtos/produtos-relacionados.php" class="btn btn-primary btn-xs pull-right<?php echo empty($GET['acao']) ? ' hidden' : '';?>">voltar</a>
			<a href="/adm/produtos/produtos-relacionados.php?acao=cadastrar_grupo" class="btn btn-primary btn-xs pull-right<?php echo !empty($GET['acao']) ? ' hidden' : '';?>">cadastrar</a>
		</div>
		<div class="panel-body">
	
			<?php if( !empty( $GET['acao'] ) && ($GET['acao'] == 'editar_grupo' || $GET['acao'] == 'cadastrar_grupo') ) {
				$ProdutosRelacionados = ! empty( $GET['acao'] ) && $GET['acao'] == 'editar_grupo' ? ProdutosRelacionadosGrupos::find( $GET['id'] ) : null;
				$grupo = count($ProdutosRelacionados) ? $ProdutosRelacionados->to_array() : null;
			?>
			<form action="/adm/produtos/produtos-relacionados.php?codigo_id=<?php echo $GET['codigo_id']?>" class="col-lg-4 col-lg-offset-3 col-md-4 col-md-offset-4 fieldset" method="post">
				<p>Digite o nome do Grupo</p>
				<input type='text' name='nome_grupo' id='nome_grupo' class='w100' value='<?php echo $grupo['nome_grupo']?>'/>
				<button type='submit' class='w30 btn btn-primary mt15'>salvar</button>
				<input type='hidden' name='acao' value='<?php echo $GET['acao']?>'/>
				<input type='hidden' name='id' value='<?php echo (INT)$GET['id']?>'/>
			</form>
			<?php } 
			else if( ! empty( $GET['acao'] ) && $GET['acao'] == 'produtos_agrupados' ) { ?>
				<?php 
				$ProdutosRelacionadosGrupos = ProdutosRelacionadosGrupos::find($GET['grupos_id']); 
				$rsGrupo = count( $ProdutosRelacionadosGrupos ) ? $ProdutosRelacionadosGrupos : '';
				?>
				<h4>Grupo: <?php echo $rsGrupo->nome_grupo?></h4>
				<form action="/adm/produtos/produtos-relacionados.php" class="mb15 col-lg-12 formulario-grupos">
					<b class="cor-001">Pesquisar:</b>
					<input type="hidden" name="grupos_id" value="<?php echo $GET['grupos_id']?>"/>
					<input type="hidden" name="search" value="<?php echo $GET['search']?>"/>
					<input type="hidden" name="acao" value="<?php echo $GET['acao']?>"/>
					<input type="text" name="q" size="50"/>
					<button type="submit" class="btn btn-primary"><?php echo !empty($GET['q']) ? 'limpar pesquisa':'pesquisar';?></button>
					<a href="/adm/produtos/produtos-relacionados.php?acao=<?php echo $GET['acao']?>&grupos_id=<?php echo $GET['grupos_id']?>&search=1" class="btn btn-danger<?php echo !empty($GET['search']) && $GET['search'] == 1 ? '-default':'';?>">
						produtos relacionados
					</a>
					<a href="/adm/produtos/produtos-relacionados.php?acao=<?php echo $GET['acao']?>&grupos_id=<?php echo $GET['grupos_id']?>&search=2" class="btn btn-warning<?php echo !empty($GET['search']) && $GET['search'] == 2 ? '-default':'';?>">
						produtos para relacionar
					</a>
				</form>
				<div class="clearfix" id="recarregar-relacionados">
					<?php
					$where = sprintf('produtos.loja_id = %u and ', $CONFIG['loja_id']); 

					$pesquisar = !empty($GET['q']) && $GET['q'] ? filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING) : '';
					/**
					 * Aplica se no somente se hover pesquisa ou tela para buscar os produtos
					 */
					$where .= ! empty( $GET['q'] ) && $GET['q'] != '' ? ""
							. "produtos.nome_produto like "
								. "'%{$pesquisar}%' "
									. "or(produtos.nome_produto like '%" . implode('%" and produtos.nome_produto like "%', explode(' ', $pesquisar)) . "%') and " 
										: '';

					/**
					 * Aplica se no somente se não hover pesquisa ou tela para buscar os produtos
					 */
					$where .= ! empty( $GET['search'] ) && $GET['search'] == '1' ? ''
						. 'exists('
							. 'select 1 from produtos_relacionados '
								. queryInjection('where produtos_relacionados.produtos_id = produtos.id and produtos_relacionados.grupos_id=%u) and ', $GET['grupos_id'])
									: queryInjection('produtos.id not in(select produtos_id from produtos_relacionados where grupos_id=%u) and ', $GET['grupos_id']);
					
					$where .= ! empty( $GET['search'] ) && $GET['search'] == '2' ? ''
						. 'not exists( select 1 from produtos_relacionados  '
							. queryInjection('where produtos_relacionados.produtos_id = produtos.id and produtos_relacionados.grupos_id=%u) and ', $GET['grupos_id']) : '';
					
					/**
					 * Aplica se para remover os produtos que já estão relacionados
					 */
					$where_join = ! empty( $GET['search'] ) && $GET['search'] == '1' ? 'inner join produtos_relacionados on produtos_relacionados.produtos_id = produtos.id ' : '';

	//                echo
					$query = ''
							. 'select '
							. 'produtos.*, '
							. 'cores.nomecor, '
							. 'tamanhos.nometamanho, '
							. 'a.tipo as tipoa, '
							. 'b.tipo as tipob, '
							. 'produtos_imagens.imagem '
							. 'from produtos '
							. $where_join
							. 'inner join produtos_imagens on produtos_imagens.codigo_id=produtos.codigo_id and produtos_imagens.cor_id=produtos.id_cor '
							. 'left join cores on cores.id=produtos.id_cor '
							. 'left join tamanhos on tamanhos.id=produtos.id_tamanho '
							. 'left join opcoes_tipo a on a.id=cores.opcoes_id '
							. 'left join opcoes_tipo b on b.id=tamanhos.opcoes_id '
							. 'where '
							. $where
							. ' produtos_imagens.capa=1 '
							. 'group by produtos.id ';
					
					$i = 0;
					$max = 24;	
					$pag = isset( $GET['pag'] ) &&  $GET['pag'] != '' ? $GET['pag'] : 1; 
					$ini_pag = ( $pag * $max ) - $max;
					$total = ( ceil( ProdutosRelacionados::connection()->query( $query )->rowCount() ) / $max );
					$query .= sprintf('limit %u, %u', $ini_pag, $max);
					$result = ProdutosRelacionados::connection()->query($query);
					
					if( $total == 0 ){ ?>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center mt50 mb50 ft20px">
						Nenhum produto relacionado ou encontrado!
					</div>
					<?php }
					while( $rs = $result->fetch() ) { ?>
						<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 text-center mb15" style="min-height: 480px;">
							<img src="<?php echo Imgs::src($rs['imagem'], 'smalls')?>" class="img-responsive center-block"/>
							<h4><?php echo $rs['nome_produto'] ?></h4>
							<?php echo !empty( $rs['tipoa'] ) ? "<span class='show mb5'>{$rs['tipoa']}: {$rs['nomecor']}</span>" : ''?>
							<?php echo !empty( $rs['tipob'] ) ? "<span class='show'>{$rs['tipob']}: {$rs['nometamanho']}</span>" : ''?>
							<?php echo !empty( $GET['search'] ) && $GET['search'] == 2 ? "<a href='/adm/produtos/produtos-relacionados.php?acao=adicionar_relacionado&grupos_id={$GET['grupos_id']}&produtos_id={$rs['id']}&search={$GET['search']}' class='mt5 btn btn-block btn-primary' data-btn='relacao-adicionar'>adicionar</a>" : ''?>
							<?php echo !empty( $GET['search'] ) && $GET['search'] == 1 ? "<a href='/adm/produtos/produtos-relacionados.php?acao=remover_relacionado&grupos_id={$GET['grupos_id']}&produtos_id={$rs['id']}&search={$GET['search']}' class='mt5 btn btn-block btn-danger' data-btn='relacao-remover'>remover</a>" : ''?>
						</div>
					<?php } ?>
				</div>
				<div class="paginacao clearfix">
					<?php
					if( $total > 0 )
					{
						if( $pag != 1 )
						{ 
							echo "<a href=\"/adm/produtos/produtos-relacionados.php?acao={$GET['acao']}&q={$GET['q']}&grupos_id={$GET['grupos_id']}&search={$GET['search']}&pag=1\">Primeira página</a>";
						}

						for( $i = $pag - 10, $limiteDeLinks = $i + 20; $i <= $limiteDeLinks; ++$i )
						{
							if($i < 1)
							{
								$i = 1;
								$limiteDeLinks = 19;
							}

							if($limiteDeLinks > $total)
							{
								$limiteDeLinks = $total; 
								$i = $limiteDeLinks - 20;
							}

							if( $i < 1 )
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
								echo "<a href=\"/adm/produtos/produtos-relacionados.php?acao={$GET['acao']}&q={$GET['q']}&grupos_id={$GET['grupos_id']}&search={$GET['search']}&pag={$i}\">{$i}</a>";
							}
						} 

						if( $pag != $total )
						{  
							if( $pag == $i && $total > 0 )
							{ 
								echo "<span class=\"lipg\">Última página</span>";
							}
							else
							{ 
								echo "<a href=\"/adm/produtos/produtos-relacionados.php?acao={$GET['acao']}&q={$GET['q']}&search={$GET['search']}&grupos_id={$GET['grupos_id']}&pag={$total}\">Última página</a>"; 
							}
						}
					}
					?>
				</div>
			<?php } else { ?>
			<table width="100%" cellpadding="8" cellspacing="0" border="0" class="table table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>NOME GRUPO</th>
							<th class="text-center">AÇÕES</th>
						</tr>
					</thead>
					<tbody>				
						<?php
						$sql = sprintf('SELECT id, nome_grupo FROM produtos_relacionados_grupos WHERE loja_id=%u ', $CONFIG['loja_id']);
						$sql .= ! empty($GET['q']) && $GET['q'] != '' ? queryInjection(' and nome_grupo like "%%%s%%" ', $GET['q']) : '';
						$sql .= 'order by nome_grupo asc ';
						
						$i 			= 0;
						$max 		= 50;	
						$pag 		= isset( $GET['pag'] ) &&  $GET['pag'] != '' ? $GET['pag'] : 1;
						$total 		= ceil ( ProdutosRelacionadosGrupos::connection()->query( $sql )->rowCount() / $max );

						$sql .= sprintf('limit %u offset %u', $max, ($max * ($pag - 1)));
						
						$result	= ProdutosRelacionadosGrupos::connection()->query( $sql );
						
						while( $rs = $result->fetch() ) { ?>
						<tr id='excluir<?php echo $rs['id']?>' class="lista-zebrada in-hover">
							<td nowrap="nowrap" width="1%" align="center">
								<?php echo $rs['id']?>
								<input type='checkbox' name='excluir_varios[]' class='excluir_varios' id='label<?php echo $rs['codigo_id']?>' value='<?php echo $rs['codigo_id']?>'/>
								<label for='label<?php echo $rs['codigo_id']?>' class='input-checkbox hidden'></label>
							</td>
							<td><?php echo $rs['nome_grupo'];?></td>
							<td nowrap="nowrap" width="1%" align="center">							
								<a href="/adm/produtos/produtos-relacionados.php?acao=editar_grupo&id=<?php echo $rs['id'];?>" class="ml5 btn btn-warning btn-sm">alterar</a>
								<a href="/adm/produtos/produtos-relacionados.php?acao=produtos_agrupados&grupos_id=<?php echo $rs['id'];?>&search=1" class="ml5 btn btn-primary btn-sm">adicionar/remover</a>
								<a href="/adm/produtos/produtos-relacionados.php?acao=ExcluirRelacionado&id=<?php echo $rs['id'];?>" class="ml5 btn btn-danger btn-sm">excluir</a>
							</td>
						</tr>
						<?php ++$i; } ?>
						<tr>
							<td colspan="3">
								<div class="paginacao clearfix">
									<?php
									if( $total > 0 )
									{
										if( $pag != 1 )
										{ 
											echo "<a href=\"/adm/produtos/produtos-relacionados.php?q={$GET['q']}&pag=1\">Primeira página</a>";
										}

										for( $i = $pag - 10, $limiteDeLinks = $i + 20; $i <= $limiteDeLinks; ++$i )
										{
											if($i < 1)
											{
												$i = 1;
												$limiteDeLinks = 19;
											}

											if($limiteDeLinks > $total)
											{
												$limiteDeLinks = $total; 
												$i = $limiteDeLinks - 20;
											}

											if( $i < 1 )
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
												echo "<a href=\"/adm/produtos/produtos-relacionados.php?q={$GET['q']}&pag={$i}\">{$i}</a>";
											}
										} 

										if( $pag != $total )
										{  
											if( $pag == $i && $total > 0 )
											{ 
												echo "<span class=\"lipg\">Última página</span>";
											}
											else
											{ 
												echo "<a href=\"/adm/produtos/produtos-relacionados.php?q={$GET['q']}&pag={$total}\">Última página</a>"; 
											}
										}
									}
									?>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			<?php } ?>
		</div>
	</div>
</div>
<?php
include '../rodape.php';