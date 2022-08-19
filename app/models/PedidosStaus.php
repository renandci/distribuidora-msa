<?php

class PedidosStatus extends ActiveRecord
{

  static $table = 'pedidos_status';

  static $before_save = ['in_store'];
}
