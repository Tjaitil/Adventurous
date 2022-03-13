<?php
    class zinsstore extends controller {
        public $data;
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->render('zinsstore', 'Zins Store', $this->data, true, true);
        }
    }
?>