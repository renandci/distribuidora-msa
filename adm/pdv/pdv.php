<?php
require_once '../topo.php';

/**
 * Retorna opcoes do produto
 * Ex: nome de cores, nome dos tamanhos, etc...
 */
function opc_prod($s) {
	return !empty($s) ? implode(' - ', [null, $s]) : null;
}

// Remove produto do PDV
if( isset($GET['acao']) && $GET['acao'] == 'prod_remove' ) {

	$return = Carrinho::delete_log(['id' => $GET['prod_id']]);	
	print_r($return);
	
	if( !empty($return['id']) && $return['id'] > 0 ) {
		header('location: /adm/pdv/pdv.php');
		return;
	}
}

// produto com o PDV
if( isset($GET['acao']) && $GET['acao'] == 'prod_add' ) {
	
	if( !empty($GET['id']) && $GET['id'] > 0 )
		$Cart = Carrinho::find((INT)$GET['id']);
	else
		$Cart = new Carrinho();

	$Cart->id_session = sha1($_SESSION['admin']['id_usuario']);
	$Cart->id_produto = $GET['prod_id'];
	$Cart->quantidade = $GET['prod_qtde'];
	$Cart->prod_valor = (dinheiro($GET['prod_price']) > 0 ? dinheiro($GET['prod_price']) : $Prod->preco_promo);
	$return = $Cart->save_log();
	
	if( !empty($return['id']) && $return['id'] > 0 ) {
		header('location: /adm/pdv/pdv.php');
		return;
	}
}

// produto com o PDV
if( isset($GET['acao']) && $GET['acao'] == 'cli_add' ) {
	
	if( empty($GET['cli_id']) && $GET['cli_id'] == '0' ) {
		header('location: /adm/pdv/pdv.php');
		return;
	}
	$i = 0;
	$Cart = Carrinho::all(['conditions' => ['id_session=?', sha1($_SESSION['admin']['id_usuario'])]]);
	foreach($Cart as $r) 
	{
		$temp = Carrinho::find($r->id);
		$temp->id_cliente = $GET['cli_id'];
		$return = $temp->save_log();
		if( !empty($return['id']) && $return['id'] > 0 ) {
			$i++;
		}
		unset($return, $temp);
	}
	
	if( !empty($i) && $i > 0 ) {
		header('location: /adm/pdv/pdv.php');
		return;
	}
}

if( isset($POST['cli']['id_cliente'], $POST['end']['id_endereco']) ) {
	$i = 0;
	
	$POST['cli']['id'] = $POST['cli']['id_cliente'];
	$Cliente = new Clientes();	
	$cli = $Cliente->save_log($POST['cli']);
	if( !empty($cli['id']) && $cli['id'] > 0 ) {
		$i++;
	}
	
	$POST['end']['id'] = $POST['cli']['id_endereco'];
	$Endereco = new ClientesEndereco();	
	$end = $Endereco->save_log($POST['end']);
	if( !empty($end['id']) && $end['id'] > 0 ) {
		$i++;
	}
	
	if( !empty($i) && $i > 0 ) {
		header('location: /adm/pdv/pdv.php');
		return;
	}
}
?>

<style>
	body{ background-color: #f1f1f1 }
</style>

<div class="container" id="div-edicao">
	<div class="row">
		<?php require_once 'pdv-produtos.php'; ?>
		<?php require_once 'pdv-clientes.php'; ?>
	</div>
</div>


<?php
require_once '../rodape.php';