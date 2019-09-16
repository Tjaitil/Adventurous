<?php
    class mine extends controller  {
        public $data = array();
        public $error;
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('mine', true);
            $this->data = $this->model->getData();
            $this->renderWE('mine', 'Mine', $this->data, $this->error);
        }
    }
?>