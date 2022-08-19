<?php

use NFePHP\NFe\Common\Standardize;

include_once dirname(__DIR__) . '/topo.php';

$porcnota = filter_input(INPUT_POST, 'porcnota', FILTER_SANITIZE_NUMBER_INT) ?  filter_input(INPUT_POST, 'porcnota', FILTER_SANITIZE_NUMBER_INT) : 100;

$porcnota_get = filter_input(INPUT_GET, 'porcnota', FILTER_SANITIZE_NUMBER_INT) ?  filter_input(INPUT_GET, 'porcnota', FILTER_SANITIZE_NUMBER_INT) : 100;

$nrnfe = filter_input(INPUT_GET, 'nrnfe', FILTER_SANITIZE_NUMBER_INT);

$id_emitente = filter_input(INPUT_GET, 'id_emitente', FILTER_SANITIZE_NUMBER_INT);

$id_pedido = filter_input(INPUT_GET, 'id_pedido', FILTER_SANITIZE_NUMBER_INT);

$codigo_id 	= filter_input(INPUT_POST, 'codigo_id', FILTER_SANITIZE_NUMBER_INT);
$csosn 		= filter_input(INPUT_POST, 'csosn', FILTER_SANITIZE_NUMBER_INT);
$unid 		= filter_input(INPUT_POST, 'unid', FILTER_SANITIZE_STRING);
$cfop 		= filter_input(INPUT_POST, 'cfop', FILTER_SANITIZE_NUMBER_INT);
$ncm 		= filter_input(INPUT_POST, 'ncm', FILTER_SANITIZE_NUMBER_INT);
$cest 		= filter_input(INPUT_POST, 'cest', FILTER_SANITIZE_NUMBER_INT);


// include percent da nota
if( isset( $_GET['acao'] ) && 'gerar_percent_nfe' == $_GET['acao'] ) {
	$result = Pedidos::new_save(['porc_nota' => $porcnota_get, 'id' => $id_pedido]);
	if( $result['id'] > 0 ) {
		header('location: /adm/nfe/nfe.php?id_pedido=' . $id_pedido . '&id_emitente=' . $id_emitente);
		return;
	}
}

// Alterar o numero da ultima nota
if( isset( $_GET['acao'] ) && 'alterar_num_nfe' == $_GET['acao'] ) {
	$result = NfeEmitentes::new_save(['nrnfe' => $nrnfe, 'id' => $id_emitente]);
	$result1 = Pedidos::new_save(['nrnfe' => $nrnfe, 'id' => $id_pedido]);
	if( $result['id'] > 0 ) {
		header('location: /adm/nfe/nfe.php?id_pedido=' . $id_pedido . '&id_emitente=' . $id_emitente . '&error=Dados alterados com sucesso!');
		return;
	}
}

// Alterar o numero do ncm do produto
if(isset($POST['acao']) && 'alterar_num_ncm' == $POST['acao']) {
	$count = 0;
	$ProdutosAll = Produtos::all(['conditions' => ['codigo_id=?', $codigo_id]]);
	foreach($ProdutosAll as $rws) 
	{
		$result = Produtos::new_save([
			'id' => $rws->id, 
			'csosn' => $csosn,
			'unid' => $unid,
			'cfop' => $cfop,
			'ncm' => $ncm,
			'cest' => $cest,
		]);
		if($result['id'] > 0) $count++;
	}
	
	if( $count > 0 ) {
		header('location: /adm/nfe/nfe.php?id_pedido=' . $id_pedido . '&id_emitente=' . $id_emitente . '&error=Dados alterados com sucesso!');
		return;
	}
}

$produtos = null;
$Pedidos = Pedidos::find($id_pedido);
foreach ( $Pedidos->pedidos_vendas as $a => $rs ) 
{
	$produtos[$a]['id_produto'] = $rs->produto->id;
	$produtos[$a]['codigo_id'] = $rs->produto->codigo_id;
	$produtos[$a]['prod_cod'] = $rs->produto->codigo_produto;
	$produtos[$a]['nome_produto'] = $rs->produto->nome_produto;
	$produtos[$a]['csosn'] = $rs->produto->csosn;
	$produtos[$a]['unid'] = $rs->produto->unid;
	$produtos[$a]['cest'] = $rs->produto->cest;
	$produtos[$a]['cfop'] = $rs->produto->cfop;
	$produtos[$a]['ncm'] = $rs->produto->ncm;
	$produtos[$a]['cst'] = $rs->produto->cst;
	$produtos[$a]['cor'] = $rs->produto->cor->nomecor;
	$produtos[$a]['tam'] = $rs->produto->tamanho->nometamanho;
	$produtos[$a]['valor_pago'] = $rs->valor_pago;
	$produtos[$a]['quantidade'] = $rs->quantidade;
	
	if( ! empty($rs->produto->grid_kits) )
	{
		unset($produtos[$a]);
		foreach ($rs->produto->grid_kits as $b => $pr ) 
		{
			$b++;
			$produtos[$b]['id_produto'] = $pr->produto->id;
			$produtos[$b]['codigo_id'] = $pr->produto->codigo_id;
			$produtos[$b]['prod_cod'] = $pr->produto->codigo_produto;
			$produtos[$b]['nome_produto'] = $pr->produto->nome_produto;
			$produtos[$b]['csosn'] = $pr->produto->csosn;
			$produtos[$b]['unid'] = $pr->produto->unid;
			$produtos[$b]['cest'] = $pr->produto->cest;
			$produtos[$b]['cfop'] = $pr->produto->cfop;
			$produtos[$b]['ncm'] = $pr->produto->ncm;
			$produtos[$b]['cst'] = $pr->produto->cst;
			$produtos[$b]['cor'] = $pr->produto->cor->nomecor;
			$produtos[$b]['tam'] = $pr->produto->tamanho->nometamanho;
			$produtos[$b]['valor_pago'] = $pr->produto->preco_promo;
			$produtos[$b]['quantidade'] = $rs->quantidade;
		}
	}
}

// Vefirificar se os enderecos de entrega estão com o codigo do IBGE
if( PedidosEnderecos::connection()->query(sprintf('select 1 from pedidos_enderecos where id_pedido=%u and id_cidade=0', $id_pedido))->rowCount() > 0 ) {
	$sql = 'UPDATE pedidos_enderecos AS PEDEND ' 
		 . 'INNER JOIN nfe_cidades AS NFECID ON (NFECID.nome = PEDEND.cidade) ' 
		 . 'SET PEDEND.id_cidade = NFECID.id ' 
		 . 'WHERE PEDEND.id_pedido=%u AND NFECID.uf = PEDEND.uf ';

	$result = PedidosEnderecos::connection()->query(sprintf($sql, $id_pedido));
	// $result->execute([ $id_pedido ]);
	if( $result ) {
		header('Location: /adm/nfe/nfe.php?id_pedido=' . $id_pedido . '&id_emitente=' . $id_emitente . '&error=Atualizamos o código do IBGE da cidade para o endereço do cliente!');
		return;
	}
}

$NfeEmitentes = NfeEmitentes::all(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);

$NfeNotas = NfeNotas::all(['conditions' => ['id_pedido=? and loja_id=? and status = 1', $id_pedido, $CONFIG['loja_id']], 'order' => 'id desc']); 

$errors = 0;
?>
<div id="conteudos-recarregar-filho">
    <form id="emitir_nfe_xml" class="clearfix" action="/adm/nfe/nfe-xml.php" method="post">
		<input type="hidden" name="id_pedido" value="<?php echo $id_pedido?>"/>
        <div class="row mb15">
			
			<?php if( isset( $GET['error'] ) && $GET['error'] != null ) { ?>
				<div class="col-md-12"><div class="alert alert-info ft12px"><?php echo $GET['error'];?></div></div>
			<?php } ?>

			<?php if(count($NfeNotas) > 0) foreach( $NfeNotas as $error ) { ?>
				<?php if( ! empty( $error->motivo ) && $error->status != 2 ) { $errors++; ?>
					<div class="col-md-12"><div class="alert alert-danger ft12px bold"><?php echo $error->motivo;?></div></div>
				<?php } ?>
			<?php } ?>

			<div class="col-md-6">
				<fieldset>
					<legend class="bold">Dados do Emitente</legend>
					<div class="clearfix">
						<label class="show clearfix">
							Emitentes:
							<span id="nrnfe_text"></span>
						</label>
						<select name="id_emitente" class="w100">
							<option value="0">Selecione o Emitente</option>
							<?php 
							$dir = sprintf('%sassets/%s/xml/', PATH_ROOT, ASSETS);
							$filename = '%s%s.xml';
							foreach( $NfeEmitentes as $r )  {

								$nfe_last = $r->nrnfe;

								// $Xml = sprintf($filename, $dir, $r->nfe_nr_last->chavenfe);
								// if( file_exists( $Xml ) ) {
								// 	$StandardizeXml = file_get_contents($Xml);
								// 	$Standardize = (new Standardize($StandardizeXml))->toStd();
								// 	$nfe_last = $Standardize->infNFe->ide->nNF;
								// }
							?>
							<option value="<?php echo (!empty($r->id) && $r->id > 0 ? $r->id:'');?>" data-nfe="<?php echo $r->nrnfe?>" data-value="Última NF-e gerada: <?php echo $nfe_last - 1?>" <?php echo ((count($NfeEmitentes) == 1 || ($id_emitente == $r->id)) ? 'selected':'')?> data-info="<?php echo $r->tpamb?>">
								<?php echo $r->razaosocial?>
							</option>
							<?php } ?>
						</select>
					</div>
				</fieldset>
			</div>
			<div class="col-md-6">
				<fieldset>
					<legend class="bold">Nr da NF-e</legend>
					<div class="row">
						<div class="col-md-8 col-xs-12">
							<label>NF-e: <small>Emitir nota</small></label>
							<select name="id_nota" class="w100">
								<option value="0">Gerar nova - NF-e</option>
							<?php if(count($NfeNotas) > 0) foreach( $NfeNotas as $rNfe ) { ?>
								<option value="<?php echo $rNfe->id;?>" style="<?php echo $rNfe->status == 2 ? 'background-color:#ffecef' : ''; echo $rNfe->status == 1 ? 'background-color:#c4efae' : ''?>"  <?php echo ($rNfe->status == 1 ? 'selected':'')?>>
									<?php echo substr($rNfe->chavenfe, -18, 8)?> 
									<?php echo $rNfe->status == 2 ? ' - Nota Cancelada' : ''?> 
									<?php echo !empty($rNfe->nrreccan) ? sprintf(' recibo %s', $rNfe->nrreccan) : ''?>
								</option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-4 col-xs-12">
							<label for="nrnfe">Número:</label>
							<input type="number" id="nrnfe" name="nrnfe" class="text-right w100" value="<?php echo $Pedidos->nrnfe > 0 ? $Pedidos->nrnfe : 0; ?>" />
						</div>
					</div>
				</fieldset>
			</div>
			
			<div class="col-md-6">
				<fieldset>
					<legend class="bold">Natureza/Tipo</legend>
					<div class="row">
						<div class="col-md-12">
							<label>Natureza:</label>
							<select name="natOp" class="w100">
								<option value="">Selecione...</option>
								<option value="Venda de Mercadoria" selected>Venda de Mercadoria</option>
								<option value="Devolucao de Mercadoria">Devolução de Mercadoria</option>
							</select>
						</div>
						<!-- <div class="col-md-6">
							<label>Tipo:</label>
							<select name="tpNF" class="w100">
								<option value="">Selecione...</option>
								<option value="1" selected>Saída</option>
								<option value="4">Devolução</option>
								<option value="0">Entrada</option>
							</select>
						</div> -->
					</div>
				</fieldset>
			</div>
			<div class="col-md-3">
				<fieldset>
					<legend class="bold">Vl. Nota</legend>
					<div class="clearfix">
						<label>Porcentagem:</label>
						<input type="number" id="porcentagem" name="porc_nota" class="text-right w100" value="<?php echo $Pedidos->porc_nota > 0 ? $Pedidos->porc_nota : 100; ?>"/>
					</div>
				</fieldset>
			</div>
			<div class="col-md-3">
				<fieldset>
					<legend class="bold">Frete</legend>
					<div class="clearfix">
						<label>Modalidade:</label>
						<select name="modFrete" class="w100">
							<option value="">Selecione...</option>
							<option value="1">Incluir Frete</option>
							<option value="9" selected>Sem Frete</option>
						</select>
					</div>
				</fieldset>
			</div>
			<div class="col-md-12 mt15">
				<fieldset id="reload_dados_nfe">
					<legend class="bold">Dados dos Produtos</legend>
					<table width="100%" cellpadding="5" cellspacing="1" align="center" class="mt5 table table-bordered">
						<thead>
							<tr>
								<th class="text-center checkboxs hidden">#</th>
								<th>Descrição</th>
								<th class="text-center" align="center" nowrap="nowrap" width="1%">Qtd</th>
								<th class="text-center">Unitário</th>
								<th class="text-center">Total</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ( $produtos as $prod ) { ?>
							<tr<?php echo (empty($prod['csosn']) || empty($prod['unid']) || empty($prod['ncm']) || empty($prod['cfop'])) ? ' bgcolor="ffe6e6" data-invalid="true"':''?> style="position: relative">
								<td nowrap="nowrap" width="1%" class="checkboxs hidden">
									<input type="checkbox" name="id_produto[]" value="<?php echo $prod['id_produto']?>" id="produto<?php echo $prod['id_produto']?>"/>
									<label for="produto<?php echo $prod['id_produto']?>" class="input-checkbox"></label>
								</td>
								<td>
									<span><?php echo $prod['nome_produto']?></span>
									<?php if(empty( $prod['csosn']) || empty($prod['unid']) || empty($prod['ncm']) || empty($prod['cfop'])) { ?>
										<i class="fa fa-warning text-danger" style="cursor: pointer;" onclick="$(this).next().next().toggleClass('hidden', 'show')"></i><br/>
										<span class="btn btn-info btn-xs ft11px btn-edit-ncm" data-id="<?php echo $prod['codigo_id']?>">editar campos fiscais</span>
									<?php } ?>
								</td>
								<td align="center">
									<?php echo $prod['quantidade']?>
								</td>
								<td align="center" nowrap="nowrap" width="1%" bgcolor="daffce" id="price_total_desc_<?php echo $prod['id_produto']?>">
									<?php echo number_format(($prod['valor_pago'] * ($Pedidos->porc_nota / 100) * $prod['quantidade']), 2, ',', '.')?>
								</td>
								<td align="center" nowrap="nowrap" width="1%" id="price_total_<?php echo $prod['id_produto']?>">
									<?php echo number_format($prod['valor_pago'] * $prod['quantidade'], 2, ',', '.')?>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</fieldset>
				
				<fieldset id="reload_dados_nfe_buttons" class="mt15">
					<legend class="bold text-left">Ações</legend>
					<?php if( ! empty( current($NfeNotas)->id ) && $errors == 0 ) { ?>
						<?php if( ! empty( current($NfeNotas)->nrprot ) ) { ?>
							<a href="/adm/nfe/nfe-imprimir.php?id_nota=<?php echo $NfeNotas[0]->id?>" target="_blank" class="btn btn-primary" <?php echo _P('nfe-imprimir', $_SESSION['admin']['id_usuario'], 'acessar')?>>
								IMPRIMIR NFe
							</a>
							<button type="button" class="btn btn-danger-default" id="button_cancelar_nfe_xml">
								cancelar nfe
							</button>
						<?php } else { ?>
							<a href="/adm/nfe/nfe-emitir.php" target="_blank" class="btn btn-warning" <?php echo _P('nfe-emitir', $_SESSION['admin']['id_usuario'], 'acessar')?>>
								enviar nota para o sefaz
							</a>
						<?php } ?>
					<?php } else if( $errors > 0 ) { ?>
						<button type="submit" class="btn btn-warning">
							corrigir xml
						</button>
					<?php } else { ?>
						<button type="submit" class="btn btn-success">
							gerar xml
						</button>
					<?php } ?>
				</fieldset>
			</div>
        </div>
    </form>
    
    <style>
		#janela-nfe {
			background-color: #f1f1f1;
		}
        fieldset {
			background-color: #fff;
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px;
            border-color: #cdcdcd;
            border-style: solid;
            border-width: 1px;
        }
		.ui-dialog { 
			z-index: 109;
		}
		/* .dialog-error { 
			z-index: 110;
		} 
		.dialog-error,  
		.dialog-error .ui-dialog-content { 
			background-color: red;
			color: #fff
		} 
		.dialog-error .ui-dialog-titlebar{ 
			border: none; 
		 	background-color: #b11e1e; 
		} */
    </style>
    
    <script>
		$(function(){
		<?php ob_start(); ?>
		// $("<div/>", {
		// 	id: "alert_inutil"
		// }).dialog({
		// 	width: 395, 
		// 	height: 175,
		// 	autoOpen: "<?php echo !empty($GET['error'])? true : false?>",
		// 	modal: "true",
		// 	title: "Atenção!",
		// 	// dialogClass: "dialog-error"
		// });
		<?php
		require dirname(__DIR__) . '/nfe/js/nfe-js.js';
		$jz = new Patchwork\JSqueeze();
		$temp = $jz->squeeze(ob_get_clean(), true, false, false);
		echo $temp;
		?>
		});
    </script>
</div>

<?php
include_once dirname(__DIR__) . '/rodape.php';
