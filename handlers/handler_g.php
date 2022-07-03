<?php
    require('../libs/handler.php');
    $handler = new handler(true);
    $handler->sessionCheck(true);
    $modelname = $_GET['model'];
    $methodname = $_GET['method'];
    
    $model = $handler->includeModel($_GET['model'], $_SESSION['gamedata']);
    $method = $handler->checkMethod($model, $_GET['method']);
    if($method === true) {
        $model->$methodname($_GET);
        echo $model->response->send();
    }
    else {
        echo "Method does not exists!";
    }
?>