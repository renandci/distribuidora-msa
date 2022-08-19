<?php

class Carrinho extends ActiveRecord
{
  static $cache = true;

  static $table = 'carrinho';

  static $before_save = ['in_cliente', 'in_store'];

  static $before_update = ['in_store'];

  static $has_one = [
    [
      'carrinho_prod',
      'class_name' => 'Produtos',
      'primary_key' => 'id_produto',
      'foreign_key' => 'id',
    ],
    // [
    //   'produto',
    //   'class_name' => 'ProdutosViewsTemp',
    //   'primary_key' => 'id_produto',
    //   'foreign_key' => 'id',
    // ],
    [
      'produto',
      'class_name' => 'Produtos',
      'primary_key' => 'id_produto',
      'foreign_key' => 'id',
    ],
    [
      'prod',
      'class_name' => 'Produtos',
      'primary_key' => 'id_produto',
      'foreign_key' => 'id',
    ],
    [
      'cliente',
      'class_name' => 'Clientes',
      'primary_key' => 'id_cliente',
      'foreign_key' => 'id',
    ],
    [
      'promocoes',
      'class_name' => 'Promocoes',
      'foreign_key' => 'id_marca',
      'primary_key' => 'id_marca',
    ]
  ];

  static $delegate = [
    ['name', 'id',          'to' => 'promocoes', 'prefix' => 'promo'],
    ['name', 'codigo_id',   'to' => 'promocoes', 'prefix' => 'promo'],
    ['name', 'setup_ini',   'to' => 'promocoes'],
    ['name', 'setup_fin',   'to' => 'promocoes'],
    ['name', 'setup_type',  'to' => 'promocoes'],
    ['name', 'setup_value', 'to' => 'promocoes'],
    ['name', 'setup_text',  'to' => 'promocoes'],
    ['name', 'setup_color', 'to' => 'promocoes'],
    ['name', 'setup_hex',   'to' => 'promocoes']
  ];

  /**
   * Sempre atuliza o carrinho de compra quando possivel cliente estiver logado no sistema
   */
  public function in_cliente()
  {
    $session = $this->global_store('cliente_session');
    $this->id_cliente = !empty($session) ? $session['id'] : 0;
    $this->cliente_ip = retornaIpReal();
  }

  // /**
  //  * Dados do carrinho de compras
  //  */
  // public static function cart($session = null)
  // {
  //   $session_id = $session ? $session : self::global_store('session_id');
  //   return self::find_by_sql('SELECT * FROM view_carrinho_all WHERE id_session=?', [$session_id]);
  // }

  // /**
  //  * Total de linhas do carrinho de compras
  //  */
  // public static function countCart()
  // {
  //   return count(self::cart());
  // }

  // public static function CuponsDescontoInsert()
  // {
  //   return;
  // }

  // /**
  //  * Retorna o frete para o carrinho de compras
  //  */
  // public static function getCarrinhoFrete($SessionSistema = '', $SessionCliente = '')
  // {
  //   $UpCarrinho = self::first(['conditions' => ['id_session=?', $SessionSistema]]);
  //   $UpCarrinho->cep = '';
  //   $UpCarrinho->frete_tipo = '';
  //   $UpCarrinho->frete_valor = '0.00';
  //   if (!$UpCarrinho->save())
  //     return 'Não foi possivel atulizar os dados do seu carrinho!';
  // }
}
