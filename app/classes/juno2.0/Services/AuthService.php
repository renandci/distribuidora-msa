<?php

namespace ODJuno\Services;

use ODJuno\Response\AuthResponse;
use ODJuno\Response\Response;
use ODJuno\Entities\BaseEntity;

class AuthService extends BaseService
{
 
    public function authenticate(): ?Response
    {
        $authResponse = new AuthResponse();

        $response = $this->client->post('oauth/token', [
            'form_params' => ['grant_type' => 'client_credentials']
        ]);
 
        return $authResponse->fromJson($response);
    }
}