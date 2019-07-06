<?php
    class handler {
        
        function __construct($model = false) {
            require('../root/routes.php');
            require('../' . constant('ROUTE_HELPER') . 'general_helpers.php');
            if($model = true) {
                require('../' . constant('ROUTE_BASE') . 'ajaxexception.php');
                require('../' . constant('ROUTE_BASE') . 'model.php');
                require('../' . constant('ROUTE_HELPER') . 'model_helpers.php');
            }
        }
        
        public function sessionCheck() {
            if(!isset($_SESSION)) {
              session_start();
            }
        }
        
        public function checkMethod($model, $method) {
            if (method_exists($model, $method)) {
                return true;
            }
            else {
                return false;
            }
        }
        
        public function includeModel ($username, $modelname, $session, $directoryup) {
            $modelloc = $modelname . '_model.php';
            $model = $modelname . '_model';
            if ($directoryup == true) {
                $file =  '../' . constant('ROUTE_MODEL') . $modelloc; 
            }
            else if ($directoryup == false) {
                $file = constant('ROUTE_MODEL') . $modelloc;
            }
            require($file);
            if(class_exists($model)) {
                if($username === true) {
                    $username = $_SESSION['username'];
                    return new $model($username, $session);
                }
                else if ($username === false) {
                    return new $model($session);
                }
            }
            else {
                return false;
            }
    
        }
    }
?>