<?php
    class model {
        protected $db;
        protected $UpdateGamedata;
        protected $ArtefactModel;
        protected $errorHandler;
        // $updateGamedata is the variable that holds updateGamedata_model if it is being instantiated with $this->loadModel from foo_model
        // $Artefact_model is the variable that holds updateGamedata_model if it is being instantiated with $this->loadModel from foo_model
        function __construct() {
            $this->includeDB();
            $this->errorHandler = new errorhandler();
        }
        public function gameMessage($message, $ajax = false) {
            $date = '[' . date("H:i:s") . '] ';
            if($ajax != false) {
                echo $date . $message;
            }
            $_SESSION['log'][] = $date . $message;
            if(count($_SESSION['log']) > 15) {
                unset($_SESSION['log'][0]);
                $_SESSION['log'] = array_values($_SESSION['log']);
            }
        }
        protected function includeDB() {
            if(!isset($this->db)) {
                /*require_once();*/
                $this->db = new database();
            }
        }
        protected function commonModels($UpdateGamedata = false, $ArtefactModel = false) {
            // Load common models
            if($UpdateGamedata === true) {
                $this->UpdateGamedata = $this->loadModel('UpdateGamedata', true);
            }
            if($ArtefactModel === true) {
                $this->ArtefactModel = $this->loadModel('Artefact', true);
            }
        }
        protected function loadModel($model, $directoryUP = true) {
            $model = $model . '_model';
            if(class_exists($model)) {
                return new $model($this->session, $this->db);
            }
            else {
                $this->reportError('model', false, 'model not found' . $model, false);
            }
        }
    }
?>