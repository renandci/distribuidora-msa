<?php

class PedidosTransacoes extends ActiveRecord
{
  static $table = 'pedidos_transacoes';

  static $after_save = ['in_store'];

  // static $before_save = ['in_store'];

  static $has_one = [
    [
      'pedidos',
      'class_name' => 'Pedidos',
      'primary_key' => 'id_pedido',
      'foreign_key' => 'id'
    ]
  ];
}
