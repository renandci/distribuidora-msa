<?php

class MercadoLivre extends ActiveRecord
{
  static $table = 'mercadolivre';

  static $before_save = ['in_store'];

  static $has_many = [
    [
      'produto',
      'class_name' => 'MercadoLivreProdutos',
      'primary_key' => 'id',
      'foreign_key' => 'mercadolivre_id',
      'thought' => 'produtos'
    ]
  ];

  /**
   * Produtos ativos no mercado livre
   */
  public static function ml_active()
  {
    return count(self::find_by_sql('select * from mercadolivre_produtos where produtos_ml_status = "active"'));
  }

  /**
   * Produtos pausados no mercado livre
   */
  public static function ml_paused()
  {
    return count(self::find_by_sql('select * from mercadolivre_produtos where produtos_ml_status = "paused"'));
  }

  /**
   * Produtos closed no mercado livre
   */
  public static function ml_closed()
  {
    return count(self::find_by_sql('select * from mercadolivre_produtos where produtos_ml_status = "closed"'));
  }

  /**
   * Retornar as marcas dos produtos
   */
  public static function ml_marcas_rows()
  {
    return self::find_by_sql(''
      . 'SELECT id, marcas, UPPER(SUBSTRING(marcas, 1, 1)) as letra '
      . 'FROM marcas '
      . 'WHERE excluir = 0 AND EXISTS('
      . 'SELECT 1 FROM mercadolivre_produtos WHERE EXISTS('
      . 'SELECT 1 FROM produtos WHERE mercadolivre_produtos.produtos_codigo_id=produtos.codigo_id AND marcas.id=produtos.id_marca)) '
      . 'GROUP BY marcas '
      . 'ORDER BY marcas ASC');
  }

  /**
   * Retornar as marcas dos produtos
   */
  public static function ml_produtos_status_rows()
  {
    return self::find_by_sql('SELECT produtos_ml_status as status '
      . 'FROM mercadolivre_produtos '
      . 'WHERE produtos_ml_status != "closed" '
      . 'GROUP BY produtos_ml_status '
      . 'ORDER BY produtos_ml_status ASC');
  }
}
