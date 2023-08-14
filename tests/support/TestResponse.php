<?php

namespace App\tests\support;

class TestResponse
{
    public int $statusCode = 200;
    public mixed $body;


    /**
     * 
     * @param string|null $response
     */
    public function __construct($response = "")
    {
        $this->statusCode = http_response_code();

        $this->body = $response;
    }
}
