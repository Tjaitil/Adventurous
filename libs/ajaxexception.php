<?php
    class ajaxexception {
        
        function __construct ($file, $line, $error_message) {
            $this->errorMessage($file, $line, $error_message);
        }
        
        protected function errorMessage($file, $line, $error_message) {
            $message = "Error on: " . $file . ' Line ' . $line . ' ' . $error_message;
            $test = mail('miner123@hotmail.no', 'ERROR', $message, 'FROM: <system@adventurous.no');
            /*error_log($this->getMessage, 0);*/
        }
    }
?>