<?php
// ob_start();
/**
 * Função ira criar as imagens do sistema se necessario <br />
 *
 * @param string $image caminho absoluto da imagem
 * @param type $size tamanho da imagem a ser gerada
 * @param type $qualidade qualidade definida como 90
 * @exemple https://imgx.lojasecommerce.test/(banners|store|status|public|imgs)-200x200-dominio-bolsas.(jpg|gif|jpeg|png)
 */

define('PATH_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// require PATH_ROOT . 'app/vendor/autoload.php';
require PATH_ROOT . 'public/WideImage/vendor/autoload.php';

try {
	// array ( [local] => store [size] => 200x200 [uri] => bmcasadecor [name] => bolsas [ex] => jpg )

	$data = filter_var_array($_GET);

	$local = !empty($data['local']) ? $data['local'] : null;
	$size = !empty($data['size']) ? $data['size'] : null;
	$uri = !empty($data['uri']) ? $data['uri'] : null;
	$name = !empty($data['name']) ? $data['name'] : null;
	$ex = !empty($data['ex']) ? $data['ex'] : null;

	$filter = !empty($data['filter']) && array_key_exists('filter', $data) ? $data['filter'] : null;
	$arg = !empty($data['arg']) && array_key_exists('arg', $data) ? $data['arg'] : '255,255,255';
	list($ar1, $ar2, $ar3) = explode(',', $arg);

	$file = null;

	switch ( $local ) {
		// Caminho para o diretorio em padrão da store
		case 'imgs':
			$file = sprintf('%s/assets/%s/imgs/%s.%s', PATH_ROOT, $uri, $name, $ex);
		break;
		// Para o blog da store se necessario
		case 'blog':
			$file = sprintf('%s/assets/%s/imgs/blog/%s.%s', PATH_ROOT, $uri, $name, $ex);
		break;
		// Para os produtos
		case 'store':
			$file = sprintf('%s/assets/%s/imgs/produtos/%s.%s', PATH_ROOT, $uri, $name, $ex);
		break;
		// Para os banners
		case 'banners':
			$file = sprintf('%s/assets/%s/imgs/banners/%s.%s', PATH_ROOT, $uri, $name, $ex);
		break;
		// Para os banners
		case 'estufas':
			$file = sprintf('%s/assets/%s/imgs/estufas/%s.%s', PATH_ROOT, $uri, $name, $ex);
		break;

		// Tenta fazer todas as imagens a partir dessa estrutura
		// Todas as imagens deve esta dentro de "public/imgs/"
		default:
		$file = sprintf('%s/public/imgs/%s.%s', PATH_ROOT, $name, $ex);
			if( strstr($name, 'status-') || strstr($name, 'off-')  ) {
				$file = sprintf('%s/public/imgs/icons-status/%s.%s', PATH_ROOT, $name, $ex);
			}
			if( strstr($name, 'usuarios-') ) {
				$file = sprintf('%s/public/imgs/usuarios/%s.%s', PATH_ROOT, substr_replace($name, '/', 0, 9), $ex);
			}
			if( strstr($name, 'imagens-bancos') ) {
				$file = sprintf('%s/public/imgs/imagens-bancos/%s.%s', PATH_ROOT, substr_replace($name, '/', 0, 15), $ex);
			}
		break;
	}

	if( ! file_exists( $file ) ) {
		throw new Exception('Nada Encontrado, Suma daqui... \0/');
	}

	$WideImage = WideImage\WideImage::load($file);

	// Adiciona um filter na imagem
	if($filter == 1 || $filter == 'true')
		$WideImage = $WideImage->applyFilter(IMG_FILTER_COLORIZE, $ar1, $ar2, $ar3);

	$size = isset($size) ? $size : sprintf('%sx%s', $WideImage->getWidth(), $WideImage->getHeight());

	// definir o tamanho da imagem a ser criada na view
	if ( ! empty( $size ) ) {
		list($w, $h) = explode('x', $size);
		$WideImage = $WideImage->resize($w, $h, 'inside');
	}

	// $cache_for = 60 * 60 * 24 * 30;
	// header("Content-type: image/$ext");
	// header("Content-Transfer-Encoding: binary");
	// header("Content-Disposition: filename=$file;");
	// header("Cache-Control: public, max-age=$cache_for");

	// png|gif
	// if(($w <= 240 || $w <= 480 || $w <= 960 || $w == 1600) && in_array($ex, ['png', 'gif', 'PNG', 'GIF']) )
	if(in_array($ex, ['gif', 'GIF']) )
		$WideImage->output($ex);

	if(in_array($ex, ['png', 'PNG']) )
		$WideImage->output($ex, 8);

	// jpg|jpeg
	// if(($w <= 240 || $w <= 480 || $w <= 960 || $w == 1600) && in_array($ex, ['jpg', 'jpeg', 'JPG', 'JEPG']) )
	if(in_array($ex, ['jpg', 'jpeg', 'JPG', 'JPEG']) )
		$WideImage->output($ex, 90);

	$WideImage->destroy();

}
catch (Exception $e) {
	echo $e->getMessage();
}
