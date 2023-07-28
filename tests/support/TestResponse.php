<?php

namespace App\tests\support;

class TestResponse
{
    public int $statusCode = 200;
    public string|null $body = "";

    public function __construct(string|null $response = "")
    {
        $this->statusCode = http_response_code();
        $this->body = \json_encode($response);
    }
}
