<?php
    class index extends controller {
        function __construct () {
            parent::__construct();
        }
        
        public function index () {
            /*$this->render('index', 'index', false);*/
            header("Location: /login");
            exit();
        }
    }
?>