<?php
    class gameguide extends controller {
        private $url;
        public $guidefile;
        public $defaultfile = 'views/guides/guidepage.php';
        
        function __construct($url) {
            parent::__construct();
            $this->url = $url;
        }
        
        private function checkURL() {
            if(count($this->url) > 1) {
                $this->guidefile = constant('ROUTE_VIEW') . '/guides/' . $this->url[1] . '.php';
                if(!file_exists($this->guidefile)) {
                    $this->guidefile = $this->defaultfile;
                }
            }
            else {
                $this->guidefile = $this->defaultfile;
            }
        }
        public function index() {
            $this->checkURL();
            $this->render('gameguide', 'Gameguide', $this->guidefile);
        }
        public function contentGenerator() {
            
        }
        public function tableGenerator() {
            
        }
    }
?>