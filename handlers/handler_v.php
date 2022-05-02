<?php
    require('../libs/handler.php');
    $handler = new handler(true);
    $handler->sessionCheck(true);
    
    // Switch to map custom building names to views
    switch($_GET['building']) {
        case 'adventure base':
            $_GET['building'] = 'adventures';
            break;
        case 'stockpile desert':
            $_GET['building'] = 'stockpile';
            break;
        default: 
            
            break;
    }
    $controller = $handler->loadController(str_replace(" ", "", $_GET['building']));
    if(is_object($controller)) {
        $controller->index();
    }
    else {
        $errorHandler = new errorhandler();
        $errorHandler->reportError(array($_SESSION['username'], "building not found, " . $_GET['building']));
        http_response_code(404);
        return false;
    }
?>