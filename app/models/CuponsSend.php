<?php


class CuponsSend extends ActiveRecord
{
  static $table = 'cupons_send';

  static $before_save = array('in_store');

  static $has_one = array(
    array(
      'cupom',
      'class_name' => 'cupons',
      'primary_key' => 'id_cupons',
      'foreign_key' => 'id',
    ),
    array(
      'cliente',
      'class_name' => 'clientes',
      'primary_key' => 'id_clientes',
      'foreign_key' => 'id',
    ),
    array(
      'cliendereco',
      'class_name' => 'ClientesEnderecos',
      'primary_key' => 'id_clientes',
      'foreign_key' => 'id_cliente',
      'conditions' => ['status = "ativo"'],
    )
  );
}
