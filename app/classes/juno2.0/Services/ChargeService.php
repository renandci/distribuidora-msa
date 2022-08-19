<?php

namespace ODJuno\Services;

use ODJuno\Entities\Charge;
use ODJuno\Entities\Split;
use ODJuno\Request\Client;
use ODJuno\Response\Response;

class ChargeService extends BaseService
{ 
    public function create(Charge $params): ?Response
    {
        $response = $this->client->post('charges', [
            'json' => $params
        ]);
        return $this->response->fromJson($response);
    }

    public function splitUpdate(Split $params): ?Response
    {
        $response = $this->client->put("charges/{$id}/split", [
            'json' => $params
        ]);

        return $this->response->fromJson($response);
    }

    public function list($filters = []): ?Response
    {
        $response = $this->client->get('charges', [
            'query' => $filters
        ]);

        return $this->response->fromJson($response);
    }

    public function show($id): ?Response
    {
        $response = $this->client->get("charges/{$id}");

        return $this->response->fromJson($response);
    }

    public function cancel($id): ?Response
    {
        $response = $this->client->put("charges/{$id}/cancelation");

        return $this->response->fromJson($response);
    }

}