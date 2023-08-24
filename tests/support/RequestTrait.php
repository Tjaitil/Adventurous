<?php

namespace App\tests\support;

use App\libs\session;
use App\tests\support\TestResponse as Response;
use Exception;

trait RequestTrait
{
    public ?Response $response = null;
    private $POST_METHOD = "POST";
    private $GET_METHOD = "GET";



    /**
     * 
     * @param string $method 
     * @param string $url 
     * @param array|null $data 
     * @return string|void 
     */
    protected function callMethod(string $method, string $url, array $data = null)
    {
        try {
            if ($method === $this->POST_METHOD) {
                $_POST = $data;
            } else if ($method === $this->GET_METHOD) {
                $_GET = $data;
            } else {
                throw new Exception("Method not allowed");
            }

            $_SERVER['REQUEST_URI'] = $url;
            $_SERVER['REQUEST_METHOD'] = $method;

            $this->mockRequest($url);

            $_POST = [];
            $_GET = [];
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }



    /**
     * Will strip query string from url and pass it as data'
     *
     * @param string $url
     *
     * @return void
     */
    public function get(string $url, array $data = [])
    {
        $url_data = \parse_url($url)['query'] ?? [];
        \parse_str($url_data, $url_data);
        $data = array_merge($data, $url_data);
        $this->callMethod($this->GET_METHOD, $url, $data ?? null);
    }



    /**
     * 
     * @param string $url 
     * @param array $data 
     * @return void 
     */
    public function post(string $url, array $data)
    {
        $this->callMethod($this->POST_METHOD, $url, $data);
    }



    /**
     * 
     * @param string $url
     */
    private function mockRequest(string $url)
    {
        new session();

        \ob_start();
        if (strpos($url, 'api') != false) {
            require(constant('ROUTE_ROOT') . 'index.php');
        } else if (strpos($url, 'handler_v') !== false) {
            require(constant('ROUTE_ROOT') . 'handlers/handler_v.php');
        }
        $ob_output = \ob_get_clean();
        $this->response = new Response($ob_output);
    }
}
