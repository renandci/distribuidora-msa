<?php

class Clientes extends ActiveRecord
{
  static $table = 'clientes';

  var $tipopessoa = false;

  /**
   * Senha no model in mode false
   * @var type boolean
   */
  var $senha_real = false;

  /**
   * Senha de confirmcação no model in mode false
   * @var type
   */
  var $senha_confirm = false;

  /**
   * Ações logo apos o update quando há alteraçoes
   * @var type null
   */
  static $before_update = ['in_created'];

  /**
   * Ações logo apos o model ser salvo
   * @var type null
   */
  static $before_save = ['in_updated', 'in_store'];

  static $has_one = [
    [
      'endereco',
      'class_name' => 'ClientesEnderecos',
      'foreign_key' => 'id_cliente',
      'conditions' => ['status=?', 'ativo']
    ], [
      'indicacao',
      'class_name' => 'ClientesIndicacoes',
      'foreign_key' => 'id_cliente'
    ]
  ];

  static $has_many = [
    [
      'enderecos',
      'class_name' => 'ClientesEnderecos',
      'foreign_key' => 'id_cliente'
    ], [
      'pedidos',
      'class_name' => 'Pedidos',
      'foreign_key' => 'id_cliente',
      'order' => 'id desc'
    ]
  ];

  static $validates_presence_of = [
    ['email', 'message' => 'text_required'],
    // [ 'nome', 'message' => 'text_required', 'allow_blank' => true ],
    // [ 'cpfcnpj', 'message' => 'text_required', 'allow_blank' => true ],
    // [ 'telefone', 'message' => 'text_required', 'allow_blank' => true ],
    // [ 'celular', 'message' => 'text_required', 'allow_blank' => true ],
    // [ 'operadora', 'message' => 'text_required', 'allow_blank' => true ],
    // [ 'cidade', 'message' => 'text_required', 'allow_blank' => true ],
    // [ 'uf', 'message' => 'text_required', 'allow_blank' => true ],
    // validação in false
    ['senha_real', 'message' => 'text_required', 'allow_null' => true],
    ['senha_confirm', 'message' => 'text_required', 'allow_null' => true],
  ];


  /**
   * Validação dos dados do model cliente
   * Podendo haver não a necessidade de ter o campo preenchido
   * @var type boolean
   */
  static $validates_size_of = [
    ['senha_real', 'within' => [4, 12], 'message' => 'text_required_strlen', 'allow_null' => true],
    ['senha_confirm', 'within' => [4, 12], 'message' => 'text_required_strlen', 'allow_null' => true],
  ];

  // validação de cadastrado sendo somente um e-mail de cadastrado por usuario
  static $validates_uniqueness_of = [
    // [ 'email', 'message' => 'text_required_unic' ],
    [['email', 'loja_id'], 'message' => 'text_required_unic'],
  ];

  // validacao do email
  static $validates_format_of = [
    ['email', 'with' => '/^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+.([a-zA-Z]{2,4})$/', 'message' => 'text_required_not'],
    // ['nome', 'with' => '/[A-zÀ-ú\']{2,}\s[A-zÀ-ú\']{1,}\'?-?[A-zÀ-ú\']{2,}\s?([A-zÀ-ú\']{1,})?/', 'message' => 'text_required'],
    // ['telefone', 'with' => '/^(\(?\d{2}\)?) ?9?\d{4}-?\d{4}$/', 'message' => 'text_required_tel_invalid'],
    // ['cpfcnpj', 'with' => '/^([0-9]{3}\.?[0-9]{3}\.?[0-9]{3}\-?[0-9]{2}|[0-9]{2}\.?[0-9]{3}\.?[0-9]{3}\/?[0-9]{4}\-?[0-9]{2})$/', 'message' => 'text_required_cpfcnpj_not'],
    // ['celular', 'with' => '/^(\(?\d{2}\)?) ?9?\d{4}-?\d{4}$/', 'message' => 'text_required_cel_invalid'],
  ];

  /**
   * Validação com padrao de personalizacao
   */
  public function validate()
  {

    // Valida se o cpf ou cnpj é válido
    if (isset($this->cpfcnpj) && (new ValidaCPFCNPJ($this->cpfcnpj))->valida() == false) {
      $this->errors->add('cpfcnpj', 'text_required_cpfcnpj');
    }

    // verificar se as senha falsas existem e se não são identicas
    if (isset($this->senha_real, $this->senha_confirm) && ($this->senha_real != $this->senha_confirm || $this->senha_confirm != $this->senha_real)) {
      $this->errors->add('senha_real', 'text_required_identic_a');
      $this->errors->add('senha_confirm', 'text_required_identic_b');
    }
  }

  /**
   * Adiciona data de inclusao de cadastro
   */
  public function in_updated()
  {
    if (empty($this->data_cadastro)) :
      $this->data_cadastro = date('Y-m-d H:i:s');
    endif;
  }

  /**
   * Adiciona data de alteracao de cadastro
   */
  public function in_created()
  {
    $this->data_alteracao = date('Y-m-d H:i:s');
  }

  /**
   * Retorna um cliente logado no sistema
   */
  public static function getClientesSession($session = '')
  {
    return !empty($session) ? static::first(
      [
        ''
          . 'select' => ''
          . 'clientes.id as cliente_id, '
          . 'clientes.nome, '
          . 'clientes.email, '
          . 'clientes.cpfcnpj, '
          . 'clientes.rg, '
          . 'clientes.data_nascimento, '
          . 'clientes.sexo, '
          . 'clientes.telefone, '
          . 'clientes.celular, '
          . 'clientes.operadora, '
          . 'clientes.cidade, '
          . 'clientes.uf, '
          . 'clientes.ip ',
        'conditions' => ['md5(id)=?', $session]
      ]
    ) : false;
  }

  // public static function my_save( $params = [] ) {

  // 	$errors = [];

  // 	$getClass = static::class;

  // 	$Class = new $getClass();

  // 	if( isset( $params['id'] ) && $params['id'] > 0 ) {
  // 		unset($Class);
  // 		$Class = $getClass::find( (INT)$params['id'] );
  // 	}

  // 	foreach( $params as $name => $values ) {
  // 		try { $Class->{$name} = $values; } catch (Exception $e) {  }
  // 	}

  // 	$Class->save();

  // 	if( ! $Class->is_valid() ) {
  // 		foreach ( $Class->errors->get_raw_errors() as $column_name => $error ) {
  // 			$errors[ ($column_name === 'email_and_loja_id' ? 'email' : $column_name) ] = current( $error );
  // 		}
  // 	}
  // 	else {
  // 		$errors['id'] = $Class->id;
  // 		$errors[ strtolower( get_class($this) ) . '_success'] = 'Salvo com sucesso!';
  // 	}

  // 	return $errors;
  // }

}
