<?php
    class crops extends controller {
        public $data;
        
        function __construct() {
          parent::__construct();
        }
         
        public function index() {
            $this->loadModel('Crops', true);
            $this->data = $this->model->getData();
            $this->render('crops', 'Crops', $this->data);
        }
    }
?>