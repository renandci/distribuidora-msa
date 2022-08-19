<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of DadosFrete
 *
 * @author renan
 */
class ProdutosDescricoes extends ActiveRecord
{
  static $table = 'produtos_descricoes';

  static $timestamp = false;

  static $before_save = array('in_store');
}
