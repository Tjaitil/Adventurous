<?php
    require('../libs/handler.php');
    $handler = new handler(false);
    $handler->sessionCheck();
    $variable = $_GET['variable'];
    
    $session_variable = $_SESSION['gamedata'][$variable];
    
    switch($variable) {
        case 'log':
            $_SESSION['gamedata']['game_message'] = '1';
            break; 
        case 'skill':
            
            break;
        default:
            break;
    }
    json_encode($session_variable);
