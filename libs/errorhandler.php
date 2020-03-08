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
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);    
            }
        }
        protected function reportError($file, $line, $error_message, $ajax = true,  $title = false) {
            $message = "Error on: " . $file . ' Line ' . $line . ' ' . $error_message . ' ' . ' ajax= ' . $ajax;
            var_dump($error_message);
            if($title === false) {
                $title = 'FROM: <system@adventurous.no';
            }
            $test = mail('miner123@hotmail.no', 'ERROR', $message, 'FROM: <system@adventurous.no');
        }  
    }
?>