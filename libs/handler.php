<?php

namespace App\libs;

use \autoloader;

class handler
{
    private $parent_dir = "";

    function __construct($model = false)
    {
        $this->parent_dir = $_SERVER["PWD"] ?? dirname(__FILE__, 2) . '/';
        require_once($this->parent_dir . '/root/routes.php');
        require_once($this->parent_dir . '/root/autoloader.php');
        require_once($this->parent_dir . '/root/config.php');

        $autoloader = new autoloader();
        spl_autoload_register(array($autoloader, 'libsLoader'));
        spl_autoload_register(array($autoloader, 'modelLoader'));
        spl_autoload_register(array($autoloader, 'controllerLoader'));

        // If ajax call is to get a file or session data
        if ($model === true) {
            require_once($this->parent_dir . constant('ROUTE_HELPER') . 'model_helpers.php');
        }
    }
    public function sessionCheck()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }
    public function includeModel($modelname, $session)
    {
        $modelloc = $modelname . '_model.php';
        $model = $modelname . '_model';
        $modelName = 'App\\models\\' . $model;
        if (class_exists($modelName)) {
            $session['username'] = $_SESSION['username'];
            return new $modelName($session);
        } else {
            // Report if user tries to access a model that doesn't exists
            $this->errorReport($model . "doesn't exists");
        }
    }
    public function checkMethod($model, $method)
    {
        if (method_exists($model, $method)) {
            return true;
        } else {
            // Report if user tries to access a method that doesn't exists
            $this->errorReport($method . " doesn't exists in" . get_class($model));
        }
    }
    private function errorReport($message)
    {
        mail('miner123@hotmail.no', 'ERROR', $message, 'FROM: <system@adventurous.no');
    }
    public function loadController($controllerName)
    {
        $controllerName  = strtolower($controllerName);
        $controllerFile = $controllerName . '.php';
        $newControllerName = ucfirst($controllerName) . 'Controller';
        if (class_exists($controllerName)) {
            /*$session['username'] = $_SESSION['username'];*/
            return DependencyContainer::getInstance()->get($controllerName);
        } else if (class_exists($newControllerName)) {
            return DependencyContainer::getInstance()->get($newControllerName);
        } else {
            // Report if user tries to access a model that doesn't exists
            $this->errorReport($controllerFile . "doesn't exists");
        }
    }
}
