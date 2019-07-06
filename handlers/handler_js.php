<?php
    require('../libs/handler.php');
    
    $handler = new handler(true);
    $handler->sessionCheck();
    $modelname = $_GET['model'];
    $methodname = $_GET['method'];
    $model = $handler->includeModel(true, $_GET['model'], $_SESSION['gamedata'], true);
    $method = $handler->checkMethod($model, $_GET['method']);
    if($method === true) {
        $model->$methodname($js = true);   
    }
?>