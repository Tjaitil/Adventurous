<?php
    class Talk_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function talk($person, $part) {
            $persons = array();
            $persons['Harfen']['0'] = "conv_b|Hello";
            $persons['Harfen']['01'] = "conv_a|Hello, what can I do for you";
            $persons['Harfen']['011'] = 'conv_b|<button onclick="talk(0,\'1\')"> Who are you </button><br><button>Bye</button>';
            $persons['Harfen']['0111'] = "conv_a|I'm Harfen from Towhar, expert of artefacts";

            if($person == '0') {
                $person = $this->session['conversation']['person'];
            }
            if($part == '0') {
                $progress = $this->session['conversation']['progress'] = '0';
            }
            else {
                $progress = $this->session['conversation']['progress'] . $part;    
            }
            
            
            $string = explode(',' ,$progress);
            
            /*$pr = explode($part);
            for($i = 0; $i < count($conversation); $part) {
                $part[$i] = '[' . $part[$i] . ']';
            }*/
            
            if(isset($persons[$person][$progress])) {
                $conversation = $persons[$person][$progress]; 
            }
            else {
                $conversation = $persons[$person]['011'];
            }
            
            /*if(array_key_exists($person, $persons) == false) {
                $this->gameMessage("This person doesn't exists!", true);
                return false;
            }*/
            
            echo $conversation;
            
            $_SESSION['gamedata']['conversation']['person'] = $person;
            $_SESSION['gamedata']['conversation']['progress'] = $progress;
        }
    }
?>