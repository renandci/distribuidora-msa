<?php

class AdmGrupos extends ActiveRecord
{
  static $table = 'adm_grupos';

  static $timestamp = false;

  static $has_many = array(
    array(
      'paginas',
      'class_name' => 'AdmPermissoes',

      //            'foreign_key' => 'id',
      //            'primary_key' => 'id_adm_grupos',

      'foreign_key' => 'id_adm_grupos',
      'primary_key' => 'id',

      //            'conditions' => array( 'id > ? and id_adm_grupos > ? and status = ?', 0, 0, 1 ),
      'group' => 'pagina'
    )
  );

  //    static $before_save = array('in_store');

}
