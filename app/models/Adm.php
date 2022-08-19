<?php

class Adm extends ActiveRecord
{
  static $table = 'adm';

  static $timestamp = false;

  //    static $before_save = array('in_store');

  static $has_many = array(
    array(
      'paginas',
      'class_name' => 'AdmPermissoes',
      'foreign_key' => 'id',
      'primary_key' => 'id_adm',
      //            'conditions' => [ '' ],

    ),
  );
  static $belongs_to = array(
    array(
      'grupos',
      'class_name' => 'AdmGrupos',
      'primary_key' => 'id_adm_grupos',
      'foreign_key' => 'id',
    )
  );


  public static function getUser($user = '', $pass = '')
  {
    $rws = self::first(['conditions' => ['usuario=? and senha=?', addslashes($user), addslashes($pass)]]);
    if ((is_object($rws) ? count($rws) : 0) > 0)
      return json_decode($rws->to_json());
    else
      return null;
  }
}
