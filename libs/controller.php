<?php
    class controller {
        
        function __construct() {
            include_once('root/config.php');
        }
    
        
        // Render site
        public function render($name, $title, $gamedata) {
            require(constant('ROUTE_VIEW') . $name . '.php');
        }
 
        //Render site with error Array   
        public function renderWE($name, $title, $gamedata, $data) {
            $title = $title;
            require(constant('ROUTE_VIEW') . $name . '.php');
        }
        
        public function checkLevel() {
            $this->loadModel('checkLevel', true);
            $xp_nextlevel = $this->model->getData($_SESSION['gamedata']['profiency_level']);
            if ($_SESSION['gamedata']['profiency_xp'] >= $xp_nextlevel) {
                $_SESSION['gamedata']['levelUp'] = true;
            }
            else {
                $_SESSION['gamedata']['levelUp'] = false;
            }
        }
        
        public function loadModel($name, $username) {
            $modelPath = constant("ROUTE_MODEL");
            $path = $modelPath . $name.'_model.php';
            if (file_exists($path)) {
                require $modelPath .$name.'_model.php';
                $modelName = $name . '_model';
                if(in_array($modelName, array('newuser_model', 'gamedata_model')) == true) {
                    $this->model = new $modelName($_SESSION['username']);
                }
                else {
                switch($username) {
                    case true:
                        $this->model = new $modelName($_SESSION['username'], $_SESSION['gamedata']);
                        break;
                    case false:
                        $this->model = new $modelName();
                        break;
                }
                }
            }
            else {
                header("Location: /maintenance");
            }
        }
        
        public function profiencyCheck($profiency) {
            $this->loadModel('profiency', true);
            $dbprofiency = $this->model->getData();
            ($profiency != $dbprofiency) ? $this->data['notification'] = "NOTE! " . $profiency . " isn't your current profiency": "";
        }
	}
?>
