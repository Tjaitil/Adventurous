<?php
    class travel extends controller {
        
        function __construct () {
            parent::__construct();
        }
        
        function index() {
            $this->render('travel', 'Travel', false);
        }
    }
?>