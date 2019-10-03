<?php
    require('../libs/handler.php');
    $handler = new handler(false);
    $handler->sessionCheck();
    $file = $_GET['file'];
    
    switch($file) {
        case 'inventory':
            require('../' . constant('ROUTE_VIEW') . 'inventory.php');
            url($_GET['page']);
            break;
        
    }
?>