<?php

class Logs extends ActiveRecord
{
  static $table = 'logs';

  static $before_save = ['in_store'];

  static $has_one = [
    [
      'user',
      'class_name' => 'Adm',
      'primary_key' => 'adm_id',
      'foreign_key' => 'id'
    ], [
      'logstables',
      'class_name' => 'Logs',
      'primary_key' => 'id',
      'foreign_key' => 'id',
      // 'group' => 'tabela'
    ]
  ];

  static $has_many = [];

  /**
   * Criar log de usuario para administrativo
   * @param array_before vetor com os dados anterior
   * @param array_after vetor com os dados agora
   * @param user usuario administrativo do sistema logado
   */
  public static function my_logs($array_before = [], $array_after = [], $user = null, $table = null, $acao = 'select')
  {
    $count = 0;
    $count_id = 0;
    $loja_id = self::global_store('loja_id');

    $tabela = isset($table) ? $table : null;

    // Nome da table
    $text = 'Tabela de alteração: ' . strtoupper(isset($table) && $table != '' ? $table : '') . PHP_EOL;
    // $text = '';
    // Se usuario for uma string
    $text .= (isset($user) && (is_string($user) && $user != '') ? "Usuario: $user" . PHP_EOL : null);

    $arrays = array_intersect_key($array_before, $array_after);

    if ((is_array($arrays) ? count($arrays) : 0) > 0) {
      foreach ($array_before as $k => $v) {
        if (!empty($array_after[$k]) && $array_after[$k] != '') {
          if ($array_before[$k] != $array_after[$k]) {
            $text .= sprintf('Campo "%s" alterado de "%s" para "%s"', $k, $array_before[$k], $array_after[$k]) . PHP_EOL;
            $acao = 'update';
            $count++;
          }
        } else {
          if (!empty($array_after[$k]) && $array_after[$k] != '') {
            $text .= sprintf('Campo "%s" inserido "%s"', $k, $array_after[$k]) . PHP_EOL;
            $acao = 'insert';
            $count++;
          }
        }
        $text .= $k == 'id' ? (!empty($array_before[$k]) ? 'Registro de alteração: ' . $array_before[$k] : '') . PHP_EOL : null;
        $count_id = $k == 'id' ? $array_before[$k] : null;

        if ($k == 'excluir' && $array_before[$k] == 1) {
          $text .= sprintf('Campo "%s" excluido "%s"', $k, $array_after[$k]) . PHP_EOL;
          $acao = 'delete';
          $count++;
        }
      }
    }

    // Salva a ação de logs
    if ($count == 0) {
      // $text = 'Tabela de alteração: ' . strtoupper(isset($table) && $table != '' ? $table : '') . PHP_EOL;
      $text = 'Não houve mudança de registro: ' . $count_id;
    }

    self::create([
      'log' => $text,
      'acao' => $acao,
      'tabela' => $table,
      'ip' => retornaIpReal(),
      'loja_id' => $loja_id,
      'adm_id' => (int)$user
    ]);
  }

  /**
   * Criar log de usuario para administrativo
   * @param string Descricao do Log a ser gerado
   * @param int Usuario administrativo do sistema logado
   */
  public static function create_logs($logs = '', $user = 0, $acao = 'select', $tabela = 'log')
  {
    $loja_id = self::global_store('loja_id');
    self::create([
      'log' => $logs,
      'acao' => $acao,
      'tabela' => $tabela,
      'ip' => retornaIpReal(),
      'loja_id' => $loja_id,
      'adm_id' => (int)$user,
    ]);
  }
}
