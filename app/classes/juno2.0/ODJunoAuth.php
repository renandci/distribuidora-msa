<?php
/**
 *  
 * @author Fernando Campos de Oliveira <fernando@odesenvolvedor.net>
 *
 * @package ODJuno; 
 */
namespace ODJuno;

use ODJuno\Exception\ODJunoException;
use ODJuno\Request\Auth;
use ODJuno\Services\AuthService;

class ODJunoAuth {

    /**
     * @var Client
     */
    protected $client;

    public function __construct($config, $sandbox = true)
    {
        $this->client = new Auth($this->getBase64EncodedCredentials($config), $sandbox);
    }


    public function authService(): AuthService
    {
        return new AuthService($this->client);
    }

    private function getBase64EncodedCredentials($config)
    {
        if (!isset($config['clientId']) || empty($config['clientId'])) {
            throw new ODJunoException('Provide your client id');
        }
        if (!isset($config['secret']) || empty($config['secret'])) {
            throw new ODJunoException('Provide your secret');
        }
        return base64_encode(sprintf('%s:%s', $config['clientId'], $config['secret']));
    }

}
