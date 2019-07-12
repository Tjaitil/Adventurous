<?php
    require('../libs/handler.php');
    $handler = new handler();
    $handler->sessionCheck(true);
    $modelname = $_POST['model'];
    $methodname = $_POST['method'];

    $model = $handler->includeModel(true, $_POST['model'], $_SESSION['gamedata'], true);
    $method = $handler->checkMethod($model, $_POST['method']);
    if($method === true) {
        if(count($_POST) < 3) {
            $model->$methodname(); //If there is less than 3 parameters, the only parameter is model and method
        }
        else {
            switch($modelname) {
                case "adventurerequest":
                    if($methodname == "joinAdventure") {
                        $model->$methodname($_POST['id']);
                    }
                    if($methodname == "adventureRequest") {
                        $model->$methodname($_POST['id'], $_POST['route'], $_POST['invitee']);
                    }
                    break;
                case "armory":
                    if(isset($_POST['part'])) {
                        //method removeArmor()
                        $model->$methodname($_POST['warrior_id'], $_POST['item'], $_POST['part']);
                    }
                    else {
                        //method wearArmor()
                        $model->$methodname($_POST['warrior_id'], $_POST['item']);
                    }
                    break;
                case "ArmyCamp":
                    //method transfer()
                    $model->$methodname($_POST['warriors']);
                    break;
                case "Bakery":
                    //method make()
                    $model->$methodname($_POST['type'], $_POST['quantity']);
                    break;
                case "buyworker":
                    //method buyWorker()
                    $profiency = $_SESSION['gamedata']['profiency'];
                    $profiency_level = $_SESSION['gamedata']['profiency_level'];
                    $model->$methodname($_POST['type'], $_POST['type_level'], $profiency, $profiency_level);
                    break;
                case 'travel':
                    $city = $_SESSION['gamedata']['location'];
                    $travel_countdown = $_SESSION['gamedata']['arrive_time'];
                    $model->$methodname($city, $_POST['destination'], $travel_countdown);
                    break;
                case "travelbureau":
                    $model->$methodname($_POST['shop'], $_POST['item']);
                    break;
                case "Main":
                    $model->$methodname($_POST['message']);
                    break;
                case "market":
                    switch($methodname) {
                        case 'cancelOffer':
                            $model->$methodname($_POST['id']);
                            break;
                        case 'trade':
                            $model->$methodname($_POST['id'], $_POST['amount']);
                            break;
                    }
                    break;
                case 'market':
                    //method getData()
                    if($_POST['mNumber'] == 1) {
                        $model->$methodname($js1 = true);
                    }
                    else if($_POST['mNumber'] == 2) {
                        $model->$methodname($js1 = true, $js2 = true);
                    }
                    else {
                        $model->$methodname($js1 = true, $js2 = true, $js3 = true);
                    }
                    break;
                case "citycentre":
                    if($methodname === 'changeArtefact' ) {
                        $model->$methodname($_POST['artefact']);
                    }
                    else if($methodname === 'buyPermits') {
                        $model->$methodname($_POST['amount']);
                    }
                    break;
                case "setadventure":
                    //method provide()
                    $adventure_id = $_GET['id'];
                    if($_POST['route'] == 'item') {
                        $item = $_GET['item'];
                        $quantity = $_GET['quantity'];
                        $model->$methodname($_POST['id'], $_POST['route'], $_POST['item'], $_POST['quantity'], $warrior_check = false);
                    }
                    else {
                        $model->$methodname($_POST['id'], $_POST['route'], $item = false, $quantity = false, $_POST['warrior_check']);
                    }
                    break;
                case "SetArmymission":
                    //method setMission()
                    $model->$methodname($_POST['mission_id'], $_POST['warrior_check']);
                    break;
                case "stockpile":
                    //method updateInventory()
                    $model->$methodname($_POST['insert'], strtolower($_POST['item']), $_POST['quantity']);
                    break;
                case "smithy":
                    //method smith()
                    $model->$methodname($_POST['item'], $_POST['mineral'], $_POST['amount']);
                    break;
                case "profiency":
                    //method changeProfiency()
                    $model->$methodname($_POST['newProfiency']);
                    break;
                case "Merchant":
                    $model->$methodname($_POST['item'], $_POST['quantity']);
                    break;
                case "CombatCalculator":
                    $model->$methodname(json_decode($_POST['form_data']));
                    break;
                case "UpdateTraining":
                    //method updateTraining()
                    $model->$methodname($_POST['warrior_id']);
                    break;
                case "updateassignment":
                case "trader":
                case "setassignment":
                    //method newAssignment()
                    if(!isset($_POST['favor'])) {
                        $model->$methodname($_POST['assignment_id']);
                    }
                    else {
                        $model->$methodname($_POST['assignment_id'], $favor = true);
                    }
                    break;
            }
        }
    }
?>