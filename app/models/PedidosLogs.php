<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of PedidosLogs
 *
 * @author renan
 */
class PedidosLogs extends ActiveRecord
{
  static $table = 'pedidos_logs';

  static $before_save = ['in_store'];

  static $has_one = [
    [
      'pedido',
      'class_name' => 'Pedidos',
      'primary_key' => 'id_pedido',
      'foreign_key' => 'id',
    ], [
      'adm',
      'class_name' => 'Adm',
      'primary_key' => 'id_adm',
      'foreign_key' => 'id',
    ]
  ];

  /**
   * Salvar os logs que são salvos nos pedidos
   * @param type $id_pedido
   * @param type $id_adm
   * @param type $descricao
   * @param type $status
   * @return string
   */
  public static function logs($pedido_id = 0, $adm_id = 0, $descricao = '', $status = '')
  {
    $c = get_called_class();
    $e = new $c();
    $e->id_pedido = (int)$pedido_id;
    $e->id_adm = (int)$adm_id;
    $e->descricao = $descricao;
    $e->status = !empty((int)$status) ? (int)$status : $e->pedido->status;
    $e->data_envio = date('Y-m-d H:i:s');
    $r = $e->save_log();
    if (!$r['id']) {
      throw new Exception('Não foi possivel fazer alteração de status do pedido!');
    }
  }
}
