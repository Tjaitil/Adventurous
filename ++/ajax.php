<?php
    require('libs/handler.php');
    $handler = new handler();
    $handler->sessionCheck();
    $model = $handler->includeModel(true, $_GET['model'], $_SESSION['gamedata'], true);
    $method = $hadnler->checkMethod($model, $_GET['methodname']);

    if ($method === true) {
        if(count($_GET) < 3) {
            $model->$method(); //If there is less than 3 parameters, the only parameter is model and method
        }
        else  {
            switch($model) {
                case "travel":
                    if(!empty($_GET['city'])) {
                        $_SESSION['gamedata']['destination'] = $destination = $_GET['city'];
                    }
                    else {
                        $destination = $_SESSION['gamedata']['destination'];
                    }
                    $city = $_SESSION['gamedata']['location'];
                    $travel_countdown = $_SESSION['gamedata']['arrive_time'];
                    $model->$method($city, $destination, $travel_countdown);
                    break;
                case "merchant":
                    $item = $_GET['item'];
                    $value = $_GET['value'];
                    $quantity = $_GET['quantity'];
                    $city = $_SESSION['gamedata']['location'];
                    $model->$method($item, $value, $quantity, $city);
                    break;
                case "profiency":
                    $profiency = $_GET['newProfiency'];
                    $gold = $_SESSION['gamedata']['gold']['amount'];
                    $model->$method($profiency, $gold);
                    break;
                case "setassignment":
                case "deliver":
                case "pickUp":
                case "updateassignment":
                    $assignment_id = $_GET['assignment_id'];
                    if(!isset($_GET['favor'])) {
                        $model->$method($assignment_id);
                    }
                    else {
                        $favor = $_GET['favor'];
                        $model->$method($assignment_id, $favor = true);
                    }
                    break;
                case "travelbureau":
                    $shop = $_GET['shop'];
                    $item = $_GET['item'];
                    $gold = $_SESSION['gamedata']['gold']['amount'];
                    $city = $_SESSION['gamedata']['location'];
                    $model->$method($shop, $item, $gold, $city);
                    break;
                case "updatetraining":
                    $warrior_id = $_GET['warrior_id'];
                    $warrior_xp = $_SESSION['gamedata']['warrior']['warrior_xp'];
                    var_dump($warrior_id);
                    $model->$method($warrior_id, $warrior_xp);
                    break;
                case "buyworker":
                    $type = $_GET['type'];
                    $type_level = $_GET['type_level'];
                    $profiency = $_SESSION['gamedata']['profiency'];
                    $profiency_level = $_SESSION['gamedata']['profiency_level'];
                    $gold = $_SESSION['gamedata']['gold']['amount'];
                    $city = $_SESSION['gamedata']['location'];
                    $model->$method($type, $type_level, $profiency, $profiency_level, $gold, $city);
                    break;
                case "stockpile":
                    $item = $_GET['item'];
                    $insert = $_GET['insert'];
                    $quantity = $_GET['quantity'];
                    $model->$method($insert, $item, $quantity);
                    break;
                case "armory":
                    $warrior_id = $_GET['warrior_id'];
                    $item = $_GET['item'];
                    if(isset($_GET['part'])) {
                        $part = $_GET['part'];
                        $model->$method($warrior_id, $item, $part);
                    }
                    else {
                        $model->$method($warrior_id, $item);
                    }
                    break;
                case "setarmymission":
                    $mission_id = $_GET['mission_id'];
                    $warrior_check = $_GET['warrior_check'];
                    $warrior_xp = $_SESSION['gamedata']['warrior']['warrior_xp'];
                    $model->$method($mission_id, $warrior_check, $warrior_xp);
                    break;
                case "bakery":
                    $type = $_GET['type'];
                    $quantity = $_GET['quantity'];
                    $gold = $_SESSION['gamedata']['gold']['amount'];
                    $model->$method($type, $quantity, $gold);
                    break;
                case "messages":
                    if($method == "showMessage") {
                        $message_id = $_GET['message_id'];
                        $model->$method($message_id);
                    }
                    if($method == "userCheck") {
                        $user = $_GET['input'];
                        $model->$method($user);
                    }
                    break;
                case "adventures":
                    if($method == "getAdventure") {
                        $adventure_id = $_GET['id'];
                        $model->$method($adventure_id);
                    }
                    if($method == "checkUser") {
                        $username = $_GET['username'];
                        $model->$method($username);
                    }
                    break;
                case "adventurerequest":
                    if($method == "joinAdventure") {
                        $request_id = $_GET['id'];
                        $model->$method($request_id);
                    }
                    else {
                        $adventure_id = $_GET['id'];
                        $route = $_GET['route'];
                        $invitee = $_GET['invitee'];
                        $model->$method($adventure_id, $route, $invitee);
                    }
                    break;
                case "setadventure":
                    $adventure_id = $_GET['id'];
                    $route = $_GET['route'];
                    if($route == 'item') {
                        $item = $_GET['item'];
                        $quantity = $_GET['quantity'];
                        $model->$method($adventure_id, $route, $item, $quantity, $warrior_check = false);
                    }
                    else {
                        $warrior_check = $_GET['warrior_check'];
                        $model->$method($adventure_id, $route, $item = false, $quantity = false, $warrior_check);
                    }
                    break;
                case "smithy":
                    $item = $_GET['item'];
                    $mineral = $_GET['mineral'];
                    $quantity = $_GET['quanity'];
                    $model->$method($item, $mineral, $quantity, $cost);
                    break;
                case "item":
                    $query = $_GET['query'];
                    $model->$method($query);
                    break;
                case 'market':
                    if($_GET['mNumber'] == 1) {
                        $model->$method($js1 = true);
                    }
                    else if($_GET['mNumber'] == 2) {
                        $model->$method($js1 = true, $js2 = true);
                    }
                    else {
                        $model->$method($js1 = true, $js2 = true, $js3 = true);
                    }
                    break;
            }
        }
    }
    else {
        echo "Method does not exists!";
    }
?>