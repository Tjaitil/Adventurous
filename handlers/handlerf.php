<?php
    require('../libs/handler.php');
    $handler = new handler(false);
    $handler->sessionCheck();
    $file = $_GET['file'];
    
    switch($file) {
        case 'inventory':
            require('../' . constant('ROUTE_VIEW') . 'inventory.php');
            loadInventory();
            break;
        case 'conversation':
            $controller = $handler->loadController('conversation');
            $controller->getConversation($_GET['page']);
            break;
        default:
            // Log error message
            break;
    }
?>