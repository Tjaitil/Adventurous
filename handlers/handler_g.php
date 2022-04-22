<?php
    require('../libs/handler.php');
    $handler = new handler(true);
    $handler->sessionCheck(true);
    $modelname = $_GET['model'];
    $methodname = $_GET['method'];
    
    $model = $handler->includeModel($_GET['model'], $_SESSION['gamedata']);
    $method = $handler->checkMethod($model, $_GET['method']);
    if($method === true) {
        switch($modelname) {
            case "Market":
                if(isset($_GET['part'])) {
                    // Method getData()
                    $model->$methodname($_GET['part']);
                }
                else {
                    // Method searchOffers()
                    $model->$methodname($_GET['item']);
                }
                
                break;
            case "Messages":
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
            default:
                $model->$methodname($_GET);
                echo $model->response->send();
                break;
        }
    }
    else {
        echo "Method does not exists!";
    }
?>