<?php
    require('../libs/handler.php');
    $handler = new handler(true);
    $handler->sessionCheck(true);
    $title = $_GET['building'];
    /*$model = $handler->includeModel(ucfirst($_GET['building']), $_SESSION['gamedata']);
    if($handler->checkMethod($model, 'getData') === true) {
        $data = $model->getData();
    }*/
    /*$method = $handler->checkMethod($model, $_POST['method']);*/
    $controllerArray = array('armyCamp');
    /*if(in_array($_GET['building'], $controllerArray)) {
        $controller = $handler->loadController($_GET['building']);
        if(is_object($controller)) {
            $controller->index();
        }
    }*/
    $controller = $handler->loadController($_GET['building']);
    if(is_object($controller)) {
        $controller->index();
    }
    /*include('../views/' . strtolower($_GET['building']) . '.php');*/
?>