<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of ClientesLogs
 *
 * @author renan
 */
class ClientesLogs extends ActiveRecord
{
  static $table = 'clientes_logs';

  static $before_save = ['in_store'];
}
