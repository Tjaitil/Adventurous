<?php
    class miningarea extends controller {
    
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->render('miningarea', 'Mining Area', false);
        }
    }
?>