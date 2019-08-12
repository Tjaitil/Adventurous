<?php
    class test extends controller {
    
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->render('test', 'Test', false);
        }
    }
?>