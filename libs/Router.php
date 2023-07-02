<?php

namespace App\libs;

use \Exception;

/**
 * @property class $currentRoute
 * @property array $routes
 * @property RegisteredRoute $matchedRoute
 * @property Request $request
 */
class Router
{

    private static $instance = null;

    protected $targetURI;
    protected $currentRoute;
    protected array $routes;
    protected RegisteredRoute $matchedRoute;
    protected Request $request;

    private function __construct()
    {
    }

    /**
     * Get instance of self
     *
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get routes registered on base_uri
     *
     * @param string $base_uri
     *
     * @return RegisteredRoute[]
     */
    protected function getRoutes($base_uri)
    {
        return $this->routes[$base_uri] ?? [];
    }


    protected function stripURLParams(string $uri)
    {
        if (strpos($uri, '')) {
            $url_array = explode('?', $uri);
            $this->targetURI = $url_array[0];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get first path after /api/ 
     *
     * @return string
     */
    protected function getRootURI()
    {
        return '/' . explode('/', $this->targetURI)[1];
    }

    /**
     * Register route on base URI
     *
     * @param RegisteredRoute $route
     *
     * @return void
     */
    protected function registerRoute(RegisteredRoute $route)
    {
        $this->routes[$route->getBaseURI()][] = $route;
    }

    /**
     * Check class and method route registered on route
     *
     * @throws Exception $error If class fails or if matched route is not set
     * @return void
     */
    protected function checkRoute()
    {
        if (!isset($this->matchedRoute)) throw new Exception("Not found");

        if (!class_exists($this->matchedRoute->getClassName(), true)) {
            throw new Exception("Class does not exist!");
        } else if (!method_exists($this->matchedRoute->getClassName(), $this->matchedRoute->getClassMethodName())) {
            throw new Exception("Method does not exists");
        }

        $this->runRoute();
    }

    /**
     * Set Matched route
     *
     * @param RegisteredRoute $route
     *
     * @return void
     */
    private function setMatchedRoute(RegisteredRoute|null $route)
    {
        $this->matchedRoute = $route;
    }

    /**
     * Undocumented function
     *
     * @param string $method request Method
     * @param string $uri request uri
     *
     * @return void
     */
    public function matchRoute(string $method, string $uri)
    {
        $this->targetURI = $uri;
        $route_array = $this->getRoutes($this->getRootURI($uri));


        $matched_route = null;
        foreach ($route_array as $key => $route) {
            if ($route->getUri() === $this->targetURI && $route->getMethod() === $method) {
                $matched_route = $route;
                break;
            } else if (
                $route->getMethod() === $method &&
                count(preg_grep('/{([^}]*)}/', [$route->getUri()])) > 0 &&
                $this->getPathLevels($route->getUri()) === $this->getPathLevels($this->targetURI) &&
                $this->comparePaths($route->getUri(), $this->targetURI) === true
            ) {
                $matched_route = $route;
                $param_array = $this->getWildCardParam($route->getUri());

                $current_route_path = explode("/", $this->targetURI);

                $data = [];
                $key = $param_array["key"];
                $data[$key] = $current_route_path[$param_array["index"]];
                $this->getRequest()->setRequestData($data);
                break;
            }
        }

        try {
            if ($matched_route === null) throw new Exception("Not found");
            $this->setMatchedRoute($matched_route);
            $this->checkRoute();
        } catch (Exception $e) {
            database::getInstance()->rollBack();
            return Response::addMessage($e->getMessage())->setStatus(404);
        }

        return $matched_route !== null ? true : false;
    }

    /**
     * Run current route
     *
     * @param class $class
     * @param method $method
     *
     * @return void
     */
    protected function runRoute()
    {
        $class_name = $this->matchedRoute->getClassName();
        $class_method = $this->matchedRoute->getClassMethodName();

        $request = $this->getRequest($this->matchedRoute->getMethod());

        // Get instance of DepedencyContainer
        $DependencyContainer = DependencyContainer::getInstance();

        $DependencyContainer->set('Request', $request);

        $class = $DependencyContainer->get($class_name);

        $class->$class_method(...$DependencyContainer->getMethodParameters($class, $class_method));
    }


    /**
     * Get the number of path levels
     *
     * @param string $path
     *
     * @return int Number of path levels
     */
    private function getPathLevels(string $path)
    {
        return count(explode("/", $path));
    }

    /**
     * Find wildcardParam in URL
     *
     * @param string $path
     *
     * @return array [
     * "key" => (string|int) variable in path
     * "index" => (int) index in path where key is located
     */
    private function getWildCardParam(string $path)
    {
        $path_array = explode("/", $path);
        preg_match_all('/{(.*?)}/', $path, $matches);


        return [
            "key" => $matches[1][0],
            "index" => array_search($matches[0][0], $path_array)
        ];
    }

    private function comparePaths(string $path1, string $path2)
    {

        $path1 = explode("/", $path1);
        $path2 = explode("/", $path2);

        $diff = array_values(array_diff($path1, $path2));

        $only_wildard_matches = true;
        foreach ($diff as $key => $value) {
            if (preg_match('/{(.*?)}/', $value) === 0) {
                $only_wildard_matches = false;
                break;
            }
        }
        return $only_wildard_matches;
    }

    /**
     * Get Request Class
     *
     * @return Request
     */
    protected function getRequest()
    {
        if (!isset($this->request)) {
            $this->request = new Request();
        }
        return $this->request;
    }
}
