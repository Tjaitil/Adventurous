<?php

namespace App\tests\support;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

trait RequestTrait
{
    public Response $response;
    protected ?Client $client;

    protected function configureClient()
    {
        if (!isset($this->client)) {
            $this->client = new Client([
                'base_uri' => 'http://localhost:8888',
                'cookies' => true
            ]);
        }
    }

    protected function callMethod(string $method, string $url, array $data = null)
    {
        $this->configureClient();
        try {
            return $this->response = $this->client->{$method}($url, ['form_params' => $data, 'http_errors' => false]);
        } catch (RequestException $ex) {
            return $ex->getResponse()->getBody()->getContents();
        }
    }

    public function get(string $url): Response
    {
        return $this->callMethod('get', $url);
    }

    public function post(string $url, array $data): Response
    {
        return $this->callMethod('post', $url, $data);
    }
}
