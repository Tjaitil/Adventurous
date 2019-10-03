<?php
    class model {
        protected $db;
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
        protected function reportError($file, $line, $error_message) {
            $message = "Error on: " . $file . ' Line ' . $line . ' ' . $error_message;
            $test = mail('miner123@hotmail.no', 'ERROR', $message, 'FROM: <system@adventurous.no');
        }
        protected function includeDB() {
            if(!isset($this->db)) {
                /*require_once();*/
                $this->db = new database();
            }
        }
    }
?>