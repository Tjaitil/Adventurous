<?php
    require('../libs/handler.php');
    $handler = new handler();
    $handler->sessionCheck(true);
    $modelname = $_GET['model'];
    $methodname = $_GET['method'];
    
    $model = $handler->includeModel(true, $_GET['model'], $_SESSION['gamedata'], true);
    $method = $handler->checkMethod($model, $_GET['method']);
    if ($method === true) {
        if(count($_GET) < 3) {
            $model->$methodname(); //If there is less than 3 parameters, the only parameter is model and method
        }
        else  {
            switch($modelname) {
                case "adventures":
                    if($methodname === "getAdventure") {
                        $model->$methodname($_GET['id']);    
                    }
                    else if($methodname === "checkUser") {
                        $model->$methodname($_GET['username']);
                    }
                    break;
                case "Main":
                    $model->$methodname($_GET['clock']);
                    break;
                case "market":
                    $model->$methodname($_GET['part']);
                    break;
                case "messages":
                    if($methodname == "showMessage") {
                        $message_id = $_GET['message_id'];
                        $model->$methodname($message_id);
                    }
                    if($methodname == "userCheck") {
                        $user = $_GET['input'];
                        $model->$methodname($user, true);
                    }
                    break;
                case "Item":
                    $model->$methodname($_GET['query']);
                    break;
                case 'talk':
                    //method talk()
                    $model->$methodname($_GET['person'], $_GET['part']);
                    break;
            }
        }
    }
    else {
        echo "Method does not exists!";
    }
?>