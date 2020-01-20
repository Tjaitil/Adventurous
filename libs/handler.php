<?php
    class handler {
        private $parent_dir = '../';
        function __construct($model = false) {
            require($this->parent_dir . '/root/db.php');
            require_once($this->parent_dir . '/root/routes.php');
            require_once($this->parent_dir . 'root/autoloader.php');
            $autoloader = new autoloader();
            spl_autoload_register(array($autoloader, 'libsLoader'));
            spl_autoload_register(array($autoloader, 'modelLoader'));
            require_once($this->parent_dir . constant('ROUTE_HELPER') . 'general_helpers.php');
            
            // If ajax call is to get a file or session data
            if($model === true) {
                require_once($this->parent_dir . constant('ROUTE_HELPER') . 'model_helpers.php');
            }
        }
        public function sessionCheck() {
            if(!isset($_SESSION)) {
              session_start();
            }
        }
        public function includeModel($modelname, $session) {
            $modelloc = $modelname . '_model.php';
            $model = $modelname . '_model';
            if(class_exists($model)) {
                $session['username'] = $_SESSION['username'];
                return new $model($session, new database());
            }
            else {
                // Report if user tries to access a model that doesn't exists
                $this->errorReport($model . "doesn't exists");
            }
        }
        public function checkMethod($model, $method) {
            if(method_exists($model, $method)) {
                return true;
            }
            else {
                // Report if user tries to access a method that doesn't exists
                $this->errorReport($method . " doesn't exists in" . get_class($model));
            }
        }
        private function errorReport($message) {
            mail('miner123@hotmail.no', 'ERROR', $message, 'FROM: <system@adventurous.no');
        }
    }
?>