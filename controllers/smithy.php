<?php
    class smithy extends controller {
        public $data;
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->render('smithy', 'Smithy', $this->data);
        }
    }
?>