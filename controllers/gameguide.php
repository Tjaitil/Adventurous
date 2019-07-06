<?php
    class gameguide extends controller {
    
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->render('Gameguide', 'gameguide', false);
        }
    }
?>