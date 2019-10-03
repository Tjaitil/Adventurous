<?php
    class formhandler {
        public $POST_array;
        public $error = array();
        public $data = array();
        
        function __construct() {
        }
        
        //Check the POST DATA:
        public function checkData($post_array) {
            $this->POST_array = $post_array;
            $error_count = 0;
            foreach($this->POST_array as $key => $value) {
                if (empty($value) || strlen($value) == 0) {
                    $this->error[$key . "Err"] = "Empty!";
                    $error_count++;
                }
            }
            if ($error_count === 0) {
                foreach($this->POST_array as $key => $value) {
                    $this->data[$key] = trim($value);
                }
                return $this->data;
            }
            else if($error_count > 0) {
                return $this->error;
            }
            
        }   
    }
?>