<?php
    class autoloader {
        
        public function libsLoader( $className ) {
            $file = dirname(__DIR__, 1) . '/' . constant('ROUTE_BASE') . $className. '.php';
            if(file_exists($file)) {
                require($file);    
            }
            else {
                return;
            }
        }
        public function modelLoader( $className ) {
            $file = dirname(__DIR__, 1) . '/' . constant('ROUTE_MODEL') . $className. '.php';
            if(file_exists($file)) {
                require($file);    
            }
            else {
                return;
            }
        }
    }
?>