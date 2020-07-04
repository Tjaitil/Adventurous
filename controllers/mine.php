<?php
    class mine extends controller  {
        public $data = array();
        function __construct() {
            parent::__construct();
        }
        public function index() {
            $this->loadModel('Mine', true);
            $this->data = $this->model->getData();
            $this->render('mine', 'Mine', $this->data, true);
        }
    }
?>