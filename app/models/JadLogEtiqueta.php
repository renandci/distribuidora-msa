<?php

class JadLogEtiqueta extends ActiveRecord
{
  static $table = 'jadlog_etiqueta';

  static $before_save = ['in_store'];

  static $has_one = [
    [
      'pedido',
      'class_name' => 'Pedidos',
      'primary_key' => 'id_pedido',
      'foreign_key' => 'id',
    ], [
      'skyhub_order',
      'class_name' => 'SkyhubOrders',
      'primary_key' => 'id_skyhub_orders',
      'foreign_key' => 'id',
    ]
  ];

  static $delegate = [
    ['name', 'status', 'to' => 'pedido'],
  ];
}
