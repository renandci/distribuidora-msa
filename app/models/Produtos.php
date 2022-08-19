<?php

class Produtos extends ActiveRecord
{
  static $table = 'produtos';

  static $validates_presence_of = [];

  static $validates_numericality_of = [];

  static $before_save = ['in_store'];

  static $has_one = [
    // [
    // 	'blackfriday',
    // 	'class_name' => 'BlackFridayProdutos',
    // 	'foreign_key' => 'id_produtos',
    // 	'primary_key' => 'id_produto', // NOTA: O id pai esta no 'produto view'
    // ],
    [
      'promocoes',
      'class_name' => 'Promocoes',
      'foreign_key' => 'id_marca',
      'primary_key' => 'id_marca',
    ],
    [
      'marca',
      'class_name' => 'Marcas',
      'primary_key' => 'id_marca',
      'foreign_key' => 'id'
    ], [
      'cor',
      'class_name' => 'Cores',
      'primary_key' => 'id_cor',
      'foreign_key' => 'id',
      'conditions' => ['excluir=?', 0]
    ], [
      'tamanho',
      'class_name' => 'Tamanhos',
      'primary_key' => 'id_tamanho',
      'foreign_key' => 'id',
      'conditions' => ['excluir=?', 0]
    ], [
      'descricao',
      'class_name' => 'ProdutosDescricoes',
      'primary_key' => 'id_descricao',
      'foreign_key' => 'id'
    ], [
      'freteproduto',
      'class_name' => 'DadosFrete',
      'primary_key' => 'id_frete',
      'foreign_key' => 'id',
    ], [
      'capa',
      'class_name' => 'ProdutosImagens',
      'foreign_key' => ['codigo_id', 'cor_id'],
      'primary_key' => ['codigo_id', 'id_cor'],
      'order' => 'capa DESC',
      // 'conditions' => ['produtos_imagens.capa = ? ', 1],
    ], [
      'produto_relacionado',
      'class_name' => 'ProdutosRelacionados',
      'foreign_key' => ['produtos_id'],
    ], [
      'comentarios_media',
      'class_name' => 'ProdutosComentarios',
      'foreign_key' => 'id_produto',
      'primary_key' => 'id',
      'order' => 'id DESC',
      'select' => 'count(id) as total, round(avg(nota),0) as media',
      'conditions' => ['ativo = 1']
    ], [
      'nfe_ncm',
      'class_name' => 'NfeNcm',
      'foreign_key' => 'ncm',
      'primary_key' => 'ncm',
      'order' => 'id DESC'
    ]
  ];

  static $has_many = [
    [
      'fotos',
      'class_name' => 'ProdutosImagens',
      'primary_key' => ['codigo_id', 'id_cor'],
      'foreign_key' => ['codigo_id', 'cor_id'],
    ], [
      'grid_kits',
      'select' => '*, count(codigo_id_produtos) as qtde',
      'class_name' => 'ProdutosKits',
      'foreign_key' => 'codigo_id',
      'primary_key' => 'codigo_id',
      'group' => 'codigo_id_produtos'
    ], [
      'produtos_menus',
      'class_name' => 'ProdutosMenus',
      'foreign_key' => 'codigo_id',
      'primary_key' => 'codigo_id'
    ], [
      'prod_menu_subgrupos',
      'class_name' => 'ProdutosMenus',
      'foreign_key' => 'id_subgrupo',
      'primary_key' => 'id_subgrupo',
      'group' => 'codigo_id, id_grupo, id_subgrupo',
    ], [
      'produtos_all',
      'class_name' => 'Produtos',
      'primary_key' => 'codigo_id',
      'foreign_key' => 'codigo_id',
      'conditions' => ['status = 0 and excluir = 0'],
      'order' => 'id_cor desc, id_tamanho asc'
    ], [
      'produtos_abas',
      'class_name' => 'ProdutosDescricoesAbas',
      'primary_key' => 'codigo_id',
      'foreign_key' => 'codigo_id',
      'order' => 'ordem asc'
    ], [
      'comentarios',
      'class_name' => 'ProdutosComentarios',
      'foreign_key' => 'id_produto',
      'primary_key' => 'id',
      'order' => 'id DESC',
      'conditions' => ['ativo = 1']
    ], [
      'comentarios_rating',
      'class_name' => 'ProdutosComentarios',
      'foreign_key' => 'id_produto',
      'primary_key' => 'id',
      'select' => 'COUNT(*) AS total_rates, round(avg(nota),0) AS rating',
      'order' => 'nota DESC',
      'group' => 'nota',
      'conditions' => ['ativo = 1']
    ]
  ];

  static $delegate = [
    ['name', 'nomecor', 'to' => 'cor'],
    ['name', 'cor1', 'to' => 'cor'],
    ['name', 'cor3', 'to' => 'cor'],

    ['name', 'nometamanho', 'to' => 'tamanho'],

    ['name', 'id',          'to' => 'promocoes', 'prefix' => 'promo'],
    ['name', 'codigo_id',   'to' => 'promocoes', 'prefix' => 'promo'],
    ['name', 'setup_ini',   'to' => 'promocoes'],
    ['name', 'setup_fin',   'to' => 'promocoes'],
    ['name', 'setup_type',  'to' => 'promocoes'],
    ['name', 'setup_value', 'to' => 'promocoes'],
    ['name', 'setup_text',  'to' => 'promocoes'],
    ['name', 'setup_color', 'to' => 'promocoes'],
    ['name', 'setup_hex',   'to' => 'promocoes'],
  ];

  public function get_new_cod()
  {
    return CodProduto($this->read_attribute('nome_produto'), $this->read_attribute('id'), $this->read_attribute('codigo_produto'));
  }

  public function get_preco_lucro()
  {
    $preco_custo = $this->read_attribute('preco_custo');

    $preco_promo = $this->read_attribute('preco_promo');

    $preco = $preco_custo ?? 0;

    return $preco > 0 ? (($preco_promo * 100 / $preco) - 100) : 0;
  }
}
