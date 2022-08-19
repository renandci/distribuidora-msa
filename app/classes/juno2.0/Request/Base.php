<?php

namespace ODJuno\Request;

/**
 * Class Client
 *
 * @author Fernando Campos de Oliveira <fernando@odesenvolvedor.net>
 * 
 * @package ODJuno\Request;
 */

use GuzzleHttp\Client as GuzzleClient;

class Base
{
    const PRODUCTION_ENDPOINT   = "https://api.juno.com.br/";
    const SANDBOX_ENDPOINT      = "https://sandbox.boletobancario.com/";

    protected $client;

    public function __construct()
    {
    }

    public function __call($method, $args)
    {
        if (count($args) < 1) {
            throw new \InvalidArgumentException(
                'Magic request methods require a URI and optional options array'
            );
        }

        $uri = $args[0];

        $options = $args[1] ?? [];

        return $this->request($method, $uri, $options);
    }

    public function request($method, $uri, $options)
    {
        $method = strtoupper($method);

        $response = $this->client->request($method, $uri, $options);

        return $response->getBody();
    }
}