<?php


class CorreiosPlp extends ActiveRecord
{
  static $table = 'correios_plp';

  static $before_save = ['in_store'];

  static $has_many = [
    [
      'etiquetas',
      'class_name' => 'CorreiosEtiquetas',
      'primary_key' => 'id',
      'foreign_key' => 'id_plp',
      'order' => 'id_pedidos desc'
    ], [
      'etiquetas_servicos',
      'class_name' => 'CorreiosServicos',
      'primary_key' => 'id',
      'foreign_key' => 'id_correios',
    ]
  ];
}
