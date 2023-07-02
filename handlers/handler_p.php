<?php
    require('../libs/handler.php');
    $handler = new handler(true);
    $handler->sessionCheck(true);
    $errorHandler = new errorhandler();
    // $request_data = json_decode(file_get_contents('php://input'), true);
    $rquest_data = $_POST;
    $modelname = $request_data['model'];
    $methodname = $request_data['method'];
    if($modelname === "cookieMaker") {
        $model = $handler->includeModel('CanvasGameID', $_SESSION['gamedata']);
    }
    else {
        $model = $handler->includeModel($request_data['model'], $_SESSION['gamedata']);
        if(!is_object($model)) {
            echo "ERROR";
        }    
    }
    $method = $handler->checkMethod($model, $request_data['method']);
    if($method === true) {
        // Response is returned from model
        $model->$methodname($request_data);
        echo $model->response->send();
    }
    else if($modelname === "cookieMaker") {
        $model->checkGameID($request_data);
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