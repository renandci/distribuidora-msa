<?php


class Configuracoes extends ActiveRecord
{
  static $table = 'configuracoes';

  static $before_save = ['in_store'];

  static $has_one  = [
    [
      'pagamentos',
      'class_name' => 'ConfiguracoesPagamento',
      'foreign_key' => 'loja_id',
      'primary_key' => 'loja_id',
    ], [
      'fretes_envios',
      'class_name' => 'ConfiguracoesFretesEnvios',
      'primary_key' => 'loja_id',
      'foreign_key' => 'loja_id',
    ], [
      'lojas',
      'class_name' => 'Lojas',
      'primary_key' => 'loja_id',
      'foreign_key' => 'loja_id',
    ], [
      'correios',
      'class_name' => 'Correios',
      'primary_key' => 'loja_id',
      'foreign_key' => 'loja_id',
    ], [
      'jadlog',
      'class_name' => 'JadLog',
      'primary_key' => 'loja_id',
      'foreign_key' => 'loja_id',
    ], [
      'carrinho',
      'class_name' => 'Carrinho',
      'foreign_key' => 'produtos.loja_id',
      'primary_key' => 'carrinho.loja_id',
      'select' => 'case when carrinho.quantidade > 0 then sum(carrinho.quantidade) else 0 end as quantidade, case when produtos.preco_promo > 0 then sum(produtos.preco_promo * carrinho.quantidade) else 0.00 end as preco_carrinho ',
      'joins' => ['inner join produtos on produtos.id = carrinho.id_produto']
    ], [
      'questionario',
      'class_name' => 'LojasQuestionario',
      'foreign_key' => 'loja_id',
      'primary_key' => 'loja_id',
    ], [
      'cliente_session',
      'class_name' => 'Clientes',
      // 'methods' => 'teste',
      'foreign_key' => 'loja_id',
      'primary_key' => 'loja_id',
      'conditions' => ['md5(id)=?', CLIENTE_ID]
    ], [
      'skyhub',
      'class_name' => 'Skyhub',
      'foreign_key' => 'loja_id',
      'primary_key' => 'loja_id',
      'conditions' => ['excluir=0']
    ]
  ];

  static $has_many = [
    [
      'transferencias',
      'class_name' => 'ConfiguracoesTransferencia',
      'foreign_key' => 'loja_id',
      'primary_key' => 'loja_id',
    ], [
      'paginas',
      'class_name' => 'ConfiguracoesPaginas',
      'foreign_key' => 'loja_id',
      'primary_key' => 'loja_id',
      'order' => 'ordem asc'
    ],

    [
      'produtos',
      'class_name' => 'Produtos',
      'foreign_key' => 'loja_id',
      'primary_key' => 'loja_id',
      'order' => 'estoque and id desc, id desc'
    ],
    [
      'banners',
      'class_name' => 'Banners',
      'foreign_key' => 'loja_id',
      'primary_key' => 'loja_id',
      'order' => 'IF(ordem = 0, rand(), ordem)'
    ],
    // [
    // 	'produtos',
    //     'class_name' => 'ProdutosViewsTemp',
    //     'foreign_key' => 'loja_id',
    //     'primary_key' => 'loja_id',
    // ],
    // [
    //   'carrinho_all',
    //   'class_name' => 'CarrinhoViewsTemp',
    //   'foreign_key' => 'loja_id',
    //   'primary_key' => 'loja_id',
    //   'conditions' => ['id_session=?', SESSION_ID]
    // ],
    // [
    //   'grupos',
    //   'class_name' => 'ProdutosMenusViewsTemp',
    //   'foreign_key' => 'loja_id',
    //   'primary_key' => 'loja_id',
    //   // 'group' => 'grupo_id',
    //   // 'methods' => ['menus'],
    //   // 'only_method' => ['menus'],
    // ], [
    //   'menus',
    //   'class_name' => 'ProdutosMenusViewsTemp',
    //   'foreign_key' => 'loja_id',
    //   'primary_key' => 'loja_id'
    //   // 'only_method' => ['menus'],
    // ],
    [
      'pedidos_status',
      'class_name' => 'PedidosStatus',
      'foreign_key' => 'loja_id',
      'primary_key' => 'loja_id'
    ],


    // [
    // 	'produtos_index',
    //     'class_name' => 'ProdutosViewsTemp',
    //     'foreign_key' => 'loja_id',
    // 	'primary_key' => 'loja_id',
    // 	'group' => 'codigo_id, id_cor',
    // 	'order' => 'rand(), estoque DESC',
    // 	'limit' => 12
    // ], [
    // 	'produtos_index_sale',
    //     'class_name' => 'ProdutosViewsTemp',
    //     'foreign_key' => 'loja_id',
    // 	'primary_key' => 'loja_id',
    // 	'conditions' => ['EXISTS(SELECT pr.id, count(pv.id_produto) FROM produtos pr INNER JOIN pedidos_vendas pv ON pv.id_produto=pr.id WHERE view_produtos_all.id = pr.id GROUP BY 1)'],
    // 	'group' => 'codigo_id, id_cor',
    // 	'order' => 'rand(), estoque DESC',
    // 	'limit' => 12,
    // ]
  ];
}
