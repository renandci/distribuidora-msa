<?php

class Cores extends ActiveRecord
{
  static $table = 'cores';

  static $before_save = ['in_store'];

  static $validates_presence_of = [];

  static $has_one = [
    [
      'opcoes',
      'class_name' => 'OpcoesTipo',
      'primary_key' => 'opcoes_id',
      'foreign_key' => 'id'
    ]
  ];
}
