<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of ProdutosRelacionados
 *
 * @author renan
 */
class ProdutosRelacionados extends ActiveRecord
{
  static $table = 'produtos_relacionados';

  static $before_save = ['in_store'];

  static $has_one = [
    [
      'produto_relacao',
      'class_name' => 'Produtos',
      'primary_key' => 'produtos_id',
      'foreign_key' => 'id',
    ], [
      'promocoes',
      'class_name' => 'Promocoes',
      'foreign_key' => 'id_marca',
      'primary_key' => 'id_marca',
    ]
  ];

  static $has_many = [
    [
      'grupos_relacionados',
      'class_name' => 'ProdutosRelacionados',
      'primary_key' => 'grupos_id',
      'foreign_key' => 'grupos_id'
    ]
  ];

  static $delegate = [
    ['name', 'id', 'to' => 'produto_relacao'],
    ['name', 'nome_produto', 'to' => 'produto_relacao'],
    ['name', 'codigo_produto', 'to' => 'produto_relacao'],
    ['name', 'preco_venda', 'to' => 'produto_relacao'],
    ['name', 'preco_promo', 'to' => 'produto_relacao'],
    ['name', 'estoque', 'to' => 'produto_relacao'],
    ['name', 'id_marca', 'to' => 'produto_relacao'],
    ['name', 'placastatus', 'to' => 'produto_relacao'],
    ['name', 'codigo_id', 'to' => 'produto_relacao'],
    ['name', 'frete', 'to' => 'produto_relacao'],

    ['name', 'id',          'to' => 'promocoes', 'prefix' => 'promo'],
    ['name', 'codigo_id',   'to' => 'promocoes', 'prefix' => 'promo'],
    ['name', 'setup_ini',   'to' => 'promocoes'],
    ['name', 'setup_fin',   'to' => 'promocoes'],
    ['name', 'setup_type',  'to' => 'promocoes'],
    ['name', 'setup_value', 'to' => 'promocoes'],
    ['name', 'setup_text',  'to' => 'promocoes'],
    ['name', 'setup_color', 'to' => 'promocoes'],
    ['name', 'setup_hex',   'to' => 'promocoes']
  ];
}
