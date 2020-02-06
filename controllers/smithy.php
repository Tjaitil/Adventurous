<?php
    class smithy extends controller {
        public $data;
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('Smithy', true);
            $this->data = $this->model->getData();
            $this->render('smithy', 'Smithy', $this->data);
        }
    }
?>