<?php

/**
 *
 */
class ClientesEnderecos extends ActiveRecord
{
  static $table = 'clientes_enderecos';

  static $before_save = ['in_store'];

  static $has_one = [[
    'cliente',
    'class_name' => 'Clientes',
    'foreign_key' => 'id',
    'primary_key', 'id_clientes',
  ]];

  static $validates_presence_of = [
    // [ 'cep', 'message' => 'text_required', 'allow_blank' => true ],
    // [ 'endereco', 'message' => 'text_required', 'allow_blank' => true ],
    // [ 'numero', 'message' => 'text_required', 'allow_blank' => true ],
    // [ 'bairro', 'message' => 'text_required', 'allow_blank' => true ],
    // // [ 'complemento', 'message' => 'text_required', 'allow_blank' => true ],
    // // [ 'referencia', 'message' => 'text_required', 'allow_blank' => true ],
    // [ 'cidade', 'message' => 'text_required', 'allow_blank' => true ],
    // [ 'uf', 'message' => 'text_required', 'allow_blank' => true ],
    // ['nome', 'message' => 'text_required', 'allow_blank' => true]
  ];

  static $validates_format_of = [
    // ['cep', 'with' => '/^[0-9]{5,5}([-]?[0-9]{3})$/', 'message' => 'text_required_cep', 'allow_blank' => true],
    // ['numero', 'with' => '/^([0-9]+)$/', 'message' => 'text_required_num', 'allow_blank' => true],
  ];

  static $validates_numericality_of = [
    ['id_cliente', 'greater_than' => 0, 'message' => 'Cliente não finalizou cadastro corretamente!']
  ];
}
