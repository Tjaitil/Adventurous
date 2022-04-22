<?php
    require('root/routes.php');
    require('root/autoloader.php');
    require('root/config.php');
    $autoloader = new autoloader();
    spl_autoload_register(array($autoloader, 'libsLoader'));
    spl_autoload_register(array($autoloader, 'modelLoader'));
    spl_autoload_register(array($autoloader, 'controllerLoader'));
    /*require(constant('ROUTE_BASE') . 'session.php');
    require(constant('ROUTE_BASE') . 'controller.php');*/
    $session = new session();
    /*require(constant('ROUTE_BASE') . 'bootstrap.php');
    require(constant('ROUTE_BASE') . 'model.php');
    require(constant('ROUTE_BASE') . 'view.php') */
    require(constant('ROUTE_HELPER') . 'general_helpers.php');
    require(constant('ROUTE_HELPER') . 'model_helpers.php');
    $bootstrap = new bootstrap($session);
    $bootstrap->init();
?>
