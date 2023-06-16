<?php
class autoloader
{

    public function __construct()
    {
        $this->registerAutoloaders();
    }
    private function registerAutoloaders()
    {
        // TODO: Fix this
        spl_autoload_register(array($this, 'libsLoader'));
        spl_autoload_register(array($this, 'modelLoader'));
        spl_autoload_register(array($this, 'controllerLoader'));
        spl_autoload_register(array($this, 'serviceLoader'));
        spl_autoload_register(array($this, 'ressourceLoader'));
        spl_autoload_register(array($this, 'testLoader'));
        spl_autoload_register(array($this, 'builderLoader'));
        spl_autoload_register(array($this, 'actionLoader'));
    }

    public function libsLoader($className)
    {
        $file = dirname(__DIR__, 1) . '/' . constant('ROUTE_BASE') . $className . '.php';
        if (file_exists($file)) {
            require($file);
        } else {
            return;
        }
    }
    public function modelLoader($className)
    {
        $file = dirname(__DIR__, 1) . '/' . constant('ROUTE_MODEL') . $className . '.php';
        if (file_exists($file)) {
            require($file);
        } else {
            return;
        }
    }
    public function controllerLoader($className)
    {
        $file = dirname(__DIR__, 1) . '/' . constant('ROUTE_CONTROLLER') . $className . '.php';
        if (file_exists($file)) {
            require($file);
        } else {
            return;
        }
    }

    public function ressourceLoader($className)
    {
        $file = dirname(__DIR__, 1) . "/resources" . "/" . $className . '.php';
        if (file_exists($file)) {
            require($file);
        } else {
            return;
        }
    }

    public function serviceLoader($className)
    {
        $file = dirname(__DIR__, 1) . '/' . constant('ROUTE_SERVICES') . $className . '.php';
        if (file_exists($file)) {
            require($file);
        } else {
            return;
        }
    }

    public function testLoader($className)
    {
        $file = dirname(__DIR__, 1) . '/' . 'tests/' . $className . '.php';
        if (file_exists($file)) {
            require($file);
        } else {
            return;
        }
    }

    public function builderLoader($className)
    {
        $file = dirname(__DIR__, 1) . '/' . 'builders/' . $className . '.php';
        if (file_exists($file)) {
            require($file);
        } else {
            return;
        }
    }

    public function actionLoader($className)
    {
        $file = dirname(__DIR__, 1) . '/' . 'actions/' . $className . '.php';
        if (file_exists($file)) {
            require($file);
        } else {
            return;
        }
    }
}
