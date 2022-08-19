<?php

namespace ODJuno\Services;

use ODJuno\Request\Base;
use ODJuno\Response\Response;

class BaseService
{
    /**
     * @var Base
     */
    protected $client;
    /**
     * @var Response
     */
    protected $response;

    public function __construct(Base $client, Response $response = null)
    {
        $this->client = $client;

        $this->response = empty($response) ? new Response() : $response;

    }
}