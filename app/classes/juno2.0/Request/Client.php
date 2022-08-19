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

class Client extends Base
{
    const PRODUCTION_ENDPOINT   = "https://api.juno.com.br/";
    const SANDBOX_ENDPOINT      = "https://sandbox.boletobancario.com/api-integration/";

    public function __construct($accessToken, $privateToken, $sandbox = true)
    {
        $this->client = new GuzzleClient([
            'base_uri' => !$sandbox ? self::SANDBOX_ENDPOINT : self::PRODUCTION_ENDPOINT,
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $accessToken),
                'X-Api-Version' => 2,
                'X-Resource-Token' => $privateToken,
                'Accept' => 'application/json'
            ]
        ]);
    }
}