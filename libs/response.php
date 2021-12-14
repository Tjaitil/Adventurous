<?php 
 final class response {
    public $data = array();
    public function addToResponse($token, $value, $options = false) {
        // $token => specify index in data object
        // $value => $value to be stored
        switch ($token) {
            case 'gameMessage':
                $this->data['gameMessages'][] = $value;
                if($options['error']) {
                    $error = "ERROR";
                } else {
                    $error = "";
                }
                $this->gameMessage(trim($error . $value), true);
                break;
            
            default:
                # code...
                break;
        }
    }
    public function send() {
        echo json_encode($this->data);
    }
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
    }
 }
?>