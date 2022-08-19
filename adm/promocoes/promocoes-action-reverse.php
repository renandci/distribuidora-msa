<?php
include '../topo.php';

if( empty($GET['id_black_friday_produtos']) || (int)$GET['id_black_friday_produtos'] == 0 ){
	header('location: /adm/promocoes/promocoes.php');
	return;
}

$Promocoes = Promocoes::find((int)$GET['id_black_friday_produtos']);
$Promocoes->delete();

header('location: /adm/promocoes/promocoes.php');
return;

// $query_produtos = '' 
				// . 'UPDATE produtos, ' 
				// . '       black_friday_produtos ' 
				// . 'SET produtos.preco_venda = black_friday_produtos.preco_venda, '
				// . '    produtos.preco_promo = black_friday_produtos.preco_promo '
				// . sprintf('WHERE (black_friday_produtos.id_produtos = produtos.id AND (produtos.loja_id=%u)) ', $CONFIG['loja_id']);
				
// $result_produtos = Produtos::connection()->query($query_produtos);
// if( $result_produtos->rowCount() ) {
	// $black_friday_produtos = BlackFridayProdutos::connection()->query(sprintf('DELETE FROM black_friday_produtos WHERE EXISTS(SELECT 1 FROM produtos WHERE (black_friday_produtos.id_produtos = produtos.id AND (black_friday_produtos.id_black_friday_config=%u)))', $GET['id_black_friday_produtos']));
	// if( $black_friday_produtos->rowCount() ) {
		// $black_friday_config = BlackFridayProdutos::connection()->query(sprintf('DELETE FROM black_friday_config WHERE black_friday_config.id=%u', $GET['id_black_friday_produtos']));
		// if( $black_friday_config->rowCount() ) {
			// header('location: /adm/promocoes/promocoes.php');
			// return;
		// }
	// }
// } else {
	// $black_friday_config = BlackFridayProdutos::connection()->query(sprintf('DELETE FROM black_friday_config WHERE black_friday_config.id=%u', $GET['id_black_friday_produtos']));
	// if( $black_friday_config->rowCount() ) {
		// header('location: /adm/promocoes/promocoes.php');
		// return;
	// }
// }

include '../rodape.php';