<?php
    require('../libs/handler.php');
    $handler = new handler(true);
    $handler->sessionCheck(true);
    $errorHandler = new errorhandler();
    if(!strlen($_POST['person']) > 0) {
        echo "ERROR";
        return false;
    }
    else {
        $controller = $handler->loadController('conversation');
        if($_POST['index'] !== "false" && $_POST['person'] == "null") {
            $response = $controller->getNextLine($_POST);
            if($response === "end") {
                echo "end";
            }
            else if(is_array($response)) {
                $model = $handler->includeModel($response[1][0], $_SESSION['gamedata']);
                if(!is_object($model)) {
                    echo "ERROR model";
                    return;
                }
                $method = $handler->checkMethod($model, $response[1][1]);
                $methodname = $response[1][1];
                if($method == true) {
                    if($controller->POST === null) {
                        $controller->POST = $_SESSION['conversation']['information'];
                    }
                    $method_response = $model->$methodname($controller->POST);
                    // If method_response is true then the function has returned some information, for example error
                    if($method_response[0] == true) {
                        /*If $method_response[1] is false it means there is an error message which is in index or else there is a created
                         * message inside the function
                        */
                        if($method_response[1] == false) {
                            $controller->echoConversation(true);    
                        }
                        else {
                            echo json_encode($method_response[1]);
                        }
                    }
                }
                else {
                    echo "ERROR";
                }
            }
            else {
                $_SESSION['conversation']['conv_index'] = $controller->index;
                $controller->echoConversation(true);
            }
            
        }
        else {
            $controller->setPerson($_POST);
        }
    }
?>