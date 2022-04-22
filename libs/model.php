<?php
    class model {
        protected $db;
        protected $UpdateGamedata;
        protected $ArtefactModel;
        protected $hungerModel;
        protected $errorHandler;
        public $response;
        // $updateGamedata is the variable that holds updateGamedata_model if it is being instantiated with $this->loadModel from foo_model
        // $Artefact_model is the variable that holds updateGamedata_model if it is being instantiated with $this->loadModel from foo_model
        function __construct() {
            $this->includeDB();
            $this->errorHandler = new errorhandler();
            $this->response = new response();
        }
        public function checkHunger() {
            if($this->session['hunger'] > 14) {
                return true;
            } else {
                $this->response->addTo('gameMessage', "Your hunger is too high, please eat!", array("error" => true));
                return false;
            }
        }
        protected function includeDB() {
            if(!isset($this->db)) {
                $this->db = new database();
            }
        }
        protected function commonModels($UpdateGamedata = false, $ArtefactModel = false, $hungerModel = false) {
            // Load common models
            if($UpdateGamedata === true) {
                $this->UpdateGamedata = $this->loadModel('UpdateGamedata', true, true);
            }
            if($ArtefactModel === true) {
                $this->ArtefactModel = $this->loadModel('Artefact', true);
            }
            if($hungerModel === true) {
                $this->hungerModel = $this->loadModel('Hunger', true, true);
            }
        }
        protected function loadModel($model, $directoryUP = true, $db = false) {
            $model = $model . '_model';
            if(class_exists($model)) {
                // $db variable is a check, if it is true the oaded model needs the db class and not the constructor
                if($db === false) {
                    return new $model($_SESSION['gamedata']);    
                }
                else {
                    return new $model($_SESSION['gamedata'], $this->db);    
                }
            }
            else {
                $this->errorHandler->reportError('model', false, 'model not found' . $model, false);
            }
        }
    }
?>