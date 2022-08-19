<?php

class OpcoesTipo extends ActiveRecord
{
  static $table = 'opcoes_tipo';

  static $before_save = ['in_store'];

  static $validates_presence_of = [];


  static $has_many = [
    [
      'cor_all',
      'class_name' => 'Cores',
      'primary_key' => 'id',
      'foreign_key' => 'opcoes_id',
      'conditions' => ['excluir=0']
    ],
    [
      'tam_all',
      'class_name' => 'Tamanhos',
      'primary_key' => 'id',
      'foreign_key' => 'opcoes_id',
      'conditions' => ['excluir=0']
    ]
  ];
}
