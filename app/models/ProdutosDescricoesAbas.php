<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of ProdutosComentarios
 *
 * @author renan
 */
class ProdutosDescricoesAbas extends ActiveRecord
{
  static $table = 'produtos_descricoes_abas';

  static $before_save = array('in_store');

  static $has_one = array(
    array(
      'produto',
      'class_name' => 'Produtos',
      'primary_key' => 'id_produto',
      'foreign_key' => 'id'
    )
  );
}
