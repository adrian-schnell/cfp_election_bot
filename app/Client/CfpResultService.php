<?php

namespace App\Client;

use GuzzleHttp\Client;

class CfpResultService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.dfx.swiss/v1/statistic/cfp/',
        ]);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllCurrentCfp(): array
    {
        $response = $this->client->get('latest');

        return json_decode($response->getBody()->getContents(), true);
    }
}
