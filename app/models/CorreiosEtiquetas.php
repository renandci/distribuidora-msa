<?php


class CorreiosEtiquetas extends ActiveRecord
{
  static $table = 'correios_etiquetas';

  static $before_save = ['in_store'];

  static $has_one = [
    [
      'pedido',
      'class_name' => 'Pedidos',
      'primary_key' => 'id_pedidos',
      'foreign_key' => 'id',
      'order' => 'pedidos.id desc'
    ],
    [
      'skyhub_order',
      'class_name' => 'SkyhubOrders',
      'primary_key' => 'id_skyhub_orders',
      'foreign_key' => 'id'
    ]
  ];

  static $has_many = [
    [
      'servicos',
      'class_name' => 'CorreiosServicos',
      'foreign_key' => 'id_correios',
      'primary_key' => 'id',
    ]
  ];
}
