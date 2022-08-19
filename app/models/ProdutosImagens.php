<?php

class ProdutosImagens extends ActiveRecord
{
  static $table = 'produtos_imagens';

  static $before_save = ['in_store'];

  //	static $primary_key = 'codigo_id';

  static $has_one = [
    [
      'produto',
      'class_name' => 'Produtos',
      'primary_key' => 'codigo_id',
      'foreign_key' => 'codigo_id',
      'group' => 'codigo_id'
    ]
  ];
}
