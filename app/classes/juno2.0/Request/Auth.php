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

class Auth extends Base
{
    const PRODUCTION_ENDPOINT   = "https://api.juno.com.br/authorization-server/";
    const SANDBOX_ENDPOINT      = "https://sandbox.boletobancario.com/authorization-server/";

    public function __construct($base64EncodedCredentials, $sandbox = true)
    {
        $this->client = new GuzzleClient([
            'base_uri' => $sandbox ? self::SANDBOX_ENDPOINT : self::PRODUCTION_ENDPOINT,
            'headers' => [
                'Authorization' => sprintf( 'Basic %s', $base64EncodedCredentials ),
                'Accept' => 'application/json'
            ]
        ]);
    }
}