<?php
    require('root/routes.php');
    require('root/db.php');
    require(constant('ROUTE_BASE') . 'controller.php');
    require(constant('ROUTE_BASE') . 'bootstrap.php');
    require(constant('ROUTE_BASE') . 'model.php');
    require(constant('ROUTE_BASE') . 'view.php');
    require(constant('ROUTE_BASE') . 'session.php');
    require(constant('ROUTE_BASE') . 'ajaxexception.php');
    require(constant('ROUTE_HELPER') . 'general_helpers.php');
    require(constant('ROUTE_HELPER') . 'model_helpers.php');
    $session = new session();
    $bootstrap = new bootstrap($session);
    $bootstrap->init();
?>
