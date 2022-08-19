<?php
class ActiveRecord extends ActiveRecord\Model
{

  // static $cache = true;

  /**
   * Determines if an attribute exists for this {@link Model}.
   *
   * @param string $attribute_name
   * @return boolean
   */
  public function __isset($attribute_name)
  {
    if (parent::__isset($attribute_name))
      return true;

    // check for getters
    if (method_exists($this, "get_${attribute_name}"))
      return true;

    // check for relationships
    if (static::table()->has_relationship($attribute_name))
      return true;

    return false;
  }

  protected static function global_store($params = null)
  {
    global $CONFIG;
    return !empty($CONFIG[$params]) ? $CONFIG[$params] : null;
  }

  /**
   * Gerar um update no banco com o id da loja
   * @global type $CONFIG
   */
  public function in_store()
  {
    try {
      $this->loja_id = $this->global_store('loja_id');
    } catch (Exception $e) {
    }
  }

  public static function moeda($get_valor)
  {
    $source = ['.', ','];
    $replace = ['', '.'];
    $valor = str_replace($source, $replace, $get_valor); //remove os pontos e substitui a virgula pelo ponto
    return $valor; //retorna o valor formatado para gravar no banco
  }

  public static function test_float($test)
  {

    if (!is_scalar($test)) {
      return false;
    }

    $type = gettype($test);

    if ($type === "float") {
      return true;
    } else {
      return preg_match("/^\\d+\\.\\d+$/", $test) === 1;
    }
  }

  public function get_placastatus()
  {

    $timestamp  = $this->global_store('timestamp');
    $atacadista = self::global_store('atacadista');

    $id_marca  = $this->read_attribute('id_marca');
    $codigo_a  = $this->read_attribute('codigo_id');

    $Promocoes = Promocoes::first(['conditions' => ['id_marca=? or codigo_id=?', $id_marca, $codigo_a]]);

    $id  = $Promocoes->id;

    $codigo_b  = $Promocoes->codigo_id;

    $setup_ini  = $Promocoes->setup_ini;
    $setup_ini  = !empty($setup_ini) ? strtotime($setup_ini->format('Y-m-d H:i:s')) : null;

    $setup_fin  = $Promocoes->setup_fin;
    // Define um rack para ser infinito, se a caso não for setado uma data final
    $setup_fin  = !empty($setup_fin) ? strtotime($setup_fin->format('Y-m-d H:i:s')) : $timestamp + 1;

    $setup_hex = $Promocoes->setup_hex;
    $setup_color = $Promocoes->setup_color;
    $setup_text = $Promocoes->setup_text;

    $ini = ($setup_ini <= $timestamp);
    $fin = ($setup_fin >= $timestamp);
    $uni = ($codigo_a == $codigo_b && ($codigo_b > 0));

    $placastatus = array();

    if ($id > 0 && ($ini && (($ini && $fin) || $uni)))
      $placastatus[0] = sprintf('<span style="background-color:#%s; color:#%s">%s', $setup_hex, $setup_color, $setup_text);

    $read_placastatus = $this->read_attribute('placastatus');

    if (!empty($read_placastatus)) {
      foreach (PlaquinhaStatus::all(['order' => 'ordem asc', 'conditions' => ['excluir = 0 and id in(?)', explode(',', $read_placastatus)]]) as $pla) {
        $placastatus[$pla->id] = sprintf(
          '<span placa-id="%u" style="background-color:#%s; color:#%s">%s',
          $pla->id,
          $pla->placa_background,
          $pla->placa_color,
          $pla->placa_text
        );
      }
    }

    return implode(',', $placastatus);
  }

  public function get_preco_venda()
  {
    $timestamp  = $this->global_store('timestamp');
    $atacadista = self::global_store('atacadista');

    $id_marca  = $this->read_attribute('id_marca');
    $codigo_a  = $this->read_attribute('codigo_id');

    $Promocoes = Promocoes::first(['conditions' => ['id_marca=? or codigo_id=?', $id_marca, $codigo_a]]);

    $codigo_b  = $Promocoes->codigo_id;
    $setup_ini  = $Promocoes->setup_ini;
    $setup_ini  = !empty($setup_ini) ? strtotime($setup_ini->format('Y-m-d H:i:s')) : null;

    $setup_fin  = $Promocoes->setup_fin;
    // Define um rack para ser infinito, se a caso não for setado uma data final
    $setup_fin  = !empty($setup_fin) ? strtotime($setup_fin->format('Y-m-d H:i:s')) : $timestamp + 1;

    $setup_type = $Promocoes->setup_type;
    $setup_value = $Promocoes->setup_value;
    $preco_venda = $this->read_attribute('preco_venda');

    $ini = ($setup_ini <= $timestamp);
    $fin = ($setup_fin >= $timestamp);
    $uni = ($codigo_a == $codigo_b && ($codigo_b > 0));


    // retorna sempre como porcetagem
    if (!empty($atacadista)) {
      $preco_venda = ($preco_venda - ($atacadista / 100) * $preco_venda);
    }
    // retorna sempre como porcetagem
    elseif (!empty($setup_ini) && $setup_type == '%' && ($ini && (($ini && $fin) || $uni))) {
      $preco_venda = ($preco_venda - ($setup_value / 100) * $preco_venda);
    }
    // retorna sempre como real
    elseif (!empty($setup_ini) && $setup_type != '$' && ($ini && (($ini && $fin) || $uni))) {
      $preco_venda = ($preco_venda - ($setup_value));
    }
    return $preco_venda;
  }

  public function get_preco_promo()
  {
    $timestamp  = $this->global_store('timestamp');
    $atacadista = self::global_store('atacadista');

    $id_marca  = $this->read_attribute('id_marca');
    $codigo_a  = $this->read_attribute('codigo_id');

    $Promocoes = Promocoes::first(['conditions' => ['id_marca=? or codigo_id=?', $id_marca, $codigo_a]]);

    $codigo_b  = $Promocoes->codigo_id;
    $setup_ini  = $Promocoes->setup_ini;
    $setup_ini  = !empty($setup_ini) ? strtotime($setup_ini->format('Y-m-d H:i:s')) : null;

    $setup_fin  = $Promocoes->setup_fin;
    // Define um rack para ser infinito, se a caso não for setado uma data final
    $setup_fin  = !empty($setup_fin) ? strtotime($setup_fin->format('Y-m-d H:i:s')) : $timestamp + 1;

    $setup_type = $Promocoes->setup_type;
    $setup_value = $Promocoes->setup_value;
    $preco_promo = $this->read_attribute('preco_promo');

    $ini = ($setup_ini <= $timestamp);
    $fin = ($setup_fin >= $timestamp);
    $uni = ($codigo_a == $codigo_b && ($codigo_b > 0));

    // retorna sempre como porcetagem
    if (!empty($atacadista)) {
      $preco_promo = ($preco_promo - ($atacadista / 100) * $preco_promo);
    }
    // retorna sempre como porcetagem
    elseif (!empty($setup_ini) && $setup_type == '%' && ($ini && (($ini && $fin) || $uni))) {
      $preco_promo = ($preco_promo - ($setup_value / 100) * $preco_promo);
    }
    // retorna sempre como real
    elseif (!empty($setup_ini) && $setup_type != '$' && ($ini && (($ini && $fin) || $uni))) {
      $preco_promo = ($preco_promo - ($setup_value));
    }
    return $preco_promo;
  }

  public function get_valorcompra()
  {
    $timestamp  = $this->global_store('timestamp');
    $atacadista = self::global_store('atacadista');

    $id_marca  = $this->read_attribute('id_marca');
    $codigo_a  = $this->read_attribute('codigo_id');

    $Promocoes = Promocoes::first(['conditions' => ['id_marca=? or codigo_id=?', $id_marca, $codigo_a]]);

    $codigo_b  = $Promocoes->codigo_id;
    $setup_ini  = $Promocoes->setup_ini;
    $setup_ini  = !empty($setup_ini) ? strtotime($setup_ini->format('Y-m-d H:i:s')) : null;

    $setup_fin  = $Promocoes->setup_fin;
    // Define um rack para ser infinito, se a caso não for setado uma data final
    $setup_fin  = !empty($setup_fin) ? strtotime($setup_fin->format('Y-m-d H:i:s')) : $timestamp + 1;

    $setup_type = $Promocoes->setup_type;
    $setup_value = $Promocoes->setup_value;
    $valorcompra = $this->read_attribute('valorcompra');

    $ini = ($setup_ini <= $timestamp);
    $fin = ($setup_fin >= $timestamp);
    $uni = ($codigo_a != $codigo_b && ($codigo_b > 0));

    // retorna sempre como porcetagem
    if (!empty($atacadista)) {
      $valorcompra = ($valorcompra - ($atacadista / 100) * $valorcompra);
    }
    // retorna sempre como porcetagem
    elseif (!empty($setup_ini) && $setup_type == '%' && ($ini && ($ini && $fin) && $uni)) {
      $valorcompra = ($valorcompra - ($setup_value / 100) * $valorcompra);
    }
    // retorna sempre como real
    elseif (!empty($setup_ini) && $setup_type != '$' && ($ini && ($ini && $fin) && $uni)) {
      $valorcompra = ($valorcompra - ($setup_value));
    }
    return $valorcompra;
  }

  /**
   *
   * @param type $sql
   * @param type $include
   * @return type
   */
  public static function find_by_sql_including($sql, $include = null)
  {
    return static::table()->find_by_sql($sql, null, true, $include);
  }

  /**
   *
   * @param type $sql
   * @param type $values
   * @param type $readonly
   * @param type $includes
   * @return type
   */
  public static function find_by_sql_custom($sql, $values = null, $readonly = false, $includes = null)
  {
    return static::table()->find_by_sql($sql, $values, $readonly, $includes);
  }

  /**
   * Contador de Registros simples via find_by_sql
   * @param type $sql
   * @param type $values
   * @param type $readonly
   * @param type $includes
   * @return type
   */
  public static function find_num_rows($sql, $values = null)
  {
    return count(static::table()->find_by_sql($sql, $values, true));
  }

  /**
   * Verifica se os nomes das tabelas existe||model
   * @param type $s
   * @return type
   */
  public static function action_tables($s = '')
  {
    return !in_array($s, static::connection()->tables());
  }

  public static function array_merge_recursive_ex($a, $b)
  {
    if (empty($a) && empty($b)) return false;
    foreach ($b as $k => $v) {
      if (array_key_exists($k, $a) && is_array($v)) {
        $a[$k] = self::array_merge_recursive_ex($a[$k], $b[$k]);
      } else {
        $a[$k] = $v;
      }
    }
    return $a;
  }

  /**
   * Criar as ações de cadastro edição e excluisão
   * @param Array Dados em vetor ['Table' => [ id => [ 'campo' => 'valor' ] ] ]<br/>
   * Deve conter a seguinte formação de dados acima
   * @param Mode O mode dever ser alterar|excluir|status|cadastrar|delete<br/>
   * <b>Nota:</b> O comando delete irá remover os dados do banco de dados
   * @param Attributo O nome do campo para gerar um nome para a remoção por exemplo
   * @return boolean Se tudo ocorrer bem, retorna a pagina anterior
   */
  public static function action_cadastrar_editar($array = null, $mode = null, $attr = null)
  {
    foreach ($array as $table => $fields) {
      foreach ($fields as $id => $array1) {
        $array1['id'] = $id;

        if ($mode == 'excluir')
          return $table::new_save($array1);

        if ($mode == 'delete')
          return $table::delete_log($array1);

        return $table::new_save($array1);
      }
    }
  }

  public function save_log(array $params = null)
  {

    if (empty($params))
      if (!($params = $this->dirty_attributes()))
        $params = $this->attributes;

    if (($pk = $this->values_for_pk()))
      if (!empty($pk['id']))
        $params['id'] = $pk['id'];

    return self::new_save($params);
  }

  public static function new_save(array $params = null)
  {
    $return = [];
    $array_before = [];
    $array_after = [];

    $getClass = get_called_class();

    $Class = new $getClass();

    if (isset($params['id']) && $params['id'] > 0) {
      unset($Class);
      $Class = $getClass::find((int)$params['id']);
    }

    foreach ($params as $name => $values) {
      try {
        $array_before[$name] = $Class->{$name};

        $array_after[$name] = html_entity_decode(stripslashes($values), ENT_QUOTES, 'UTF-8');

        // if (static::test_float(static::moeda($values)))
        if (static::test_float(static::moeda($values)) && $name != 'placastatus')
          $Class->{$name} = static::moeda($values);
        else
          $Class->{$name} = html_entity_decode(stripslashes($values), ENT_QUOTES, 'UTF-8');
      } catch (Exception $e) {
      }
    }

    $Class->save();

    if (!$Class->is_valid()) {
      foreach ($Class->errors->get_raw_errors() as $column_name => $error) {
        $return[($column_name === 'email_and_loja_id' ? 'email' : $column_name)] = current($error);
      }
    } else {
      $return['id'] = $Class->id;
      $return[static::$table . '_success'] = 'Salvo com sucesso!';
      Logs::my_logs($array_before, $array_after, (int)(!empty($_SESSION['admin']['id_usuario']) ? $_SESSION['admin']['id_usuario'] : 0), static::$table);
    }

    return $return;
  }

  public static function delete_log(array $params = null)
  {
    if (empty($params['id']) && $params['id'] == 0) return;

    $getClass = get_called_class();
    $Class = $getClass::find((int)$params['id']);
    $return['id'] = $Class->id;
    $Class->delete();

    $return[static::$table . '_removido'] = 'Removido com sucesso!';

    Logs::my_logs(['id' => $return['id']], ['id' => 'ID ' . $return['id'] . ' - removido com sucesso'], (int)$_SESSION['admin']['id_usuario'], static::$table);

    return $params;
  }
}

// $name = 'arquivo.txt';
// $file = fopen($name, 'a');
// $text = @var_export( $init, true);
// fwrite($file, $text);
// fclose($file);
