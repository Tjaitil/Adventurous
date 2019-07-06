<?php
    class bakery extends controller {
    
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->render('bakery', 'Bakery', false);
        }
    }
?>