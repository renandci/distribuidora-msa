<?php

class GridProdutos extends ActiveRecord
{
  static $table = 'grid_produtos';

  static $before_save = ['in_store'];

  static $has_one = [
    [
      'grid_one',
      'class_name' => 'Grid',
      'primary_key' => 'id_grid',
      'foreign_key' => 'id'
    ]
  ];

  static $has_many = [
    [
      'grid_many',
      'class_name' => 'Grid',
      'primary_key' => 'id_grid',
      'foreign_key' => 'id'
    ]
  ];
}
