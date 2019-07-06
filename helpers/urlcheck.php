<?php
    function urlcheck() {
        $url = $_SERVER['REQUEST_URI'];
        $url = ltrim($url, '/');
        $url = explode("/", $url);
    
        $page = basename($_SERVER['PHP_SELF']);
        $page = str_replace('.php', '', $page);
        
        if($url[0] == $page) {
            header("Location: /main");        
        }
        return $url;
    }
?>