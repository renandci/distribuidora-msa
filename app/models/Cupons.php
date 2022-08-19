<?php


class Cupons extends ActiveRecord
{
  static $table = 'cupons';

  static $before_save = array('in_store');

  static $has_one = array(
    array(
      'cliente',
      'class_name' => 'Clientes',
      'primary_key' => 'cupom_cliente_id',
      'foreign_key' => 'id',
    )
  );

  static $has_many = array(
    array(
      'pedidos',
      'class_name' => 'Pedidos',
      'primary_key' => 'id',
      'foreign_key' => 'id_cupom',
    ),
    array(
      'cuponssend',
      'class_name' => 'CuponsSend',
      'primary_key' => 'id',
      'foreign_key' => 'id_cupons',
    )
  );


  /**
   * Tenta buscar o menor preco nos produtos
   */
  public static function get_preco_min()
  {
    $r = self::find_by_sql('select min(preco_promo) as preco_promo from produtos where preco_promo > 0 and excluir = 0 and status = 0 order by preco_promo asc');
    return current($r)->to_array();
  }
}
