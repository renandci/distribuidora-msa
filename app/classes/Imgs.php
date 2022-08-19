<?php

class Imgs
{
  protected static  $nulos = null;

  protected static  $dominios = ['www.', '.dev', '.com', '.br', 'imagens.', 'static.', '.dci', '.test'];

  protected static $http_http = 'http://';

  protected static $http_https = 'https://';

  protected static $replace_from = ['www', 'static'];

  protected static $replace_to = ['imagens', 'imagens'];

  private static function get_url_base()
  {
    return strtolower($_SERVER['SERVER_NAME']);
  }

  private static function get_url_base_img()
  {
    if (strstr(self::get_url_base(), '.test'))
      return '//imagens.ecommerce.test';
    return self::get_url_base();
  }

  private static function get_url_base_imgx()
  {
    if (strstr(self::get_url_base(), '.test'))
      return '//www.distribuidoramsa.test/';
    else
      return '//www.distribuidoramsa.com.br/';
  }

  private static function get_assets()
  {
    return str_replace(self::$dominios, self::$nulos, self::get_url_base());
  }

  private static function get_securit()
  {
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
      return self::$http_https;

    return self::$http_http;
  }

  public static function img_base64_encode($result)
  {
    return implode(',', ['data:image/jpeg;base64', $result]);
  }

  private static function get_generate($img)
  {

    return [
      // somente para fachadavirtual
      'imgstemp' => '/imgstemp_' . self::get_assets() . '_' . $img, 'imagepersonalize' => self::get_securit() . preg_replace('/(www.|imagens.|static.|store.)/', 'imagens.', self::get_url_base()) . '/imagepersonalize_' . self::get_assets() . $img,

      // store
      'large'     => self::get_securit() . preg_replace('/(www.|imagens.|static.|store.)/', 'store.', self::get_url_base()) . '/produtos/' . $img,
      'medium'    => self::get_securit() . preg_replace('/(www.|imagens.|static.|store.)/', 'store.', self::get_url_base()) . '/produtos/medium/' . $img,
      'smalls'   => self::get_securit() . preg_replace('/(www.|imagens.|static.|store.)/', 'store.', self::get_url_base()) . '/produtos/smalls/' . $img,
      'banners'   => self::get_securit() . preg_replace('/(www.|imagens.|static.|store.)/', 'store.', self::get_url_base()) . '/banners/' . $img,
      'imgs'     => self::get_securit() . preg_replace('/(www.|imagens.|static.|store.)/', 'store.', self::get_url_base()) . '/' . $img,

      // public
      'public'   => self::get_securit() . preg_replace('/(www.|imagens.|static.)/', 'imagens.', self::get_url_base_img()) . '/imgs/' . $img,
      'status'   => self::get_securit() . preg_replace('/(www.|imagens.|static.)/', 'imagens.', self::get_url_base_img()) . '/imgs/icons-status/' . $img,
    ];
  }

  private static function get_imgx($img)
  {
    return [
      // store
      'xs'       => sprintf('%sstore-55x55-%s-%s', self::get_url_base_imgx(), self::get_assets(), $img),
      'large'     => sprintf('%sstore-960x960-%s-%s', self::get_url_base_imgx(), self::get_assets(), $img),
      'medium'    => sprintf('%sstore-480x480-%s-%s', self::get_url_base_imgx(), self::get_assets(), $img),
      'smalls'   => sprintf('%sstore-240x240-%s-%s', self::get_url_base_imgx(), self::get_assets(), $img),
      'banners'   => sprintf('%sbanners-1600x553-%s-%s', self::get_url_base_imgx(), self::get_assets(), $img),
      'estufas'  => sprintf('%sestufas-%s-%s', self::get_url_base_imgx(), self::get_assets(), $img),
      'blog'     => sprintf('%sblog-%s-%s', self::get_url_base_imgx(), self::get_assets(), $img),
      'imgs'     => sprintf('%simgs-%s-%s', self::get_url_base_imgx(), self::get_assets(), $img),
      // 'imgs' 		=> self::get_securit() . preg_replace('/(www.|imagens.|static.|store.)/', 'store.', self::get_url_base()) . '/' . $img,

      // public
      // 'public' 	=> self::get_securit() . preg_replace('/(www.|imagens.|static.)/', 'imagens.', self::get_url_base_img()) . '/imgs/' . $img,
      'public'     => sprintf('%s%s', self::get_url_base_imgx(), $img),
      // 'status' 	=> self::get_securit() . preg_replace('/(www.|imagens.|static.)/', 'imagens.', self::get_url_base_img()) . '/imgs/icons-status/' . $img,
      'status'     => sprintf('%s%s', self::get_url_base_imgx(), $img),
    ];
  }

  /**
   * Retorna uma rota para as imagens
   * @param $img
   * @param $filter
   * @return string
   */
  public static function src($img, $filter)
  {
    if ($filter == 'base64' || $filter == 'blob')
      return self::img_base64_encode($img);

    // return self::get_generate($img)[ $filter ];
    return self::get_imgx($img)[$filter];
  }
}
