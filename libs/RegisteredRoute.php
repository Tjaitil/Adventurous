<?php

namespace App\libs;

class RegisteredRoute
{

    private $uri;
    private $action;
    private $method;
    private $base_uri;

    public function __constructor()
    {
    }

    public function setUri(string $uri)
    {
        $this->uri = $uri;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setAction(array $action)
    {
        $this->action = $action;
    }

    /**
     * Undocumented function
     *
     * @return string $class_name
     */
    public function getClassName()
    {
        return $this->action[0];
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getClassMethodName()
    {
        return $this->action[1];
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setBaseURI()
    {
        $this->base_uri =  '/' . explode('/', $this->getUri())[1];
    }

    public function getBaseURI()
    {
        return $this->base_uri;
    }

    public function getRouteLevels()
    {
        return [count(explode('/', $this->base_uri)),];
    }

    public function getPaths()
    {
        $path_array = explode('/', $this->base_uri);

        return $path_array;
    }
}
