<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of ClientesIndicacoes
 *
 * @author renan
 */
class ClientesIndicacoes extends ActiveRecord
{
  //put your code here
  static $table = 'clientes_indicacoes';

  static $timestamp = false;

  static $before_save = array('in_store');
}
