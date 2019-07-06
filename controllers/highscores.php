<?php
    class highscores extends controller {
        public $data;
        
        function __construct () {
            parent::__construct();
        }
        public function index() {
            $this->loadModel('Highscores', true);
            $this->data = $this->model->getData();
            $this->render('highscores', 'Highscores', $this->data);
        }
    }
?>