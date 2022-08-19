<?php
include 'topo.php';

switch($POST['acao'])
{
	case 'EnviarBanners':
		
		$ID = (INT)$POST['id'];
		
		$CAMINHO = URL_VIEWS_BASE_PUBLIC_UPLOAD . '/imgs/banners/';
		
		$IMG = current( $_FILES );
		
		$ext_pathinfo = pathinfo( $IMG['name'] );
		$ext = $ext_pathinfo['extension'];
		
		if( ! empty( $ID ) && $ID > 0 ) {
            $Banner = Banners::find($ID);
            $FOTO = $Banner->to_array();
        }
		
		if( is_file( $CAMINHO . $FOTO['banner'] ) )
			unlink($CAMINHO . $FOTO['banner']);
		
		if( is_file($CAMINHO . 'mobile-' . $FOTO['banner']) )
			unlink($CAMINHO . 'mobile-' . $FOTO['banner']);
		
		// array_map('unlink', glob( $CAMINHO . $FOTO['banner'] ) ? : []);
		
		$NOVO_NOME_IMAGEM = ! empty($FOTO['banner']) && $FOTO['banner'] != '' ? $FOTO['banner'] : uniqid( time() ) . '.' . $ext;
		$NOVO_NOME_IMAGEM = uniqid( time() ) . '.' . $ext;
		
		if( empty( $ID ) && $ID == 0 ) {
            if( Banners::action_cadastrar_editar(['Banners' => [0 => [ 'banner' => $NOVO_NOME_IMAGEM ] ] ], 'cadastrar', 'banner') ) {
                
            }
		} else {
			if( Banners::action_cadastrar_editar(['Banners' => [$ID => [ 'banner' => $NOVO_NOME_IMAGEM ] ] ], 'alterar', 'banner') ) {
                
            }
		}
		
		// Carregar a imagem no upload
		$WideImageTmpName = WideImage\WideImage::load( $IMG['tmp_name'] );
        
		$WideImageTmpName->saveToFile( $CAMINHO . $NOVO_NOME_IMAGEM );
		
		$QuadroBanner = $CAMINHO . $NOVO_NOME_IMAGEM;
		
		$w = $WideImageTmpName->getWidth();
		$h = $WideImageTmpName->getHeight();
        $mobile = str_replace(',', '.', (($w * 35) / 100));
        
		$WideImageBanners480x132 = $WideImageTmpName->resize($mobile);
		
		$WideImageSquare = WideImage\WideImage::load( $QuadroBanner );
        $WideImageSquare480x132 = $WideImageSquare->resize($mobile);
		$WideImageSquare480x132->merge($WideImageBanners480x132, 'center', 'center', 99)->saveToFile( $CAMINHO . 'mobile-' . $NOVO_NOME_IMAGEM );	
		
		$WideImageSquare->destroy();
		$WideImageTmpName->destroy();
		
		// /*-------------------------------------------------------------------------------------------------------------------------------------------*/
		// // Carregar quadro da imagem
        // $QuadroBanner = file_exists( '../' . URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/_quadro_banner.jpg' ) ? '../' . URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/_quadro_banner.jpg' : '../public/imgs/_quadro_banner.jpg';
			
		// // Carregar a imagem no upload
		// $WideImageTmpName = WideImage\WideImage::load( $IMG['tmp_name'] );
        
		// $w = $WideImageTmpName->getWidth();
		// $h = $WideImageTmpName->getHeight();
        
        // $mobile = str_replace(',', '.', (($w * 35) / 100));
        
        // $WideImageBanners = $WideImageTmpName->resize( $w, $h );
		// $WideImageBannersMobile = $WideImageTmpName->resize($mobile);
        
		// $WideImageSquare = WideImage\WideImage::load( $QuadroBanner );
		// $WideImageSquare = $WideImageSquare->resize( $w, $h );
        
		// $WideImageSquare->merge($WideImageBanners, 'center', 'center', 99)->saveToFile( $CAMINHO . $NOVO_NOME_IMAGEM );	
		
        // $WideImageBannersMobile = $WideImageBannersMobile->resize($mobile);
		// $WideImageBannersMobile->merge($WideImageBannersMobile, 'center', 'center', 100)->saveToFile( $CAMINHO . 'mobile-' . $NOVO_NOME_IMAGEM );	
		
		// $WideImageBanners->destroy();
		// $WideImageSquare->destroy();
		// $WideImageTmpName->destroy();
		
		header("Location: /adm/banners-enviar.php?codigo_id={$GET['codigo_id']}&id_banner={$GET['id_banner']}");
		return;
	break;
}

if( isset($GET['id_banner']) && $GET['id_banner'] != '' ) {
    $Banner         = Banners::find($GET['id_banner']);
    $IMAGEM_BANNER  = $Banner->to_array();
}
?>

<div class="tag-opcoes clearfix" id="div-edicao">
	<table width="100%" cellpadding="8" cellspacing="0" border="0">
		<tr>
			<td align="center" width="100%">
				<form id="banner" class="form-caixa-imagens" method="post" enctype="multipart/form-data" action="/adm/banners-enviar.php">
					<input type="file" name="banner" class="ocultar-banners banners" id="click" onchange="document.getElementById('text-image').innerHTML = this.value;"/>
					<img src="<?php echo $IMAGEM_BANNER ? Imgs::src($IMAGEM_BANNER['banner'], 'banners') : Imgs::src('banner-null.gif', 'public');?>" class="imagem-carregar w100" id="banner"/>
					<div style="background-color: #f3f3f3; padding: 5px 0;">
						<a href="javascript:void(0);" class="fa fa-folder-open cor-001 fa-2x" onclick="document.getElementById('click').click();" <?php echo _P( 'banners-enviar', $_SESSION['admin']['id_usuario'], 'incluir|alterar' )?>></a>
					</div>
					<input type="hidden" name="id" value="<?php echo $IMAGEM_BANNER['id']?>">
					<input type="hidden" name="acao" value="EnviarBanners">
				</form>
			</td>
		</tr>
	</table>
	<style type="text/css">
        input[type=file]{
            display: none;
        }
		.tag-opcoes{
			position: relative;
			width: 100%;
		}
		.tag-opcoes h2{
			position: relative;
			padding: 8px;
			text-align: center;
			margin: 0;
		}
		#banners img{
			width: 700px;
			height: auto;
		}
		.ocultar-banners{
			display: none;
		}
		.form-caixa-imagens{
			border: solid 1px #f3f3f3;
			width: 100%;
		}
	</style>
</div>
<?php
include 'rodape.php';
