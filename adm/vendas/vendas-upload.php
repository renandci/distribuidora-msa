<?php
include '../topo.php';

$id = filter_input(INPUT_GET, 'id');

$id_venda = filter_input(INPUT_GET, 'id_venda');

// recarrega a venda
$PV = PedidosVendas::find( $id_venda );

// carregar somente os dados personalizado
$personalizado = html_entity_decode( $PV->personalizado );

// decodifica a string
$personalizado_decode = json_decode( $personalizado, true );

// pega somente a imagem
$personalizado_imagem = $personalizado_decode[0]['Imagem'];

// caminho da imagem
$caminho = '../assets/' . ASSETS . '/temp/';

// upload das novas imagens personalizadas
$files = current( $_FILES );

// apenas os arquivos devem ser upados
$validos = ['.gif', '.jpeg', '.jpg', '.png'];

// define as variaveis
$pathinfo = pathinfo( $personalizado_imagem );
list( $dirname, $basename, $extension, $filename ) = $pathinfo;

unset( $personalizado_decode[0]['Personalizado'] );

for( $i = 0; $i < count( $files['tmp_name'] ); $i++ ) 
{
	// verifica se um diretório existe
	$new_dir = $caminho . $id_venda;
	if( ! is_dir( $new_dir ) ) {
		// tenta criar um diretório para as imagens novas
		if( ! mkdir( $new_dir ) ) {
			throw new Exception("Não foi possivel criar o diretorio {$new_dir}");
		}
	}
	
	// captura a extensaão do arquivo
	$ext = strtolower( substr( $files['name'][$i], -4 ) );
	
	// verifica se é válida
	if( in_array( $ext, $validos ) ) {
		$new_name = $pathinfo['filename'] . '-' . $i . $ext;
		$WideImage = WideImage\WideImage::load( $files['tmp_name'][$i] );
		$WideImage->saveToFile( "{$new_dir}/{$new_name}" );
		$WideImage->destroy();
		$json['Personalizado'][] = Imgs::src("_{$id_venda}_{$new_name}", 'imagepersonalize');
	}
}

$return[] = $json + $personalizado_decode[0];

$PV->personalizado = trim( json_encode( $return , JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
if( $PV->save() ) {
	echo '<span id="status">true</span>';
}

include '../rodape.php';