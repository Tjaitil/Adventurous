<?php
    class conversation extends controller {
        public $index;
        public $text;
        public $POST;
        function __construct () {
            if(isset($_SESSION['conversation']['conv_index'])) {
                $this->index = $_SESSION['conversation']['conv_index'];    
            }
            else {
                $this->index === false;
            }
        }
        public function getConversation($person) {
            $person = strtolower($person);
            $file = "../gamedata/conversations/" . $person . ".json";
            if(file_exists($file)) {
                // get file and echo JSON file;
                
                /*
                switch($person) {
                    
                }*/
                
                return file_get_contents($file);
            }
            else {
                // Deal with problem, send mail?
                return false;
            }
        }
        public function setPerson($POST, $index = false) {
            // Set new person
            $person = $POST['person'];
            $_SESSION['conversation']['person'] = $person;
            // If the index is false the conversation will begin at the start. If else the conversation will start at specified index
            if($index == false) {
                $this->echoConversation(true);
            }
            else {
                $this->echoConversation();
            }
        }
        public function getNextLine($POST) {
            // Loop through the active_dialogues for the match for text clicked on and make next index
            
            /*
            Last letter in the index determines what type the last interaction was. For example "hfrq2" is answer to question 2
            
            Q - main questions
            q - question
            r - response
            rr - random response
            1-9 - number of response/question
            
            person|conversation|nextIndex|Funcname/other/end
           
           
           */
            $info = false;
            $this->text = $POST['index'];
            if(strpos($this->text, "|")) {
                $text_array = explode("|", $this->text);
                $info = $text_array[0];
                $item_match = false;
                foreach($_SESSION['gamedata']['inventory'] as $key) {
                    if($key['item'] === $info) {
                        $item_match = true;
                        $this->POST = $info;
                        break;
                    }   
                }
                // Find last index
                if($item_match === true) {
                    $conversation = json_decode($this->getConversation($_SESSION['conversation']['person']), true);
                    $this->text = explode("|", $conversation[$this->index])[1];
                }
            }
            // Add check
            $active_dialogues = $_SESSION['conversation']['active_dialogues'];
            for($i = 0; $i < count($active_dialogues); $i++) {
                if(strpos($active_dialogues[$i], $this->text)) {
                    $dialogue_match = explode("|", $active_dialogues[$i]);
                    /*var_dump($dialogue_match);*/
                    if($dialogue_match[2] === "end") {
                        $end = true;
                        break;
                    }
                    if(isset($dialogue_match[4])) {
                        $split = explode("#", $dialogue_match[4]);
                    }
                    else {
                        $this->index = $_SESSION['conversation']['conv_index'] . $dialogue_match[2];
                        /*var_dump($this->index);*/
                    }
                    break;
                }
            }
            if(isset($end)) {
                return "end";
            }
            else if(isset($split)) {
                return array($info, $split);
            }
            else {
                return 'next';
            }
        }
        public function echoConversation($getIndex = false) {
            // Echo the conversation;
            // Get the file containing the conversations with a person
            $conversation = json_decode($this->getConversation($_SESSION['conversation']['person']), true);
            if($conversation == false) {
                return false;
            }
            // If the index is false, it means the index is not set by getNextLine() and setPerson() is called
            if($getIndex === true) {
                $_SESSION['conversation']['conv_index'] = $conversation['index'];
            }
            if(!isset($conversation[$_SESSION['conversation']['conv_index']])) {
                // Find last Q (conversation)
                $strpos = strrpos($_SESSION['conversation']['conv_index'], "Q");
                $_SESSION['conversation']['conv_index'] = substr($_SESSION['conversation']['conv_index'], 0, $strpos +1);
            }
            // Check if the index in $conversation is string, if true make it into array before echoing
            if(is_string($conversation[$_SESSION['conversation']['conv_index']])) {
                $active_conversation = $_SESSION['conversation']['active_dialogues'] =
                    array($conversation[$_SESSION['conversation']['conv_index']]);
            }
            else {
                $active_conversation = $_SESSION['conversation']['active_dialogues'] =
                    $conversation[$_SESSION['conversation']['conv_index']];
            }
            for($i = 0; $i < count($active_conversation); $i++) {
                $for_index = explode("|", $active_conversation[$i]);
                if(isset($for_index[4])) {
                    array_pop($for_index);
                }
                $active_conversation[$i] = implode("|", $for_index);
            }
            // Return false if the length of conversation array is not over 0;
            if(count($active_conversation) > 0) {
                echo json_encode($active_conversation);
            }
            else {
                return false;
            }
        }
}
?>