<?php
    class news extends controller {
    
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->render('news', 'News', false);
        }
    }
?>