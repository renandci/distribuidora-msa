<?php

class NfeNotas extends ActiveRecord
{
  static $table = 'nfe_notas';

  static $before_save = ['in_store'];

  static $validates_presence_of = [];

  static $has_one = [
    [
      'pedido',
      'class_name' => 'Pedidos',
      'primary_key' => 'id_pedido',
      'foreign_key' => 'id',
    ], [
      'emitente',
      'class_name' => 'NfeEmitentes',
      'primary_key' => 'id_emitentes',
      'foreign_key' => 'id',
    ], [
      'skyhub_order',
      'class_name' => 'SkyhubOrders',
      'primary_key' => 'id_skyhub_orders',
      'foreign_key' => 'id'
    ], [
      'skyhub',
      'class_name' => 'SkyhubOrders',
      'primary_key' => 'id_skyhub_orders',
      'foreign_key' => 'id'
    ]
  ];
}
