<?php
    class bakery extends controller {
        public $data = array();
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('Bakery', true);
            $this->data = $this->model->getData();
            $this->render('bakery', 'Bakery', $this->data, true, true);
        }
    }
?>