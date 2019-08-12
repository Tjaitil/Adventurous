<?php
    class city extends controller {
        protected $city;
        protected $cityfile;
        
        function __construct () {
            parent::__construct();
        }
        
        public function index() {
            var_dump($_SESSION['gamedata']['location']);
             $this->city = $_SESSION['gamedata']['location'];
            
             $this->cityfile = constant("ROUTE_VIEW") . $this->city . '.php';
             if(file_exists($this->cityfile)) {
                $this->render('city', ucfirst($this->city), $this->cityfile);
             }
             else {
                
             }

        }
    }
?>