<?php
    class farm extends controller {
    
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            
            $this->render('farm', 'Farm', false);
        }
    }
?>