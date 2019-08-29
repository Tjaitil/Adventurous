<?php
    class autoloader {
        
        public function libsLoader( $className ) {
            require(dirname(__DIR__, 1) . '/' . constant('ROUTE_BASE') . $className. '.php');
        }
    }
?>