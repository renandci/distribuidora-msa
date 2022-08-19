<?php

class ProdutosKits extends ActiveRecord
{
  static $table = 'produtos_kits';

  static $before_save = ['in_store'];

  static $has_one = [
    [
      'produto',
      'class_name' => 'Produtos',
      'primary_key' => 'codigo_id_produtos',
      'foreign_key' => 'codigo_id',
    ], [
      'produto2',
      'class_name' => 'Produtos',
      'primary_key' => 'codigo_id',
      'foreign_key' => 'codigo_id'
    ]
  ];

  static $delegate = [
    ['name', 'codigo_produto',  'to' => 'produto'],
    ['name', 'nome_produto',  'to' => 'produto'],
    ['name', 'preco_promo',    'to' => 'produto'],
    ['name', 'ncm',        'to' => 'produto'],
    // ['name', 'chavenfe', 	'to' => 'produto'],
    // ['name', 'id', 			'to' => 'produto']
  ];

  // static $has_many = [ [
  // 'grid_many',
  // 'class_name' => 'Grid',
  // 'primary_key' => 'id_grid',
  // 'foreign_key' => 'id'
  // ]
  // ];
}
