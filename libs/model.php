<?php
    class model {
        protected $db;
        protected $UpdateGamedata;
        protected $artefactModel;
        // $updateGamedata is the variable that holds updateGamedata_model if it is being instantiated with $this->loadModel from foo_model
        // $artefactModel is the variable that holds updateGamedata_model if it is being instantiated with $this->loadModel from foo_model
        function __construct() {
            $this->includeDB();
        }
        public function gameMessage($message, $ajax = false) {
            $date = '[' . date("H:i:s") . '] ';
            if($ajax != false) {
                echo $date . $message;
            }
            $_SESSION['gamedata']['log'][] = $date . $message;
            if(count($_SESSION['gamedata']['log']) > 15) {
                unset($_SESSION['gamedata']['log'][0]);
                $_SESSION['gamedata']['log'] = array_values($_SESSION['gamedata']['log']);
            }
        }
        protected function reportError($file, $line, $error_message, $ajax = true) {
            $message = "Error on: " . $file . ' Line ' . $line . ' ' . $error_message . ' ' . $ajax;
            $test = mail('miner123@hotmail.no', 'ERROR', $message, 'FROM: <system@adventurous.no');
        }
        protected function includeDB() {
            if(!isset($this->db)) {
                /*require_once();*/
                $this->db = new database();
            }
        }
        protected function loadModel($model, $directoryUP = true) {
            $model = $model . '_model';
            if(class_exists($model)) {
                return new $model($this->session);
            }
            else {
                $this->reportError('model', false, 'model not found' . $model, false);
            }
        }
    }
?>