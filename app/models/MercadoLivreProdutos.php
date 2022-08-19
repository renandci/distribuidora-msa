<?php

class MercadoLivreProdutos extends ActiveRecord
{
  static $table = 'mercadolivre_produtos';

  static $before_save = array('in_store');

  static $has_many = [
    [
      'produto',
      'class_name' => 'Produtos',
      'primary_key' => 'produtos_codigo_id',
      'foreign_key' => 'codigo_id',
    ]
  ];
}
