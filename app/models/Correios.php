<?php

class Correios extends ActiveRecord
{
  static $table = 'correios';

  static $before_save = ['in_store'];

  public $CorreiosInformacoes;

  static $has_many = [
    [
      'etiquetas',
      'class_name' => 'CorreiosEtiquetas',
      'primary_key' => 'id',
      'foreign_key' => 'id_correios',
    ], [
      'etiquetas_servicos',
      'class_name' => 'CorreiosServicos',
      'primary_key' => 'id',
      'foreign_key' => 'id_correios',
    ]
  ];
}
