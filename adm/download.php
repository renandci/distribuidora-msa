<?php	
include '../app/includes/bibli-funcoes.php';
$arquivo = filter_input(INPUT_GET, 'arquivo');
$excluir = filter_input(INPUT_GET, 'excluir');
if($arquivo != '' and is_file($arquivo)) {
	download($arquivo);		
	if ($excluir){
		unlink($arquivo);
	}
}
