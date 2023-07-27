<?php

namespace App\tests\support;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

trait RequestTrait
{
    public static ?Response $response;
    protected ?Client $client;
    public int $statusCode = 0;

    protected function configureClient()
    {
        if (!isset($this->client)) {
            $this->client = new Client([
                'base_uri' => "http://localhost:8080",
                'cookies' => true
            ]);
        }
    }

    protected function callMethod(string $method, string $url, array $data = null)
    {
        $this->configureClient();
        try {
            $this->response = $this->client->{$method}($url, ['json' => $data, 'http_errors' => false]);
            $this->statusCode = $this->response->getStatusCode();
            return $this->response;
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
