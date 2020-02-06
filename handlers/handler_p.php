<?php
    require('../libs/handler.php');
    $handler = new handler(true);
    $handler->sessionCheck(true);
    $errorHandler = new errorhandler();
    $modelname = $_POST['model'];
    $methodname = $_POST['method'];
    $model = $handler->includeModel($_POST['model'], $_SESSION['gamedata']);
    $method = $handler->checkMethod($model, $_POST['method']);
    if($method === true) {
        switch($modelname) {
            case 'Travel':
                $city = $_SESSION['gamedata']['location'];
                $travel_countdown = $_SESSION['gamedata']['arrive_time'];
                $model->$methodname($city, $_POST['destination'], $travel_countdown);
                break;
            case "Travelbureau":
                $model->$methodname($_POST['shop'], $_POST['item']);
                break;
            case "Market":
                switch($methodname) {
                    case 'cancelOffer':
                    case 'fetchItem':
                        $model->$methodname($_POST['id']);
                        break;
                    case 'trade':
                        $model->$methodname($_POST['id'], $_POST['amount']);
                        break;
                    case 'newOffer':
                        $model->$methodname(json_decode($_POST['JSON_data'], true));
                }
                break;
            case "RecruitWorker":
                //method recruitWorker()
                if(isset($_POST['level'])) {
                    $model->$methodname($_POST['type'], $_POST['level']);
                }
                else {
                    $model->$methodname($_POST['type']);   
                }
                
                break;
            case "SetAdventure":
                //method provide()
                if(!isset($_POST['warrior_check'])) {
                    $model->$methodname(strtolower($_POST['item']), $_POST['quantity'], $warrior_check = false);
                }
                else {
                    $model->$methodname($item = false, $quantity = false, $_POST['warrior_check']);
                }
                break;
            case "Stockpile":
                //method updateInventory()
                $model->$methodname($_POST['insert'], strtolower($_POST['item']), $_POST['quantity']);
                break;
            case "Updateassignment":
            case "Trader":
                if($methodname == 'pickUp') {
                    if(isset($_POST['favor'])) {
                        $model->$methodname($favor = true);
                    }
                    else {
                        $model->$methodname();
                    }
                }
                break;
            case "Setassignment":
                //method newAssignment()
                if(isset($_POST['favor'])) {
                    $model->$methodname($_POST['assignment_id'], $favor = true);
                }
                else {
                    $model->$methodname($_POST['assignment_id']);
                }
                break;
            default:
                $model->$methodname($_POST);
                break;
        }
    }
    else {
        return false;
    }
?>