<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of FretesGratis
 *
 * @author renan
 */
class ConfiguracoesFretesEnvios extends ActiveRecord
{
  static $table = 'configuracoes_fretes_envios';

  static $timestamp = false;

  static $before_save = array('in_store');


  public function get_fretes_envios_all()
  {
    $loop = null;
    $test = $this->read_attribute('fretes_envios');
    foreach (explode('|', $test) as $rws) {
      $text = explode(' ', $rws);
      $text = $text[0];
      $text_int = (int)soNumero($rws);
      $loop[$text] = sprintf('%05s', $text_int);
    }
    return $loop;
  }

  public function get_envios_correios()
  {
    $loop = null;
    $test = $this->fretes_envios_all;
    foreach ($test as $key => $rws) {
      $text_int = (int)soNumero($rws);
      if (in_array($key, ['PAC', 'SEDEX']))
        $loop[$key] = sprintf('%05s', $text_int);
    }
    return $loop;
  }

  public function get_envios_jadlog()
  {
    $loop = null;
    $test = $this->read_attribute('fretes_envios');
    foreach (explode('|', $test) as $rws) {
      $key = explode('*', $rws)[0];
      $text_int = (int)soNumero($rws);
      if (in_array($key, ['.PACKAGE', 'ECONÔMICO', 'PICKUP', '.COM']))
        $loop[$key] = sprintf('%s', $text_int);
    }
    return $loop;
  }
}
