<?php
    require('../libs/handler.php');
    $handler = new handler(true);
    $handler->sessionCheck();
    $modelname = $_GET['model'];
    $methodname = $_GET['method'];
    $model = $handler->includeModel($_GET['model'], $_SESSION['gamedata']);
    $method = $handler->checkMethod($model, $_GET['method']);
    if($method === true) {
        $model->$methodname($js = true);
        echo $model->response->send();
    }
?>