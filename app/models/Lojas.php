<?php

class Lojas extends ActiveRecord
{
  static $table = 'lojas';

  static $primary_key = 'dominio';

  static $after_save = ['in_store'];

  static $has_one = [
    [
      'plano',
      'class_name' => 'LojasPlanos',
      'primary_key' => 'lojas_planos_id',
      'foreign_key' => 'id',
    ]
  ];
  // , [
  //             'configuracoes',
  //             'class_name' => 'Configuracoes',
  //             'primary_key' => 'id',
  //             'foreign_key' => 'loja_id'
  //         ], [
  // 			'pagamentos',
  // 			'class_name' => 'ConfiguracoesPagamento',
  // 			'primary_key' => 'id',
  // 			'foreign_key' => 'loja_id',
  // 		], [
  // 			'fretes',
  //             'class_name' => 'ConfiguracoesFretesEnvios',
  //             'primary_key' => 'id',
  //             'foreign_key' => 'loja_id',
  // 		], [
  // 			'lojas',
  //             'class_name' => 'Lojas',
  //             'primary_key' => 'id',
  //             'foreign_key' => 'loja_id',
  //         ], [
  //             'correios',
  //             'class_name' => 'Correios',
  //             'primary_key' => 'id',
  //             'foreign_key' => 'loja_id',
  //             'include' => ['etiquetas', 'etiquetas_servicos']
  //         ], [
  //             'jadlog',
  //             'class_name' => 'JadLog',
  //             'primary_key' => 'id',
  //             'foreign_key' => 'loja_id'
  //         ]
  //     ];

  // 	// Infiltrar dados da store
  //     static $has_many = [ [
  //             'transferencias',
  //             'class_name' => 'ConfiguracoesTransferencia',
  //             'primary_key' => 'loja_id',
  //             'foreign_key' => 'loja_id',
  //         ], [
  // 			'paginas',
  //             'class_name' => 'ConfiguracoesPaginas',
  //             'primary_key' => 'id',
  //             'foreign_key' => 'loja_id',
  // 		], [
  // 			'Adm',
  // 			'class_name' => 'Adm',
  // 			'primary_key' => 'id',
  //             'foreign_key' => 'loja_id'
  // 		], [
  // 			'carrinho',
  //             'class_name' => 'Carrinho',
  //             'primary_key' => 'id',
  //             'foreign_key' => 'loja_id',
  // 		],
  // 		[
  //             'pgtos',
  //             'class_name' => 'LojasPgto',
  //             'primary_key' => 'id',
  //             'foreign_key' => 'loja_id',
  //             'order' => 'status asc, vencimento asc',
  //         ],
  //         // busca todos os produtos
  //         [
  //             'produtos',
  //             'class_name' => 'Produtos',
  //             'primary_key' => 'id',
  //             'foreign_key' => 'loja_id',
  //             'conditions' => array('status=? and excluir=?', 0, 0 )
  //         ],
  //         // busca todos os pedidos
  //         [
  //             'pedidos',
  //             'class_name' => 'Pedidos',
  //             'primary_key' => 'id',
  //             'foreign_key' => 'loja_id',
  // //            'conditions' => array( 'excluir=?', 0 )
  //         ]
  // 	];

  /**
   * Gerar um contantado de pagesViews para o sistema
   */
  public static function CountViews($modulo = '')
  {
    $MesAtual = date('Y-m-01 00:00:00');
    $MesFinal = date('Y-m-t 23:59:59');
    $Lojas = Lojas::find(ASSETS);

    /**
     * Desativa o sistema temporariamente a caso o contador estiver zerado
     */
    if ($Lojas->max_visualizacoes == '0') {
      exit;
    }
    /**
     * Percorre se está dentro do mes atual
     */
    if (self::count(['conditions' => ['updated_at BETWEEN ? and ? and dominio = ?', $MesAtual, $MesFinal, ASSETS]]) > 0) {
      $Lojas->max_visualizacoes = $Lojas->max_visualizacoes - 1;
    }
    /**
     * Gera um novo mes
     */
    else {
      $Lojas->max_visualizacoes = $Lojas->plano->visualizacoes;
    }
    /**
     * Define somentes as paginas
     */
    if ('identificacao' !== $modulo) {
      $Lojas->save();
    }
  }
}
