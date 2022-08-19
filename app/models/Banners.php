<?php

class Banners extends ActiveRecord
{
  static $table = 'banners';

  static $before_save = ['in_store'];

  static $has_one = [
    [
      'produto_bann',
      'class_name' => 'Produtos',
      'primary_key' => 'id_produtos',
      'foreign_key' => 'id',
    ]
  ];
}
