<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of ProdutosPersonalizados
 *
 * @author renan
 */
class ProdutosPersonalizados extends ActiveRecord
{
  //    static $before_save = array('in_store');
  static $table = 'produtos_personalizado';

  static $has_one = array(
    array(
      'produto',
      'class_name' => 'Produtos',
      'primary_key' => 'codigo_id',
      'foreign_key' => 'codigo_id',
      'group' => 'codigo_id'
    )
  );
}
