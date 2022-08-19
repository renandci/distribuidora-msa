<?PHP
// session_start();

include "../db.php";
include "../includes/bibli-funcoes.php";

switch( $_POST['acao'] )
{
	case 'enviar-imagem' :
		
		$FOTO_INPUT	= $_POST['id'];
		$STR_POST 	= explode( "-", $_POST['id'] );
		$fotos 		= $STR_POST[0];
		$codigo_id 	= $STR_POST[1];
		$id_cor 	= intval( $STR_POST[2] );

		$query = mysqli_query($conexao,"select nome_produto, {$fotos} from produtos where codigo_id = {$codigo_id} and id_cor = {$id_cor}");
		while( $r = mysqli_fetch_assoc( $query ) )
		{
			// var nome foto - numero foto
			// $nome_foto = trim( converter_texto( substr( "{$r['nome_produto']}", 0, 80 ) ) . "-{$FOTO_INPUT}.jpg" );
			$nome_foto = uniqid(time()) . "-" . time() . ".jpg";
			$nome_foto_excluir = $r["{$fotos}"];
		}
		mysqli_free_result( $query );
		
		$nome_foto_grande = "../public/imgs/produtos/{$nome_foto_excluir}";
		$nome_foto_pequena = "../public/imgs/produtos/smalls/{$nome_foto_excluir}";
		
		if( file_exists( $nome_foto_grande ) && $nome_foto_excluir )
		{
			unlink( $nome_foto_grande );
		}
		if( file_exists( $nome_foto_pequena ) && $nome_foto_excluir )
		{
			unlink( $nome_foto_pequena );
		}			
		
		if( move_uploaded_file( $_FILES[$FOTO_INPUT]["tmp_name"], "../public/imgs/produtos/{$nome_foto}" ) )
		{
			if( mysqli_query($conexao,"update produtos set {$fotos} = '{$nome_foto}' where codigo_id = {$codigo_id} and id_cor = {$id_cor} ") )
			{
				$str['mensagem'] = "Imagem criada com sucesso!";
				$str['imagem'] = "{$nome_foto}";
				$str['erros'] = false;
			}
			else
			{
				$str['mensagem'] = "Não foi possível realizar o nome da imagem!";
				$str['imagem'] = "../public/imagenssem-foto-produto.png";
				$str['erros'] = true;
			}
		}
		else
		{
			$str['mensagem'] = "Não foi possível enviar a imagem!";
			$str['imagem'] = "../public/imagenssem-foto-produto.png";
			$str['erros'] = true;
		}
		logs( "Cadastro de imagens: {$nome_foto}", $_SESSION['admin']['id_usuario'] );
		mysqli_close( $conexao );
		exit( json_encode( $str ) );
	break;
	 
	case 'redimensionar-imagem-grande' :
		$imagem_input = trim( $_POST['imagem'] );
		$caminhoimagem = "../public/imagensprodutos/{$imagem_input}";
		
		$width = 700;
		$height = 700;
		list($width_orig, $height_orig) = getimagesize($caminhoimagem);
		$ratio_orig = $width_orig/$height_orig;
		if ($width/$height > $ratio_orig)
		{
		  $width = $height*$ratio_orig;
		}
		else
		{
		  $height = $width/$ratio_orig;
		}
		$image_p = imagecreatetruecolor($width, $height);
		$image = imagecreatefromjpeg($caminhoimagem);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		
		if( imagejpeg($image_p, $caminhoimagem, 85) )
		{
			$str['imagem'] = $imagem_input;
			$str['mensagem'] = "Imagem criada com sucesso!";
			echo json_encode($str);
		}
		
		imagedestroy($image_p);
		exit;
	break;
	
	case 'criar-imagem-grande' :
		// sleep(0.5);
		$imagem_input = trim( $_POST['imagem'] );
		
		$imagem_original = "../public/imagensprodutos/{$imagem_input}";
		$nova_imagem_criada = "../public/imagensquadro-imagem.jpg";
		
		list($width_img_criar, $height_img_criar) = getimagesize($nova_imagem_criada);
		$width = $width_img_criar;
		$height = $height_img_criar;
		
		list($width_orig, $height_orig) = getimagesize($imagem_original);
		$ratio_orig = $width_orig/$height_orig;
		if ($width/$height > $ratio_orig)
		{
		  $width = $height * $ratio_orig;
		}
		else
		{
		  $height = $width / $ratio_orig;
		}
		
		header('content-type: image/jpeg');
		$marca = imagecreatefromjpeg( $imagem_original );		// não esquecer de verificar o nome do arquivo
		$foto = imagecreatefromjpeg( $nova_imagem_criada ); 	// não esquecer de verificar o nome do arquivo

		// pega as dimensoes da marca d'agua
		$marca_larg = imagesx($marca);
		$marca_alt = imagesy($marca);
		
		$WIDTH = ($width_img_criar - $width)/2;
		$HEIGHT = ($height_img_criar - $height)/2;
		
		// insere a marca na imagem
		imagecopyresampled( $foto, $marca, $WIDTH, $HEIGHT, 0, 0, $marca_larg, $marca_alt, $marca_larg, $marca_alt );
		
		if( imagejpeg($foto, "../public/imagensprodutos/{$imagem_input}", 95) )
		{
			$str['mensagem'] = "Imagem redimensionada com sucesso!";
			$str['imagem'] = $imagem_input;			
			echo json_encode($str);
		}
		imagedestroy($foto);
		exit;
	break;
	
	case 'criar-imagem-pequena' :
		sleep(1);
		$imagem_input = trim( $_POST['imagem'] );
		$imagem = "../public/imagensprodutos/{$imagem_input}"; // imagem que será redimensionada
		$imagem_redimensionada = "../public/imagensprodutos/smalls/{$imagem_input}"; //nova imagem criada

		list($largura, $altura) = getimagesize($imagem);
		$nova_largura = 300; // nova largura

		$nova_altura = ($nova_largura * $altura) / $largura; // calcula a nova altura
		$image_p = imagecreatetruecolor($nova_largura, $nova_altura); 
		$image = imagecreatefromjpeg($imagem);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura, $altura);
		if( imagejpeg($image_p, $imagem_redimensionada, 90) )
		{			
			$str['imagem'] = "../public/imagensprodutos/smalls/{$imagem_input}";
			$str['mensagem'] = "Imagem criada com sucesso!";
			echo json_encode($str);
		}
		imagedestroy($image_p);
		exit;
	break;		
	
	case 'excluir-imagem' : 
		
		$FOTO_INPUT	= $_POST['id'];
		$STR_POST 	= explode( "-", $_POST['id'] );
		$fotos 		= $STR_POST[0];
		$codigo_id 	= intval( $STR_POST[1] );
		$id_cor 	= intval( $STR_POST[2] );
		
		$query = mysqli_query($conexao,"select {$fotos} from produtos where codigo_id = {$codigo_id} and id_cor = {$id_cor}");
		while( $f = mysqli_fetch_assoc( $query ) )
		{
			$nome_foto = $f["{$fotos}"];
		}
		mysqli_free_result( $query );
		
		$nome_foto_grande = "../public/imagensprodutos/{$nome_foto}";
		$nome_foto_pequena = "../public/imagensprodutos/smalls/{$nome_foto}";
		$str['erros'] = 1;
		if( file_exists( $nome_foto_grande ) > 0 )
		{
			@unlink( $nome_foto_grande );
			$str['erros'] = 0;
		}
		if( file_exists( $nome_foto_pequena ) > 0 )
		{
			@unlink( $nome_foto_pequena );
			$str['erros'] = 0;
		}
		
		if( $str['erros'] == 0 )
		{
			mysqli_query($conexao,"update produtos set {$fotos} = '' where codigo_id = {$codigo_id} and id_cor = {$id_cor}");
			$str['mensagem'] = "Imagem excluida com sucesso!";
		}
		else
		{
			mysqli_query($conexao,"update produtos set {$fotos} = '' where codigo_id = {$codigo_id} and id_cor = {$id_cor}");
			$str['mensagem'] = "Imagem excluida com sucesso!";
		}
		
		mysqli_close( $conexao );
		exit( json_encode( $str ) );
	break;
	
	case 'enviar-banner' :
		$ID_BANNER 			= isset( $_GET['id_banner'] ) && $_GET['id_banner'] != "" ? $_GET['id_banner'] : "";
		$LINK_PRODUTO		= isset( $_POST['produto'] ) && $_POST['produto'] != "" ? $_POST['produto'] : "";
		$IMAGEM_BANNER 		= $_FILES['banner'];
		$IMAGEM_BANNER_NOME = array();
		
		
		if( $ID_BANNER ){
			$IMAGEM_BANNER_NOME = mysqli_fetch_assoc( mysqli_query($conexao, "select banner from banners where md5( id ) = '{$ID_BANNER}' " ) );
		}else{
			$IMAGEM_BANNER_NOME['banner'] = "BANNER-" . md5( time() ) . ".jpg";
		}
		
		if( move_uploaded_file( $IMAGEM_BANNER['tmp_name'], "../public/imagensbanners/{$IMAGEM_BANNER_NOME['banner']}" ) )
		{
			if( mysqli_query($conexao,"update banners set banner = '{$IMAGEM_BANNER_NOME['banner']}', produto = '{$LINK_PRODUTO}' where md5( id ) = '{$ID_BANNER}' ") and $ID_BANNER != "" )
			{
				$str['mensagem'] = "Banner alterado com sucesso!";
				$str['imagem'] = "../public/imagensbanners/{$IMAGEM_BANNER_NOME['banner']}";
				$str['erros'] = false;
			}
			else if( mysqli_query($conexao,"insert into banners ( produto, banner ) values ( '{$LINK_PRODUTO}', '{$IMAGEM_BANNER_NOME['banner']}' ) ") and $ID_BANNER == "" )
			{
				$str['mensagem'] = "Banner cadastrado com sucesso!";
				$str['imagem'] = "../public/imagensbanners/{$IMAGEM_BANNER_NOME['banner']}";
				$str['erros'] = false;
			}
			else
			{
				$str['mensagem'] = "Não foi possível realizar o nome da imagem!";
				$str['imagem'] = "../public/imagensbanners/banner-null.gif";
				$str['erros'] = true;
			}
		}
		else
		{
			$str['mensagem'] = "Não foi possível enviar o banner!";
			$str['imagem'] = "../public/imagensbanners/banner-null.gif";
			$str['erros'] = true;
		}
		/* logs( "Cadastro de imagens: {$nome_foto}", $_SESSION['admin']['id_usuario'] );*/
		exit( json_encode( $str ) );
	break;
}