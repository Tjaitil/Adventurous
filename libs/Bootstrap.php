<?php

namespace App\libs;

use App\controllers\AdvclientController;
use App\controllers\IndexController;
use App\controllers\LoginController;
use App\controllers\MainController;
use App\controllers\notfound;
use \Exception;

// TODO: Clean up this file
class Bootstrap
{
    private $url = NULL;
    private $controller = NULL;
    private $controllerPath = 'controllers/';
    private $errorhandler;
    private $defaultFile = 'index.php';

    private $routes = [
        "advclient" => AdvclientController::class,
        "main" => MainController::class
    ];

    public $session;

    function __construct(session $session)
    {
        $this->errorhandler = new errorhandler();
        $this->session = $session;
    }
    function init()
    {
        $this->getURL();
        if (in_array($this->url[0], ['api'])) {
            require(constant('ROUTE_ROOT') . 'routes/api.php');
            return false;
        } else if (in_array($this->url[0], array('gameguide', 'client')) == false && count($this->url) > 1) {
            require(constant('ROUTE_VIEW') . 'error.php');
            return;
        } else if ($this->url[0] === 'logout') {
            $this->session->destroy();
        }
        if (strpos($this->url[0], '-')) {
            $this->url[0] = implode(explode('-', $this->url[0]));
        }
        $this->session->validatelogin();
        if ($this->session->status == false && $this->url[0] != 'registration') {
            if (isset($_SESSION['outdatedSessionID']) === false) {
                session_unset();
            }
            $this->controller = new LoginController($this->session);
            $this->controller->index();
            return false;
        }
        $array = array(NULL, 'none');
        $allowed = array('newuser', 'registration', 'gameguide', 'login');

        // If the user hasn't chosen a profiency yet, check if the user is trying to access a page that requires profiency,
        // if yes send the user to the location of the new user.
        /*if(in_array($_SESSION['profiency'], $array) != false && in_array($this->url[0], $allowed) == false) {
                header("Location: /newuser");
            }*/

        if (empty($this->url[0])) {
            $this->loadDefaultController();
            return false;
        }
        $this->loadExistingController();
    }
    private function getURL()
    {
        $this->url = $_SERVER['REQUEST_URI'];
        $this->url = ltrim($this->url, '/');
        $this->url = explode("/", $this->url);
    }

    private function loadDefaultController()
    {
        $link = $this->controllerPath . $this->defaultFile;
        $this->controller = new IndexController();
        $this->controller->index();
    }

    private function loadExistingController()
    {
        $file = constant('ROUTE_CONTROLLER') . $this->url[0] . '.php';

        try {
            if ($this->url[0] == 'gameguide') {
                $this->controller = new $this->url[0]($this->url);
                $this->controller->index();
            } else {
                $dependencyContainer = DependencyContainer::getInstance();

                if ($this->url[0] === 'login') {
                    $this->controller = new LoginController($this->session);
                } else {

                    $matchedController = $this->routes[$this->url[0]];
                    $this->url[0] .= 'Controller';
                    $dependencyContainer = DependencyContainer::getInstance();
                    $this->controller = $dependencyContainer->get($matchedController);
                    $this->controller->index(...$dependencyContainer->getMethodParameters($this->controller, 'index'));
                }
            }
        } catch (Exception $e) {
            Logger::log($e->getMessage(), 'error');
            $file = constant('ROUTE_CONTROLLER') . 'error.php';
            // Report error
            $this->errorhandler->reportError(array("None", "Controller doesn't exists " . __METHOD__));
            require $file;
            $this->controller = new notfound();
            $this->controller->index();
        }
    }
}
