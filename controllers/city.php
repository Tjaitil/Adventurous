<?php
    class city extends controller {
        protected $city;
        protected $cityfile;
        public $data = array();
        
        function __construct () {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('Main', true);
            $this->loadController('main');
            // Pass the data gathered from the fetch model to then controller to handle the data
            $this->data = $this->controller->calculateCountdowns($this->model->getData(false, false));
            $this->loadModel('Adventures', true);
            $this->data['adventure'] = $this->model->getData();
        
            $this->city = str_replace(" ", "-", $_SESSION['gamedata']['location']);
            $this->cityfile = constant("ROUTE_VIEW") . $this->city . '.php';
            $this->generateGameID();
            if(file_exists($this->cityfile)) {
               $this->render('city', ucfirst($this->city), $this->cityfile);
            }
            else {
               header("Location: /notfound");
               die();
            }
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