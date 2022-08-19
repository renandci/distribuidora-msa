<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of Grupos
 *
 * @author renan
 */
class Grupos extends ActiveRecord
{
  static $table = 'grupos';

  static $before_save = ['in_store'];

  static $validates_presence_of = [];

  // static $has_one = [ [
  // 		'test',
  // 		'class_name' => 'ProdutosMenus',
  // 		'foreign_key' => 'id_grupo',
  // 		'primary_key' => 'id',
  // 	]
  // ];

}
