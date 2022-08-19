<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of ConfiguracoesPaginas
 *
 * @author renan
 */
class ConfiguracoesPaginas extends ActiveRecord
{
  static $table = 'configuracoes_paginas';

  static $before_save = array('in_store');
}
