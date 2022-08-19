<?php

class PedidosCielo extends ActiveRecord
{
  static $table = 'pedidos_cielo';

  static $before_save = array('in_store');

  /**
   * In Store
   * Salva o id da loja no sistema
   * @global type $CONFIG
   */
  //    public function in_store()
  //    {
  //        global $CONFIG;
  //        $this->loja_id = $CONFIG['loja_id'];
  //    }
}
