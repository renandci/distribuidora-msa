<?php

abstract class Controller 
{
	protected static $array_before = [];

	protected static $array_after = [];
	
	protected static $CAMINHO = PATH_ROOT . '/' . URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/produtos/';
	
	public static function moeda($get_valor) { 
	   $source = ['.', ','];
	   $replace = ['', '.'];
	   $valor = str_replace($source, $replace, $get_valor); //remove os pontos e substitui a virgula pelo ponto 
	   return $valor; //retorna o valor formatado para gravar no banco 
	}
	
    public static function test_float($test) {

        if (!is_scalar($test)) {return false;}

        $type = gettype($test);

        if ($type === "float") {
            return true;
        } else {
            return preg_match("/^\\d+\\.\\d+$/", $test) === 1;
        }
    }
	
	public static function group_assoc($array, $key) {
		$return = array();
		foreach($array as $v) {
			$return[$v[$key]][] = $v;
		}
		return $return;
	}
	
	
	public static function up_image( $image = null, $name = null ) {
		
		try {
			// Carregar a imagem no upload
			$WideImageTmpName = WideImage\WideImage::load( $image );
			$WideImage960x960 = $WideImageTmpName->resize(960, 960);
			$WideImage480x480 = $WideImageTmpName->resize(480, 480);
			$WideImage315x315 = $WideImageTmpName->resize(240, 240);
			
			// Carregar quadro da imagem
			$WideImageSquare = WideImage\WideImage::load( PATH_ROOT . '/public/imgs/_quadro.jpg' );
			$WideImageSquare960x960 = $WideImageSquare->resize(960, 960);
			$WideImageSquare480x480 = $WideImageSquare->resize(480, 480);
			$WideImageSquare315x315 = $WideImageSquare->resize(240, 240);
			
			// Salva as imagens 
			$WideImageSquare960x960->merge($WideImage960x960, 'center', 'center', 95)->saveToFile( self::$CAMINHO . $name );	
			$WideImageSquare480x480->merge($WideImage480x480, 'center', 'center', 93)->saveToFile( self::$CAMINHO . 'medium/' . $name );	
			$WideImageSquare315x315->merge($WideImage315x315, 'center', 'center', 89)->saveToFile( self::$CAMINHO . 'smalls/' . $name );	
			
			$WideImageSquare960x960->destroy();	
			$WideImageSquare480x480->destroy();	
			$WideImageSquare315x315->destroy();	
			$WideImageSquare->destroy();	
			
		} catch(Exception $e) {
			return $e->getMessage();
		}
	}
	
}