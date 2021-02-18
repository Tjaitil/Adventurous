<?php
    class bootstrap {
        private $url = NULL;
        private $controller = NULL;
        private $controllerPath = 'controllers/';
        private $modelPath = 'models/';
        private $errorFile = 'error.php';
        private $errorhandler;
        private $defaultFile = 'index.php';
        public $session;
        
        function __construct ($session) {
            $this->errorhandler = new errorhandler();
            $this->session = $session;
        }
        function init() {
            $this->getURL();
              
            if(in_array($this->url[0], array('gameguide', 'client')) == false && count($this->url) > 1) {
                require(constant('ROUTE_VIEW') . 'error.php');
                return;
            }
            if($this->url[0] === 'logout') {
                $this->session->destroy();
           }
            if(strpos($this->url[0], '-')) {
                $this->url[0] = implode(explode('-', $this->url[0]));
            }
            $this->session->validatelogin();
            if($this->session->status == false && $this->url[0] != 'registration') {
                session_unset();
                $this->controller = 'login';
                $file = $this->controllerPath . $this->controller . '.php';
                require $file;
                $this->controller = new login($this->session);
                $this->controller->index();
                return false;
            }
            $array = array(NULL, 'none');
            $allowed = array('newuser', 'registration', 'gameguide', 'login');
            
            // If the user hasn't chosen a profiency yet, check if the user is trying to access a page that requires profiency,
            // if yes send the user to the location of the new user.
            /*if(in_array($_SESSION['profiency'], $array) != false && in_array($this->url[0], $allowed) == false) {
                header("Location: /newuser");
            }*/
            
           if(empty($this->url[0])) {
                $this->loadDefaultController();
                return false;
           }
           $this->loadExistingController();
        }
        private function getURL () {
             $this->url = $_SERVER['REQUEST_URI'];
             $this->url = ltrim($this->url, '/');
             $this->url = explode("/", $this->url);
        }
        
        private function loadDefaultController() {
            require($this->controllerPath . $this->defaultFile);
            $link = $this->controllerPath . $this->defaultFile;
            $this->controller = new index();
            $this->controller->index();
        }
        
      private function loadExistingController () {
          $file = $this->controllerPath . $this->url[0] . '.php';
          if(file_exists($file)) {
              require $file;
              if($this->url[0] == 'gameguide') {
                  $this->controller = new $this->url[0]($this->url);
                  $this->controller->index();
              }
              else {
                  $this->controller = new $this->url[0]($this->session);
                  $this->controller->index();
              }
          }
          else {
            // Report error
            $this->errorhandler->reportError(array("None", "Controller doesn't exists " . __METHOD__));
          }
      }
    }
?>