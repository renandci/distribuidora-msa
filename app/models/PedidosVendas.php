<?php

class PedidosVendas extends ActiveRecord
{
  static $table = 'pedidos_vendas';

  static $before_save = ['in_store'];

  static $has_many = [
    [
      'produtos_vendas',
      'class_name' => 'Produtos',
      'primary_key' => 'id_produto',
      'foreign_key' => 'id'
    ]
  ];

  static $has_one = [
    [
      'pedido',
      'class_name' => 'Pedidos',
      'primary_key' => 'id_pedido',
      'foreign_key' => 'id'
    ], [
      'produto',
      'class_name' => 'Produtos',
      'primary_key' => 'id_produto',
      'foreign_key' => 'id'
    ]
  ];


  /**
   * Salva os pedidos da venda
   * @param type $id_pedido
   * @param type $id_produto
   * @param type $preco_custo
   * @param type $valor_pago
   * @param type $quantidade
   * @param type $personalizado
   * @return string
   */
  public static function gerarVendas($id_pedido = 0, $id_produto = 0, $preco_custo = 0, $valor_pago = 0, $quantidade = 0, $personalizado = '')
  {
    $p_v = new PedidosVendas();
    $p_v->id_pedido = $id_pedido;
    $p_v->id_produto = $id_produto;
    $p_v->preco_custo = $preco_custo;
    $p_v->valor_pago = $valor_pago;
    $p_v->quantidade = $quantidade;
    $p_v->personalizado = $personalizado;
    if (!$p_v->save()) {
      throw new Exception('Não foi possivel inserir os produtos!');
    }
  }
}
