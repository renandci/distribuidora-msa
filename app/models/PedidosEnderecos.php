<?php

class PedidosEnderecos extends ActiveRecord
{
  static $table = 'pedidos_enderecos';

  static $before_save = ['in_store'];

  static $timestamp = false;

  static $has_one = [
    [
      'cod_ibge',
      'class_name' => 'NfeCidades',
      'primary_key' => 'id_cidade',
      'foreign_key' => 'id',
    ]
  ];


  /**
   * Criar os enderecos para o sistema
   * @param type $id_pedido
   * @param type $id_cliente
   * @param type $nome
   * @param type $endereco
   * @param type $nr
   * @param type $bairro
   * @param type $comp
   * @param type $refer
   * @param type $cidade
   * @param type $uf
   * @param type $cep
   * @return string Id do novo endereco
   */
  public static function gerarEnderecos($id_pedido = 0, $id_cliente = 0, $nome = '', $endereco = '', $nr = '', $bairro = '', $comp = '', $refer = '', $cidade = '', $uf = '', $cep = '')
  {
    $var = new PedidosEnderecos();
    $var->id_pedido = $id_pedido;
    $var->id_cliente = $id_cliente;
    $var->nome = $nome;
    $var->endereco = $endereco;
    $var->numero = $nr;
    $var->bairro = $bairro;
    $var->complemento = $comp;
    $var->referencia = $refer;
    $var->cidade = $cidade;
    $var->uf = $uf;
    $var->cep = $cep;

    if (!$var->save()) {
      throw new Exception('Não foi possivel salvar seu endereço!');
    }

    return $var->id;
  }
}
