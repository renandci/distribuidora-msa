<?php
include '../topo.php';

$codigo_id_produtos = filter_input(INPUT_POST, 'codigo_id', FILTER_SANITIZE_NUMBER_INT);

$codigo_id = filter_input(INPUT_GET, 'codigo_id', FILTER_SANITIZE_NUMBER_INT);

$acao = filter_input(INPUT_POST, 'acao', FILTER_SANITIZE_STRING|FILTER_SANITIZE_MAGIC_QUOTES);

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$q = filter_input(INPUT_POST, 'q', FILTER_SANITIZE_STRING|FILTER_SANITIZE_MAGIC_QUOTES);

if( filter_input(INPUT_GET, 'acao') == 'remover_to_kit' ) {
	
	$ProdutosKits = ProdutosKits::find($id);
	$ProdutosKits->delete_log(['id' => $id]);
	
	header(sprintf('location: /adm/produtos/produtos-kits.php?codigo_id=%u', $codigo_id));
	return;
}

if( ! empty( $acao ) && $acao == 'add_prod_kit' ) {
	
	ProdutosKits::action_cadastrar_editar([ 'ProdutosKits' => [ 0 => ['codigo_id' => $codigo_id, 'codigo_id_produtos' => $codigo_id_produtos] ] ], 'alterar', 'codigo_id');
	
	header(sprintf('location: /adm/produtos/produtos-kits.php?codigo_id=%u', $codigo_id));
	return;
}

if( ! empty( $acao ) && $acao == 'search_prod' ) {
	
	$return_arr = array();
	$Produtos = Produtos::all(['conditions' => ['loja_id=? and nome_produto like ?', $CONFIG['loja_id'], sprintf("%%%s%%", $q)], 'limit' => '10']);
	foreach( $Produtos as $rs ) 
	{
		$row_array['label']  = CodProduto($rs->nome_produto, $rs->id, $rs->codigo_produto) . ' - ' . $rs->nome_produto; 
		$row_array['codigo'] = CodProduto($rs->nome_produto, $rs->id, $rs->codigo_produto);
		$row_array['codigo_id'] = $rs->codigo_id;

		array_push($return_arr, $row_array); 
	}
	
	echo sprintf('<span id="search_prod">%s</span>', json_encode($return_arr));
	return;
}

?>
<div id="grid_kits">
    <table class="table">
		<thead>
			<tr class="plano-fundo-adm-003">
				<th colspan="5" color="#ffffff">
					<form action="/adm/produtos/produtos-kits.php">
						<div class="form-group">
							<input type="text" class="form-control" name="q" id="search" placeholder="Pesquise o produto para adicionar ao kit"/>
							<!--
							<div class="col-sm-10">
							</div>
							<label for="search" class="col-sm-2 control-label">
								<button class="btn btn-success" type="button"> adicionar</button>
							</label>
							-->
						</div>
					</form>
				</th>
			</tr>
		</thead>
		<tbody id="tbody">
			<?php
			$Total = 0;
			$Produtos = Produtos::first(['conditions' => ['codigo_id=?', $codigo_id]]);
			foreach( $Produtos->grid_kits as $rws )  { ?>
			<tr class="<?php echo empty($rws->produto->ncm) ? 'danger':''?>">
				<td><?php echo $rws->nome_produto?></td>
				<td nowrap="nowrap" width="1%"><?php echo !empty($rws->produto->ncm) ? $rws->ncm : 'produto sem ncm'?></td>
				<td nowrap="nowrap" width="1%">R$: <?php echo number_format($rws->produto->preco_promo, 2, ',', '.')?></td>
				<td nowrap="nowrap" width="1%"><?php echo $rws->qtde?></td>
				<td nowrap="nowrap" width="1%">
					<a href="/adm/produtos/produtos-kits.php?codigo_id=<?php echo $codigo_id?>&acao=remover_to_kit&id=<?php echo $rws->id?>" class="btn btn-danger btn-xs btn_remove_to_kit">
						remover
					</a>
				</td>
			</tr>
			<?php 
			$Total += $rws->produto->preco_promo * $rws->qtde; 
			?>
			<?php } ?>
		</tbody>
		</tfooter>
			<tr>
				<td colspan="5" align="right">
					<?php // =$Total - $Produtos->preco_promo?>
				</td>
			</tr>
		</tfooter>
    </table>
	<script>
		$("#tbody").on("click", ".btn_remove_to_kit", function(e){
			e.preventDefault();
			
			if( ! confirm('Deseja realmente remover?') ) return;
			
			$.ajax({
				url: this.href||e.target.href,
				success: function( str ) { 
					var list = $("<div/>", { html: str });
					$("#tbody").html( list.find("#tbody").html() );
				}
			});
		});
		
		$("#search").catcomplete({ 
			delay: 0,
			minLength: 3,
			select: function( event, ui ) {
				$.ajax({
					url: "/adm/produtos/produtos-kits.php?codigo_id=<?php echo $GET['codigo_id']?>",
					type: "post",
					data: { acao: "add_prod_kit", codigo_id: ui.item.codigo_id },
					success: function( str ) { 
						var list = $("<div/>", { html: str });
						$("#tbody").html( list.find("#tbody").html() );
						$("input[name=q]").val("");						
					}
				});
			},
			source: function(request, response) {
				$.ajax({
					url: "/adm/produtos/produtos-kits.php?codigo_id=<?php echo $GET['codigo_id']?>",
					type: "post",
					data: { acao: "search_prod", q: request.term },
					success: function( str ) { 
						var list = $("<div/>", { html: str});
						var jsonObj = list.find("#search_prod").html();
						var obj = $.parseJSON(jsonObj);
						response(obj); 
					}
				});
			}
		});
	</script>
</div>
<?php
include '../rodape.php';