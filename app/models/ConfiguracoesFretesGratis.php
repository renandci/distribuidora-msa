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
class ConfiguracoesFretesGratis extends ActiveRecord
{
  static $table = 'configuracoes_fretes_gratis';

  static $before_save = ['in_store'];
}
