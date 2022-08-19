<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of ProdutosRelacionados
 *
 * @author renan
 */
class ProdutosRelacionadosGrupos extends ActiveRecord
{
    static $table = 'produtos_relacionados_grupos';
    
    static $timestamp = false;
    
    static $before_save = array('in_store');
}
