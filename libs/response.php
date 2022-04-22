<?php 
 final class response {
    public $data = array();
    
    public function addTo($token, $value, $options = false) {
        // $token => specify index in data object
        // $value => $value to be stored

        if(array_search($token, array("errorGameMessage", "gameMessage", "data", "levelUP", "html")) === false) {
            return false;
        }
        switch ($token) {
            case 'gameMessage':
                $this->data['gameMessages'][] = $value;
                $this->gameMessage($value, true);
                break;
            case 'errorGameMessage':
                $message = "ERROR " . $value;
                $this->data['gameMessages'][] = $message;
                $this->gameMessage($message, true);
                break;
            case 'data':
                $this->data[$options['index']] = $value;
                break;
            case 'levelUP':
                $this->data['levelUP'] = $value; 
                break;
            case 'html':
                $this->data['html'][] = $value;
                break;
            default:

            break;
        }
    }
    public function send() {
        if(isset($this->data['html']) && count($this->data['html']) === 1) {
                $this->data['html'] = $this->data['html'][0];
        }
        return json_encode($this->data);
    }
    public function gameMessage($message, $ajax = false) {
        $date = '[' . date("H:i:s") . '] ';
        $messageString = $date . trim($message);
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