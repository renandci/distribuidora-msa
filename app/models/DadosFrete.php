<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of DadosFrete
 *
 * @author renan
 */
class DadosFrete extends ActiveRecord
{
  static $table = 'dados_frete';

  static $before_save = ['in_store'];
}
