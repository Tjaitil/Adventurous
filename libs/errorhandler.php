<?php
    final class errorhandler {
        protected $errorMail;
        
        public function gameMessage($message, $ajax = false) {
            $date = '[' . date("H:i:s") . '] ';
            $messageString = $date . $message;
            $_SESSION['log'][] = $messageString;
            if(count($_SESSION['log']) > 15) {
                unset($_SESSION['log'][0]);
                $_SESSION['log'] = array_values($_SESSION['log']);
            }
            if($ajax === false) {
                return $messageString;
            }
            else {
                echo $messageString;
            }
        }
        public function catchAJAX($db, $e) {
            $test = $db->conn->rollBack();
            if(strpos($e->getMessage(), 'inv_amount') !== false) {
                return "Your inventory is full!";
            }
            else{
                $this->reportError(array($_SESSION['username'], $e->getFile() . $e->getLine() .$e->getMessage()));
                return $e->getFile() . $e->getLine() .$e->getMessage();
            }
        }
        public function reportError($errorArray) {
            /*$errorarray will contain following information:
            * [0] -> username;
            * [1] -> writen error message which will contain the error
            * IT call also be the $e from Exception class
            *
            */
            // if(is_object($errorArray)) {
            //     $message = $errorArray->getMessage();
            // }
            // else {
            //     $message = "Error: " . "username " . $errorArray[0] . ' message: ' .  $errorArray[1];
            // }
            
            // if(!isset($errorArray['title'])) {
            //     $title = 'Game error';
            // }
            // $header = "MIME-Version: 1.0" . "\r\n";
            // $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            // $header .= 'From: <system@adventurous.no';
            // $test = mail('miner123@hotmail.no', $title, $message, $header);
        }
    }
?>