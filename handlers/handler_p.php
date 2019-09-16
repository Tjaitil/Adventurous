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
                case "AdventureRequest":
                    switch($methodname) {
                        case 'joinAdventure':
                            $model->$methodname($_POST['id']);
                            break;
                        case 'request':
                            $model->$methodname($_POST['id'], $_POST['route'], $_POST['invitee']);
                            break;
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
                    switch($methodname) {
                        case 'transfer':
                        case 'offRest':
                        case 'changeType':
                            $model->$methodname($_POST['warriors']);
                            break;
                        case 'healWarrior':
                            if(isset($_POST['item'])) {
                            $model->$methodname($_POST['type'], $_POST['warriors'], $_POST['item'], $_POST['quantity']); 
                            }
                            else {
                                $model->$methodname($_POST['type'], $_POST['warriors']);
                            }
                            break;
                    }
                    break;
                case "Bakery":
                    //method make()
                    $model->$methodname($_POST['item'], $_POST['quantity']);
                    break;
                case 'Crops':
                    //method getSeeds()
                        $model->$methodname($_POST['item'], $_POST['quantity']);
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
                        case 'fetchItem':
                            $model->$methodname($_POST['id']);
                            break;
                        case 'trade':
                            $model->$methodname($_POST['id'], $_POST['amount']);
                            break;
                        case 'newOffer':
                            $model->$methodname(json_decode($_POST['JSON_data']));
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
                case "RecruitWorker":
                    //method recruitWorker()
                    if(isset($_POST['level'])) {
                        $model->$methodname($_POST['type'], $_POST['level']);
                    }
                    else {
                        $model->$methodname($_POST['type']);   
                    }
                    
                    break;
                case "setadventure":
                    //method provide()
                    if(!isset($_POST['warrior_check'])) {
                        $model->$methodname(strtolower($_POST['item']), $_POST['quantity'], $warrior_check = false);
                    }
                    else {
                        $model->$methodname($item = false, $quantity = false, $_POST['warrior_check']);
                    }
                    break;
                case "SetArmymission":
                    //method setMission()
                    $model->$methodname($_POST['mission_id'], $_POST['warrior_check']);
                    break;
                case "SetCrops":
                    //setCrops()
                    var_dump($_POST);
                    var_dump(json_decode($_POST['JSON_data'], true));
                    $model->$methodname(json_decode($_POST['JSON_data'], true));
                    break;
                case "SetMine":
                    $model->$methodname($_POST['mineral'], $_POST['workforce']);
                    break;
                case "SetTraining":
                    $model->$methodname($_POST['warrior'], $_POST['type']);
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
                    if($methodname == 'pickUp') {
                        if(isset($_POST['favor'])) {
                            $model->$methodname($favor = true);
                        }
                        else {
                            $model->$methodname();
                        }
                    }
                    break;
                case "setassignment":
                    //method newAssignment()
                    if(isset($_POST['favor'])) {
                        $model->$methodname($_POST['assignment_id'], $favor = true);
                    }
                    else {
                        $model->$methodname($_POST['assignment_id']);
                    }
                    break;
            }
        }
    }
    else {
        echo "model doesn't exists!";
        return false;
    }
?>