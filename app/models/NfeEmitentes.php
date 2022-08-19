<?php

class NfeEmitentes extends ActiveRecord
{
  static $table = 'nfe_emitentes';

  static $before_save = ['in_store'];

  static $validates_presence_of = [];

  static $has_one = [
    [
      'nfe_nr_last',
      'class_name' => 'NfeNotas',
      'foreign_key' => 'id_emitentes',
      'primary_key' => 'id',
      'order' => 'id desc',
      'conditions' => ['status = 1']
    ]
  ];

  static $has_many = [
    [
      'notas',
      'class_name' => 'NfeNotas',
      'foreign_key' => 'id_emitentes',
      'primary_key' => 'id',
    ], [
      'notas_mensal',
      'class_name' => 'NfeNotas',
      'foreign_key' => 'id_emitentes',
      'primary_key' => 'id',
      // pega somente os dados para o mes atual
      'conditions' => ['year(created_at) = year(now()) and month(created_at) = month(now()) - 1'],
      'order' => 'year(created_at) desc, month(created_at) desc',
    ]
  ];

  public function jsonnfe()
  {
    return json_encode([
      'tpAmb'     => (int)$this->read_attribute('tpamb'), // 1 - producao | 2 - homacao
      'atualizacao'   => date('Y-m-d h:i:s'),
      'razaosocial'   => $this->read_attribute('razaosocial'),
      'cnpj'       => soNumero($this->read_attribute('cnpj')), // PRECISA SER VÁLIDO
      'ie'       => soNumero($this->read_attribute('inscest')), // PRECISA SER VÁLIDO
      'siglaUF'     => $this->read_attribute('uf'),
      'schemes'     => $this->read_attribute('schemes'),
      'versao'     => $this->read_attribute('versao'),
      'tokenIBPT'   => $this->read_attribute('tokenibpt'),
      'CSC'       => $this->read_attribute('csc'),
      'CSCid'     => $this->read_attribute('cscid')
    ]);
  }
}
