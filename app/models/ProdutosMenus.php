<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of ProdutosMenus
 *
 * @author renan
 */
class ProdutosMenus extends ActiveRecord
{

  static $table = 'produtos_menus';

  static $before_save = ['in_store'];

  static $validates_numericality_of = [];

  static $has_one = [
    [
      'promocoes',
      'class_name' => 'Promocoes',
      'foreign_key' => 'id_marca',
      'primary_key' => 'id_marca',
    ],
    [
      'grupo',
      'class_name' => 'Grupos',
      'primary_key' => 'id_grupo',
      'foreign_key' => 'id',
      'conditions' => ['excluir=?', 0]
    ],
    [
      'subgrupo',
      'class_name' => 'SubGrupos',
      'primary_key' => 'id_subgrupo',
      'foreign_key' => 'id',
      'conditions' => ['excluir=?', 0]
    ], [
      'produto',
      'class_name' => 'Produtos',
      'primary_key' => 'codigo_id',
      'foreign_key' => 'codigo_id',
      'conditions' => ['excluir=?', 0],
      'group' => 'codigo_id'
    ], [
      'menu_grupo',
      'class_name' => 'Grupos',
      'primary_key' => 'id_grupo',
      'foreign_key' => 'id',
      'conditions' => ['excluir=?', 0],
      'order' => 'ordem asc'
    ], [
      'menu_subgrupo',
      'class_name' => 'SubGrupos',
      'primary_key' => 'id_subgrupo',
      'foreign_key' => 'id',
      'conditions' => ['excluir=?', 0],
      'order' => 'ordem asc'
    ]
  ];

  static $belongs_to = [
    [
      'parent_produtos',
      'class_name' => 'Produtos',
      'primary_key' => 'codigo_id',
      'foreign_key' => 'codigo_id',
      'conditions' => ['excluir=?', 0],
      'group' => 'codigo_id'
    ], [
      'parent_grupo',
      'class_name' => 'Grupos',
      'primary_key' => 'id_grupo',
      'foreign_key' => 'id',
      'conditions' => ['excluir=?', 0]
    ], [
      'parent_subgrupo',
      'class_name' => 'SubGrupos',
      'primary_key' => 'id_subgrupo',
      'foreign_key' => 'id',
      'conditions' => ['excluir=?', 0]
    ],
  ];

  static $delegate = [
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
