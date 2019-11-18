<?php
    class smithy extends controller {
        public $data;
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('smithy', true);
            $this->data = $this->model->getData();
            $this->render('smithy', 'Smithy', $this->data);
        }
    }
?>