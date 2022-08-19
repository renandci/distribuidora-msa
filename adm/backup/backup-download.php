<?php
define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);

require_once PATH_ROOT . '/app/settings.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';

$arquivo = filter_input(INPUT_GET, 'arquivo');

if(!empty($arquivo) and is_file( $arquivo ) ) {
	$return = download( $arquivo );
	if ( empty( $return ) ) {
		unlink($arquivo);
	} else {
		echo $return;
	}
}