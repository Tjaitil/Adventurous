<?php
    class city extends controller {
        protected $city;
        protected $cityfile;
        
        function __construct () {
            parent::__construct();
        }
        
        public function index() {
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