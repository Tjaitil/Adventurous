<?php
    class maintenance extends controller {
    
        function __construct() {
            parent::__construct();
        }
        
        public function index () {
            $this->render('maintenance', 'Maintenance', false);
        }
    }
?>