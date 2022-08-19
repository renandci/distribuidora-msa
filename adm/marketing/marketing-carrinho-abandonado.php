<?php
include '../topo.php';
$store['button_whatsapp'] = function($tel = '', $text = '') {
    $tel = soNumero($tel);
    if(strlen($tel) >= 11)
    return sprintf('<a href="https://wa.me/55%s?text=%s" target="_blank" class="ml5 mr5 fa fa-whatsapp"></a>', $tel, $text);
};
?>


<style>
	body{ background-color: #f1f1f1 }
</style>

<!--[CART VIEW]-->
<?php if( ! empty( $_GET['acao'] ) && $_GET['acao'] == 'cart_view' ) { ?>
	<div id="id_cart_view">
		<table class="table table-borded">
			<tr>
				<th>#</th>
				<th>Produto</th>
				<th>QTDE</th>
				<th cals>Valor</th>
				<th>Add</th>
			</tr>
			<?php
			$vl_qtde = 0;
			$vl_frete = 0;
			$vl_price = 0;
			$cart_session_id = ! empty( $_GET['cart_session_id'] ) && $_GET['cart_session_id'] != '' ? (STRING)$_GET['cart_session_id'] : null;
			$conditions['conditions'] = [ 'id_session=?', $cart_session_id ];			
			$Carrinho = Carrinho::all($conditions);
			foreach( $Carrinho as $rws ) { ?>
			<tr>
				<td width="105px" nowrap="nowrap"><img src="<?php echo Imgs::src($rws->produto->imagem, 'smalls');?>" width="105"></td>
				<td style="vertical-align: middle">
					<h4 class="mb5 mt0"><?php echo $rws->produto->nome_produto?></h4>
					<small><?php echo ($rws->produto->nomecor ? implode("<br/>", [$rws->produto->nomecor, '']) : null)?></small>
					<small><?php echo ($rws->produto->nometamanho ? implode("<br/>", [$rws->produto->nometamanho, '']) : null)?></small>
				</td>
				<td nowrap="nowrap" width="1%" align="center" style="vertical-align: middle">
					<?php echo $rws->quantidade?>
				</td>
				<td nowrap="nowrap" width="1%" align="center" style="vertical-align: middle">
					R$ <?php echo number_format($rws->produto->preco_promo, 2, ',', '.')?>
				</td>
				<td nowrap="nowrap" width="1%" style="vertical-align: middle">
					<?php echo $rws->created_at->format('H:i')?> hs
				</td>
			</tr>
			<?php
			$vl_qtde += $rws->quantidade;
			$vl_frete += $rws->frete_valor;
			$vl_price += ($rws->produto->preco_promo * $rws->quantidade);
			?>
			<?php } ?>
			<tr class="ft14px">
				<td colspan="4" style="vertical-align: middle" align="right">
					Itens
				</td>
				<td nowrap="nowrap" width="1%" align="right" style="vertical-align: middle">
					<?php echo $vl_qtde?>
				</td>
			</tr>
			<tr class="ft14px">
				<td colspan="4" style="vertical-align: middle" align="right">
					Frete
				</td>
				<td nowrap="nowrap" width="1%" align="right" style="vertical-align: middle">
					<?php echo number_format($vl_frete, 2, ',', '.')?>
				</td>
			</tr>
			<tr class="ft14px">
				<td colspan="4" style="vertical-align: middle" align="right">
					Produtos
				</td>
				<td nowrap="nowrap" width="1%" align="right" style="vertical-align: middle">
					<?php echo number_format($vl_price, 2, ',', '.')?>
				</td>
			</tr>
			<tr class="ft20px">
				<td colspan="4" style="vertical-align: middle" align="right" bgcolor="#f1f1f1">
					Total
				</td>
				<td nowrap="nowrap" width="1%" align="right" style="vertical-align: middle" bgcolor="#f1f1f1">
					R$ <?php echo number_format(($vl_price + $vl_frete), 2, ',', '.')?>
				</td>
			</tr>
		</table>
	</div>
<?php } ?>
<!--[END CART VIEW]-->

<div class="panel panel-default">
	<div class="panel-body">
		<h3 class="clearfix mt0">Carrinho Abandonado</h3>
		<div class="table-responsive">
			<table class="table table-borded table-hover" id="table_newsletter">
				<tr>
					<th>Nome</th>
					<th class="text-center">Telefone</th>
					<th class="text-center">Celular</th>
					<th class="text-center">E-mail</th>
					<th class="text-center">Criado</th>
					<th class="text-center">Ações</th>
				</tr>
				<?php
				$max = 25;
				
				$pag = isset( $GET['pag'] ) && $GET['pag'] > 0 ? (INT)$GET['pag'] : 1;
				
				$ini = (($pag * $max) - $max);
				
				$conditions['conditions'] = [ 'loja_id=? and (cliente_tmp != "" or id_cliente > 0) ', $CONFIG['loja_id'] ];
				$conditions['group'] = 'id_session';
				
				$num = count(Carrinho::all($conditions));
				
				$conditions['limit'] = $max;
				$conditions['offset'] = ($max * ($pag - 1));
				$conditions['order'] = 'id desc';
				
				$Carrinho = Carrinho::all($conditions);

				$total = ceil($num / $max);
				
				foreach( $Carrinho as $rws ) { ?>
				<tr class="<?php echo ($rws->id == $GET['id_mail'] ? ' success':'')?> in-hover">
					<?php 
					$Cliente = Clientes::first(['conditions' => ['email=?', $rws->cliente_tmp]]);
					if($rws->id_cliente > 0)
						$Cliente = Clientes::find($rws->id_cliente);
					?>
					<td><?php echo !empty($Cliente->id) ? $Cliente->nome : '<span class="text-danger">Cliente não cadastrado!</span>'?></td>
					<td nowrap="nowrap" width="1%"><?php echo $Cliente->telefone?></td>
					<td nowrap="nowrap" width="1%" align="center"><a href="https://wa.me/55<?=sonumero($Cliente->celular)?>?text=Olá <?=$Cliente->nome?>" target="_blank" class="ml5 mr5 fa fa-whatsapp" style="color: #fff;background-color: #0dc142; padding: 5px;border-radius: 50px;font-size: 12pt;"></a> <?php echo $Cliente->celular?></td>
					<td nowrap="nowrap" width="1%" align="center"><?php echo (!empty($rws->cliente_tmp)?$rws->cliente_tmp:(!empty($Cliente->id)?$Cliente->email:''))?></td>
					<td nowrap="nowrap" width="1%" align="center"><?php echo $rws->created_at->format('d/m/Y H:i')?></td>
					<td nowrap="nowrap" width="1%" align="center">
						<a href="/adm/marketing/marketing-carrinho-abandonado.php?acao=cart_view&cart_session_id=<?php echo $rws->id_session?>" class="btn btn-warning btn-xs" data-btn="cart_view">
							<i class="fa fa-edit"></i> 
							ver carrinho
						</a> 
						<!--
						<a href="/adm/marketing/marketing-carrinho-abandonado.php?id_mail=<?php echo $rws->id?>" class="btn btn-info btn-xs">
							<i class="fa fa-send"></i> 
							gerar envios
						</a>
						<a href="/adm/marketing/marketing-carrinho-abandonado.php?acao=remover_emails&id_mail=<?php echo $rws->id?>" class="btn btn-danger btn-xs" onclick="return confirm('Deseja realmente excluir\nTodos os e-mail de envio serão cancelados.');">
							<i class="fa fa-trash"></i> 
							excluir
						</a>
						-->
					</td>
				</tr>
				<?php } ?>
			</table>
		</div>
		<div class="pagination">
			<?php
			for( $i = $pag - 5, $limiteDeLinks = $i + 10; $i <= $limiteDeLinks; ++$i ) {
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
				
				// trocas os dados
				$link_attr2 = str_replace("*{$pag}*", $i, $link_attr);
				
				if( ($i == $pag) || ($pag == '0') ) {
					echo '<li class="active"><span>' . $i . '</span></li>';
				} 
				else {
					echo '<li><a href="/adm/marketing/marketing-carrinho-abandonado.php?pag=' . $i . '&type_status=' . $GET['type_status'] .  '">' . $i . '</a></li>';
				}
			}
			?>
		</div>
	</div>
</div>
<?php ob_start(); ?>
<script>

	JanelaModal.dialog({
		autoOpen: false,
		width: 960,
		height: 600,
		modal: true,
		dialogClass: "classe-ui",
		title: "Carrinho Itens"		
	});

	$( "body" ).on( "click", "[data-btn=cart_view]", function( e ) {
		e.preventDefault();
		var link = (this.href||e.target.href);
		$.get( link, function( str ) {
			var list = $("<div/>", { html: str });
			JanelaModal.dialog("open").html( list.find("#id_cart_view").html() );
		});
	} );
</script>
<?php $SCRIPT['script_manual'] .= ob_get_clean(); ?>
<?php include '../rodape.php';