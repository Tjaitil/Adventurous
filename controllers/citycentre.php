<?php
    class citycentre extends controller  {
        public $data = array();
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('CityCentre', true);
            $this->data = $this->model->getData();
            $this->render('citycentre', 'City Centre', $this->data);
        }
    }
?>