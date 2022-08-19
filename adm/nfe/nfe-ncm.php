<?php
include dirname(__DIR__) . '/topo.php';

/**
 * Cadastra
 */
if( isset($GET['acao']) && $GET['acao'] === 'cadastrar' ) {

	if( count( $POST ) > 0 )
    	NfeNcm::action_cadastrar_editar($POST, 'cadastrar', 'nfencm');
    header('Location: /adm/nfe/nfe-ncm.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Editar
 */
if( isset($GET['acao']) && $GET['acao'] === 'editar' ) {
	if( count( $POST ) > 0 )
    	NfeNcm::action_cadastrar_editar($POST, 'alterar', 'nfencm');
    header('Location: /adm/nfe/nfe-ncm.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Excluir
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir' ) {
    NfeNcm::action_cadastrar_editar([ 'NfeNcm' => [ $GET['id'] => ['excluir' => 1] ] ], 'excluir', 'nfencm');
    header('Location: /adm/nfe/nfe-ncm.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Remover em massa
 */
if( count( $POST['NfeNcm'] ) > 0 ) {
    NfeNcm::action_cadastrar_editar($POST, 'excluir', 'nfencm');
    header('Location: /adm/nfe/nfe-ncm.php?codigo_id=' . $GET['codigo_id']);
    return;
}


$TOTAL_CADASTROS_GERAL = NfeNcm::count();
// $TOTAL_CADASTROS_ATIVOS = NfeNcm::count(['conditions'=>['excluir=?', 0]]);
// $TOTAL_CADASTROS_DESATIVOS = NfeNcm::count(['conditions'=>['excluir=?', 1]]);

$GET_STATUS = isset( $POST['status'] ) && $POST['status'] != '' ? $POST['status'] : ( isset( $GET['status'] ) && $GET['status'] != '' ? $GET['status'] : '' );
$GET_PESQUISAR = isset( $GET['pesquisar'] ) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : ( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '' );
?>
<style>
	.ocultos{display: none; }
	body{ background-color: #f1f1f1 }
</style>
<div class="panel panel-default mb25 mt50">
	<div class="panel-heading panel-store text-uppercase">NCM</div>
	<div id="div-edicao" class="panel-body">
		<table width="100%" border="0" cellpadding="8" cellspacing="0" id="tabela-nfencm">
			<tbody>
				<tr class="ocultar">
					<td colspan="4">
						<form action="/adm/nfe/nfe-ncm.php?codigo_id=<?php echo $GET['codigo_id']?>" method="post" class="formulario-nfencm">
							<div class="clearfix mb15" style="line-height: 17px;">
								<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_GERAL?></span> ncm cadastradas</span>
							</div>
							<input name="pesquisar" type="text" class="w50"/>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
							<button class="btn btn-primary" type="button" <?php echo _P('nfencm', $_SESSION['admin']['id_usuario'], 'incluir')?> onclick="$('#formulario').slideToggle(0);">
                                cadastrar
                            </button>
							<button class="btn btn-danger" type="button" data-action="btn-excluir-varios" data-href="/adm/nfe/nfe-ncm.php?codigo_id=<?php echo $GET['codigo_id']?>" <?php echo _P( 'nfencm', $_SESSION['admin']['id_usuario'], 'excluir' )?>>
                                excluir seleção
                            </button>
						</form>
					</td>
				</tr>
				
				<tr id="formulario" class="ocultos ocultar">
					<td colspan="4">
                        <form class="formulario-nfencm col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-1" action="/adm/nfe/nfe-ncm.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=cadastrar" method="post" enctype="multipart/form-data">
							<div class="clearfix fieldset mt15 mb15">
								<div class="col-lg-4">
									<p>Ncm:</p>
									<input type="text" value="" name="NfeNcm[0][ncm]" class="w100"/>
								</div>
								<div class="col-lg-12">
									<p>Descrição:</p>
									<input type="text" value="" name="NfeNcm[0][descricao]" class="w100"/>
								</div>
								<div class="col-lg-4">
									<p>Valor Alicota :</p>
									<input type="text" value="" name="NfeNcm[0][aliqnac]" class="w100"/>
								</div>
								<div class="col-lg-4">
									<p>Imposto Alicota:</p>
									<input type="text" value="" name="NfeNcm[0][aliqimp]" class="w100"/>
								</div>
								<div class="col-lg-12 mt15">
                                    <button type="submit" class="btn btn-primary btn-cadastros-nfencm" <?php echo _P('nfencm', $_SESSION['admin']['id_usuario'], 'alterar|incluir')?>>
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
					<td>NCM</td>
					<td align='center'>Ações</td>
				</tr>
				
				<?php
				$i = 0;
				
				$maximo = 25;
				
				$pag = isset( $GET['pag'] ) && $GET['pag'] != '' ? $GET['pag'] : 1;
				
				$inicio = (($pag * $maximo) - $maximo);
				
				$conditions = array();
				
				// $conditions['conditions'] = sprintf('excluir = 0 and loja_id=%u', $CONFIG['loja_id']);
				$conditions['conditions'] = sprintf('id > 0 ');
				
				$conditions['conditions'] .= isset( $GET_PESQUISAR ) && $GET_PESQUISAR != '' ? sprintf(' and ncm like "%%%s%%" ', addslashes($GET_PESQUISAR)) : '';
								
				$total = ceil(NfeNcm::count($conditions) / $maximo);
				
				$conditions['order'] = 'ncm asc';
				
				$conditions['limit'] = $maximo;
				
				$conditions['offset'] = ($maximo * ($pag - 1));
				
				$result = NfeNcm::all( $conditions );
				
				foreach ( $result as $rs ) { ?>
				<tr class="lista-zebrada in-hover formulario<?php echo $rs->id;?> ocultar" <?php echo ($i % 2) ? 'style="background-color:#f3f3f3"': ''?>>
					<td nowrap="nowrap" width="1%">
                        <input type="checkbox" name="NfeNcm[<?php echo $rs->id;?>][excluir]" id="label<?php echo $rs->id?>" value="1" data-action="selecionados-exclusao"/>
						<label for="label<?php echo $rs->id?>" class="input-checkbox"></label>
					</td>
					<td>
						<?php echo $rs->ncm?>
					</td>
					<td align="center" nowrap="nowrap" width="1%">
						<a href="javascript: void(0);" class="btn btn-primary btn-sm" onclick="$('#tabela-nfencm #formulario<?php echo $rs->id?>').slideToggle(0);" <?php echo _P('nfencm', $_SESSION['admin']['id_usuario'], 'alterar')?>>editar</a> 
						<!-- <a href="/adm/nfe/nfe-ncm.php?codigo_id=<?php echo $GET['codigo_id']?>&id=<?php echo $rs->id?>&acao=excluir" class="btn btn-danger btn-sm btn-excluir-modal" <?php echo _P('nfencm', $_SESSION['admin']['id_usuario'], 'excluir')?>>excluir</a> -->
					</td>
				</tr>
				<tr class="formulario<?php echo $rs->id;?> ocultos" id="formulario<?php echo $rs->id;?>">
					<td colspan="4">
						<form class="formulario-nfencm col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-1 fieldset" action="/adm/nfe/nfe-ncm.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=editar" method="post" enctype="multipart/form-data">
							<div class="clearfix fieldset mt15 mb15">
								<div class="col-lg-4">
									<p>Ncm:</p>
									<input type="text" value="<?php echo $rs->ncm;?>" name="NfeNcm[<?php echo $rs->id;?>][ncm]" class="w100"/>
								</div>
								<div class="col-lg-12">
									<p>Descrição:</p>
									<input type="text" value="<?php echo $rs->descricao;?>" name="NfeNcm[<?php echo $rs->id;?>][descricao]" class="w100"/>
								</div>
								<div class="col-lg-4">
									<p>Valor Alicota :</p>
									<input type="text" value="<?php echo $rs->aliqnac;?>" name="NfeNcm[<?php echo $rs->id;?>][aliqnac]" class="w100"/>
								</div>
								<div class="col-lg-4">
									<p>Imposto Alicota:</p>
									<input type="text" value="<?php echo $rs->aliqimp;?>" name="NfeNcm[<?php echo $rs->id;?>][aliqimp]" class="w100"/>
								</div>
								<div class="col-lg-12 mt15">
                                    <button type="submit" class="btn btn-primary btn-cadastros-nfencm" <?php echo _P('nfencm', $_SESSION['admin']['id_usuario'], 'alterar|incluir')?>>
                                        salvar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="$('#tabela-nfencm #formulario<?php echo $rs->id;?>').slideToggle(0);">cancela</button>
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
										echo sprintf('<a href="/adm/nfe/nfe-ncm.php?%s" class="btn-paginacao">%s</a>', $data, $i);
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
include dirname(__DIR__) . '/rodape.php';