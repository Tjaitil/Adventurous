<?php
    require('../libs/handler.php');
    $handler = new handler(true);
    $handler->sessionCheck(true);
    $modelname = $_GET['model'];
    $methodname = $_GET['method'];
    
    $model = $handler->includeModel($_GET['model'], $_SESSION['gamedata']);
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
                    if(isset($_GET['part'])) {
                        // Method getData()
                        $model->$methodname($_GET['part']);
                    }
                    else {
                        // Method searchOffers()
                        $model->$methodname($_GET['item']);
                    }
                    
                    break;
                case "messages":
                    switch($methodname) {
                        case 'showMessage':
                            $model->$methodname($_GET['message_id']);
                            break;
                        case 'userCheck':
                            // $_GET['input'] is user input
                            $model->$methodname($_GET['input'], true);
                            break;
                        case 'getmMessages':
                            $model->$methodname($_GET['table'], $_GET['type'], $_GET['date']);
                            break;
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