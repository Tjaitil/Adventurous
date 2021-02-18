<?php
    class advclient extends controller {
        protected $city;
        protected $cityfile;
        public $data = array();
        
        function __construct () {
            parent::__construct();
        }
        public function index() {
            $this->loadModel('SidebarUpdater', true);
            $this->data = $this->model->calculateCountdowns(false);
            $this->loadModel('Adventures', true);
            $this->data['adventure'] = $this->model->getData();
            $this->generateGameID();
            $this->render('advclient', $_SESSION['gamedata']['location'], false, false);
        }
        private function generateGameID() {
            $str = 0;
            for($i = 0; $i < 8; $i++) {
                $str .= rand(0,9);
            }
            $_SESSION['gameid'] = $str;
        }
    }
?>