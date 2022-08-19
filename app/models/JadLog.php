<?php

class JadLog extends ActiveRecord
{
  static $table = 'jadlog';

  static $before_save = ['in_store'];

  static $has_many = [
    [
      'etiquetas',
      'class_name' => 'JadLogEtiqueta',
      'primary_key' => 'id',
      'foreign_key' => 'id_jadlog',
    ], [
      'etiquetas_servicos',
      'class_name' => 'JadLogServicos',
      'primary_key' => 'id',
      'foreign_key' => 'id_jadlog',
    ]
  ];

  static $delegate = [
    ['name', 'servicos', 'to' => 'etiquetas_servicos'],
  ];
}
