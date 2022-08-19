<?php

class AdmPermissoes extends ActiveRecord
{
  static $table = 'adm_permissoes';

  static $timestamp = false;

  static $has_one = [
    [
      'grupo',
      'class_name' => 'AdmGrupos',
      'foreign_key' => 'id',
      'primary_key' => 'id_adm_grupos',
    ], [
      'adm',
      'class_name' => 'Adm',
      'foreign_key' => 'id',
      'primary_key' => 'id_adm'
    ]
  ];

  static $belongs_to = [
    [
      'grupos',
      'class_name' => 'AdmGrupos',
      'foreign_key' => 'id_adm_grupos',
      'order' => 'ordem desc'
    ]
  ];

  public static function p($pagina = null, $session = null)
  {
  }
}
