<?php

namespace App\libs;

use Jenssegers\Blade\Blade;


class controller
{
    protected $model;
    protected $controller;
    protected Blade $viewEngine;
    public $viewBlade = false;

    public function __construct()
    {
        $this->bootViewEngine();
    }


    /**
     * Boot the blade rendering engine
     * 
     * @return void 
     */
    public function bootViewEngine()
    {
        $views = ROUTE_ROOT . 'views';
        $cache = ROUTE_ROOT . 'cache';
        $viewEngine = new ViewEngine($views, $cache);
        $this->viewEngine = $viewEngine->get();
    }



    // Render site
    public function render($name, $title, $data, bool $up = false, bool $ajax = false, bool $useBlade = false)
    {
        $this->viewBlade = $useBlade;
        foreach ($data as $key => $value) {
            ${"$key"} = $value;
        }

        if ($ajax == false) {
            if ($up !== false) {
                require(constant('ROUTE_ROOT') . constant('ROUTE_VIEW') . 'page.php');
            } else {
                require(constant('ROUTE_VIEW') . 'page.php');
            }
        } else {
            $data['title'] = $title;
            if ($this->viewBlade) {
                echo $this->viewEngine->render($name, $data);
            } else {
                require(constant('ROUTE_ROOT') . constant('ROUTE_VIEW') . $name . '.php');
            }
        }
    }



    //Render site with error Array   
    /**
     * @deprecated version
     */
    public function renderWE($name, $title, $gamedata, $data, $up = false)
    {
        if ($up !== false) {
            require('../' . constant('ROUTE_VIEW') . 'page.php');
        } else {
            if ($name == 'login') {
                require(constant('ROUTE_VIEW') . $name . '.php');
            } else {
                require(constant('ROUTE_VIEW') . 'page.php');
            }
        }
    }



    public function loadModel($name, $db, $secondaryModel = false)
    {
        $path = $name . '_model';
        // if (class_exists('App\\models\\' . $path)) {
        $modelName = 'App\\models\\' . $name . '_model';
        /*$db = new database();*/
        // If the model doesn't need the username, don't provide it
        if (in_array($modelName, array('Login_model', 'Registration_model'))) {
            $this->model = new $modelName();
        } else if (in_array($modelName, array('newuser_model', 'gamedata_model')) == true) {
            $this->model = new $modelName($_SESSION['username'], $db);
        } else {
            $session = $_SESSION['gamedata'] ?? [];
            $session['username'] = $_SESSION['username'];
            if ($secondaryModel) {
                return new $modelName($session);
            } else {
                $this->model = new $modelName($session);
            }
        }
        // }
    }



    public function loadController($controllerName)
    {
        $controllerName  = strtolower($controllerName);
        $controllerFile = $controllerName . '.php';
        if (class_exists($controllerName)) {
            /*$session['username'] = $_SESSION['username'];*/
            $this->controller = new $controllerName();
        } else {
            // Report if user tries to access a model that doesn't exists
            // $this->errorReport($controllerFile . "doesn't exists");
        }
    }
}
