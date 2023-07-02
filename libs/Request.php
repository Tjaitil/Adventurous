<?php

namespace App\libs;

use Exception;
use Respect\Validation\Validator as v;

class Request
{
    private $data = [];

    private $method;

    public function __construct()
    {
        $this->setRequestMethod();
    }

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
                $data = json_decode(file_get_contents("php://input"), true);
                $this->setRequestData($data);
                break;
            case 'GET':
                $this->method = "GET";
                $this->setRequestData($_GET);
                break;
            case 'PUT':
                $this->method = "PUT";
                $data = json_decode(file_get_contents("php://input"), true);
                $this->setRequestData($data);
                break;
        }
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
}
