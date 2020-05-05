<?php
    require('../libs/handler.php');
    $handler = new handler(true);
    $handler->sessionCheck(true);
    $title = "test";
    include('../views/test.php');
    /*$testy = "hello world";
    $doc = new DOMDocument();
    $doc->loadHTMLFile('../views/test.php');
    echo $doc->saveHTML();*/
    
?>