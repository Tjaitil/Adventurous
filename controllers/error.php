<?php
    class notfound extends controller {
    
        function __construct () {
            parent::__construct();
        }
    
        function index () {
            $this->render('error', 'Error', false);
        }
    }
?>