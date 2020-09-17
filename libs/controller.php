<?php
    class controller {
        protected $model;
        protected $controller;
        
        function __construct($check = true) {
            if($check === true) {
                $this->checkLevel();
            }
        }
        // Render site
        public function render($name, $title, $gamedata, $up = false) {
            if($up !== false) {
                require('../' . constant('ROUTE_VIEW') . $name . '.php');    
            }
            else {
                require(constant('ROUTE_VIEW') . $name . '.php');
            }
        }
        //Render site with error Array   
        public function renderWE($name, $title, $gamedata, $data, $up = false) {
            if($up !== false) {
                require('../' . constant('ROUTE_VIEW') . $name . '.php');    
            }
            else {
                require(constant('ROUTE_VIEW') . $name . '.php');
            }
        }
        public function checkLevel() {
            $profiencies = array('farmer', 'miner', 'trader', 'warrior');
            $next_level = array($_SESSION['gamedata']['farmer']['xp'], $_SESSION['gamedata']['miner']['xp'],
                               $_SESSION['gamedata']['trader']['xp'], $_SESSION['gamedata']['warrior']['xp']);
            $_SESSION['gamedata']['level_up'] = array();
            for($i = 0; $i < count($profiencies); $i++) {
                if($_SESSION['gamedata'][$profiencies[$i]]['xp'] >= $_SESSION['gamedata'][$profiencies[$i]]['next_level']) {
                    if($_SESSION['gamedata'][$profiencies[$i]]['level'] >= 30 && $_SESSION['gamedata']['profiency'] !== $profiencies[$i]) {
                        continue;
                    }
                    else {
                        array_push($_SESSION['gamedata']['level_up'], $profiencies[$i]);
                    }
                }
            }
        }
        public function loadModel($name, $db) {
            $path = $name.'_model';
            if(class_exists($path)) {
                $modelName = $name . '_model';
                /*$db = new database();*/
                if(in_array($modelName, array('newuser_model', 'gamedata_model')) == true) {
                    $this->model = new $modelName($_SESSION['username'], $db);
                }
                else {
                    $session = $_SESSION['gamedata'];
                    $session['username'] = $_SESSION['username'];
                    $this->model = new $modelName($session);
                    /*switch($db) {
                        case true:
                            $session = $_SESSION['gamedata'];
                            $session['username'] = $_SESSION['username'];
                            $this->model = new $modelName($session, $db);
                            break;
                        case false:
                            $this->model = new $modelName($db);
                            break;
                    }*/
                }
            }
            else {
                header("Location: /maintenance");
            }
        }
        public function loadController($controllerName) {
            $controllerName  = strtolower($controllerName);
            $controllerFile = $controllerName . '.php';
            if(class_exists($controllerName)) {
                /*$session['username'] = $_SESSION['username'];*/
                $this->controller = new $controllerName();
            }
            else {
                // Report if user tries to access a model that doesn't exists
                $this->errorReport($controllerFile . "doesn't exists");
            }
        }
        public function profiencyCheck($profiency) {
            $this->loadModel('profiency', true);
            $dbprofiency = $this->model->getData();
            ($profiency != $dbprofiency) ? $this->data['notification'] = "NOTE! " . $profiency . " isn't your current profiency": "";
        }
	}
?>
