<?php

namespace ODJuno\Interfaces;

/**
 * Interface ODJunoInterface
 *
 * @author Fernando Campos de Oliveira <fernando@odesenvolvedor.net>
 * 
 * @package ODJuno\Interfaces;
 */
interface ODJunoInterface
{

    /**
     * @param $json
     *
     * @return \stdClass
     */
    public static function fromJson($json);

    /**
     * @param \stdClass $data
     */
    public function populate(\stdClass $data);
}