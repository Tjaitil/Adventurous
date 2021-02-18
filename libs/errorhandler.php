<?php
    final class errorhandler {
        protected $errorMail;
        
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
        public function catchAJAX($db, $e) {
            $test = $db->conn->rollBack();
            if(strpos($e->getMessage(), 'inv_amount') !== false) {
                $this->gameMessage("ERROR: Your inventory is full!", true);
            }
            else{
                $this->reportError(array($_SESSION['username'], $e->getFile() . $e->getLine() .$e->getMessage()));
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);    
            }
        }
        public function reportError($errorArray) {
            /*$errorarray will contain following information:
            * [0] -> username;
            * [1] -> writen error message which will contain the error
            * IT call also be the $e from Exception class
            *
            */
            
            if(is_object($errorArray)) {
                $message = $errorArray->getMessage();
            }
            else {
                $message = "Error: " . "username " . $errorArray[0] . ' message: ' .  $errorArray[1];
            }
            
            if(!isset($errorArray['title'])) {
                $title = 'Game error';
            }
            $test = mail('miner123@hotmail.no', $title, $message, 'FROM: <system@adventurous.no');
        }
        public function hello() {
            print "Hello world!";
        }
    }
?>