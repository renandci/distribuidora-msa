<?php

class PedidosBoletos extends ActiveRecord
{
  static $table = 'pedidos_boletos';

  static $before_save = array('in_store');

  static $timestamp = false;
}
