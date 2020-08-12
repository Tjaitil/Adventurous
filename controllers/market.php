<?php
    class market extends controller {
        public $data = array();
        public $post_data = array();
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('Market', true);
            $this->data = $this->model->getData();
            $this->render('market', 'Market', $this->data, true);
        }
    }
?>