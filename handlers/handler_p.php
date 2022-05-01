<?php
    require('../libs/handler.php');
    $handler = new handler(true);
    $handler->sessionCheck(true);
    $errorHandler = new errorhandler();
    $modelname = $_POST['model'];
    $methodname = $_POST['method'];
    if($modelname === "cookieMaker") {
        $model = $handler->includeModel('CanvasGameID', $_SESSION['gamedata']);
    }
    else {
        $model = $handler->includeModel($_POST['model'], $_SESSION['gamedata']);
        if(!is_object($model)) {
            echo "ERROR";
        }    
    }
    $method = $handler->checkMethod($model, $_POST['method']);
    if($method === true) {
        // Response is returned from model
        $model->$methodname($_POST);
        echo $model->response->send();
    }
    else if($modelname === "cookieMaker") {
        $model->checkGameID($_POST);
        echo $model->response->send();
    }
    else {
        // errorHandler;
        if($modelname !== "cookieMaker") {
            echo "ERROR: method doesnt exists!";
            return false;
        }
    }
?>