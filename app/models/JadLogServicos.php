<?php

class JadLogServicos extends ActiveRecord
{
  static $table = 'jadlog_servicos';

  static $after_save = ['in_store'];

  static $has_one = [
    [
      'etiquetas',
      'class_name' => 'JadLog',
      'primary_key' => 'id_jadlog',
      'foreign_key' => 'id',
    ]
  ];

  // static $delegate = [
  // 	[ 'name', 'status', 'to' => 'pedido' ],
  // ];
}
