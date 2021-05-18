<?php
    class ArcheryShop extends controller {
        public $data = array();
        
        function __construct() {
            parent::__construct();
        }
        public function index() {
            $this->loadModel('ArcheryShop', true);
            $this->data = $this->model->getData();
            $this->render('archeryshop', 'Archery Shop', $this->data, true, true);
        }   
    }
?>