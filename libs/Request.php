<?php

namespace App\libs;

use Exception;
use Respect\Validation\Validator as v;

class Request
{
    private $data = [];

    private string $method;
    private string $uri;

    public function __construct()
    {
        $this->setRequestMethod();
    }



    /**
     * Validate request input
     * 
     * @param array $rules 
     * @return void 
     * @throws \Exception 
     */
    public function validate(array $rules)
    {
        foreach ($rules as $key => $value) {
            if (!isset($this->data[$key])) {
                throw new Exception("Missing key " . $key);
            }

            v::key($key, $value)->check($this->data);
        }
    }



    /**
     * Retrieve user input
     *
     * @param string $key
     * @param int|bool| $name
     *
     * @return mixed
     */
    public function getInput(string $key, $type = null)
    {

        return $this->data[$key] ?? null;
    }



    /**
     * Set request method and get data based on method
     *
     * @return void
     */
    private function setRequestMethod()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->method = "POST";
                break;
            case 'GET':
                $this->method = "GET";
                break;
            case 'PUT':
                $this->method = "PUT";
                break;
        }
        $this->setRequestData($this->readRequestData());
    }



    /**
     * Set request data
     * 
     * @return void
     */
    public function setRequestData($requestData)
    {
        if ($requestData === null) return false;
        foreach ($requestData as $key => $value) {
            $this->data[$key] = $value;
        }
    }



    /**
     * @return array
     */
    private function readRequestData()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (App::getInstance()->getIsMocking()) {
            if ($this->method === "POST") {
                $data = $_POST;
            } else if ($this->method === "GET") {
                $data = $_GET;
            }
        }
        return $data;
    }


    public function setUri(string $uri)
    {
        $this->uri = $uri;
    }
}
