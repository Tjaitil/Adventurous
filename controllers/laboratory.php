<?php
    class laboratory extends controller {
    
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->render('laboratory', 'Laboratory', false);
        }
    }
?>