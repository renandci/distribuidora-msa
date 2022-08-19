<?php

/**
 * Função ira criar as imagens do sistema se necessario <br />
 *
 * @param string $image caminho absoluto da imagem
 * @param type $size tamanho da imagem a ser gerada
 * @param type $qualidade qualidade definida como 90
 * @exemple https://imgx.lojasecommerce.test/(banners|store|status|public|imgs)-200x200-dominio-bolsas.(jpg|gif|jpeg|png)
 */

define('PATH_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

include PATH_ROOT . '/app/vendor/autoload.php';

// print_r($_GET);
// die;;
try {
	// array ( [local] => store [size] => 200x200 [uri] => bmcasadecor [name] => bolsas [ex] => jpg )
	extract(filter_var_array($_GET));


	$file = '';
	switch ( $local ) {
		case 'store':
			$file = sprintf('%s/assets/%s/imgs/produtos/%s.%s', PATH_ROOT, $uri, $name, $ex);
		break;
	}

	if( ! file_exists( $file ) ) {
		throw new Exception('Nada Encontrado');
	}
	$WideImage = WideImage\WideImage::load( $file );

	// definir o tamanho da imagem a ser criada na view
	if ( !empty( $size ) ) {
		list($w, $h) = explode('x', $size);
		$WideImage = $WideImage->resize( $w, $h, 'inside');
	}

	if( $w <= 200 && in_array($ex, ['png', 'gif', 'PNG', 'GIF']) )
		$WideImage->output($ex, 8);

	if( $w <= 400 && in_array($ex, ['png', 'gif', 'PNG', 'GIF']) )
		$WideImage->output($ex, 9);

	if( $w >= 401 && in_array($ex, ['png', 'gif', 'PNG', 'GIF']) )
		$WideImage->output($ex, 10);

	if( $w <= 200 && in_array($ex, ['jpg', 'jpeg', 'JPG', 'JEPG']) )
		$WideImage->output($ex, 80);

	if( $w <= 700 && in_array($ex, ['jpg', 'jpeg', 'JPG', 'JEPG']) )
		$WideImage->output($ex, 90);

	if( $w >= 701 && in_array($ex, ['jpg', 'jpeg', 'JPG', 'JEPG']) )
		$WideImage->output($ex, 99);

	$WideImage->destroy();

}
catch (Exception $e) {
	echo $e->getMessage();
}
