<?php
/**
 * Abstract Class BaseEntity
 *
 * @author Fernando Campos de Oliveira <fernando@odesenvolvedor.net>
 * 
 * @package ODJuno\Entities;
 */

namespace ODJuno\Entities;

use ODJuno\Interfaces\ODJunoSerializable;

class BaseEntity implements ODJunoSerializable
{
    public function jsonSerialize()
    {
        $arr = get_object_vars($this);
        foreach ($arr as $k => $v) {
            if (!is_bool($v) && $v !== 0 && empty($v)) {
                unset($arr[$k]);
            }
        }
        return $arr;
    }
}