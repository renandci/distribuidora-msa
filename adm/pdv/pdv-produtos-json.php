<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT . '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';

/**
 * Retorna opcoes do produto
 * Ex: nome de cores, nome dos tamanhos, etc...
 */
function opc_prod($s) {
	return !empty($s) ? implode(' - ', [null, $s]) : null;
}

if( ! isset($GET['q']) && $GET['q'] == '' ) 
	return;

$max = 25;
$pag = isset( $GET['page'] ) && $GET['page'] != '' ? $GET['page'] : 1;

$conditions['order'] = 'produtos.nome_produto asc';
$conditions['conditions'] = sprintf('produtos.excluir=0 and produtos.status=2 and produtos.loja_id=%u ', $CONFIG['loja_id']);
$conditions['conditions'] .= queryInjection('' 
	. 'and (produtos.nome_produto like "%%%s%%" or (produtos.codigo_referencia like "%%%s%%" or produtos.codigo_produto like "%%%s%%")) ', $GET['q'], $GET['q'], $GET['q']);

$count = Produtos::count($conditions);

$conditions['limit'] = $max;
$conditions['offset'] = (($pag - 1) * $max);

$result = Produtos::all($conditions);

foreach($result as $rs) {
	$json['results'][] = [
		'id' => $rs->id,
		'text' => $rs->new_cod . ' - ' . $rs->nome_produto . opc_prod($rs->cor->nomecor) . opc_prod($rs->tamanho->nometamanho),
	];
}

header('Content-Type: text/json; charset=utf-8');

// to json
$json['pagination'] = ['more' => ($count > ($conditions['offset'] + $max))];
if( !($count > ($conditions['offset'] + $max)) ) {
	$json['results'][] = [ 'id' => '0', 'text' => 'Nenhum registro encontrado...'];
	echo json_encode($json);
	return;
}

echo json_encode($json);