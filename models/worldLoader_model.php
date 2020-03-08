<?php
    class worldLoader_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function JSONfiles() {
            $file = restore_file('pixela', true);
            $string = file_get_contents('../gamedata/pixela.json');
            echo $string;
            /*print_r(json_decode($file, true));*/
        }
    }
?>