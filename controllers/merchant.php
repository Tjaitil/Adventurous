<?php 
    class merchant extends controller  {
        public $data;
        
        function __construct() {
            parent::__construct();    
        }
        public function index() {
            $this->loadModel('Merchant', true);
            $this->data = $this->model->getData();
            $this->render('merchant', 'Merchant', $this->data, true, true);
        }
    }
?>