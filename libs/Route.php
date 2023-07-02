<?php

namespace App\libs;

/**
 * @property string $uri
 * @property array $action
 * @property string $method
 */
class Route extends Router
{

    private $base_uri;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    public static function getRouter()
    {
        return Router::getInstance();
    }

    public static function createNewRegisteredRoute(string $method, string $uri, array $action)
    {
        $class = new RegisteredRoute();
        $class->setMethod($method);
        $class->setUri($uri);
        $class->setBaseUri($uri);
        $class->setAction($action);
        self::getRouter()->registerRoute($class);
    }

    public static function get(string $uri, array $action)
    {
        self::createNewRegisteredRoute('GET', $uri, $action);
    }


    public static function post(string $uri, array $action)
    {
        self::createNewRegisteredRoute('POST', $uri, $action);
    }

    public static function put(string $uri, array $action)
    {
        self::createNewRegisteredRoute('PUT', $uri, $action);
    }
}
