<?php

class LojasQuestionario extends ActiveRecord
{
  static $table = 'lojas_questionario';

  static $before_save = ['in_store'];

  static $has_many = [
    [
      'questaoopcao',
      'class_name' => 'LojasQuestionario',
      'foreign_key' => 'parent_id',
      'primary_key' => 'id',
    ]
  ];

  public static function getOrdem()
  {
    $LojasQuestionario = LojasQuestionario::first(['order' => 'id DESC']);
    return $LojasQuestionario->input_ordem + 1;
  }

  /**
   * Buscar array
   * @param $arr Entrada de array
   * @param $a id da busca
   * @param $b campo para a busca
   *
   */
  public static function custom_array_search($arr, $a, $b, $type = 'checked')
  {

    $r = array();
    foreach ($arr as $test) {
      if ($test[$b] === $a) {
        $r = $test;
      }
    }

    return $r;
  }
}
