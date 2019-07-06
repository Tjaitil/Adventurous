<?php
    class citycentre extends controller  {
    
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->render('citycentre', 'City Centre', false);
        }
    }
?>