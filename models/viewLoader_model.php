<?php
    class extends controller {
    
        function __construct() {
            parent::__construct();
        }
        public function index() {
            $this->render('', '', false);
        }
    }   
?>