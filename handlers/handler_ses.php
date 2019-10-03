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
        default:
            break;
    }
    if(is_array($session_variable) == true) {
        jsecho($session_variable);
        /*if(is_array($session_variable) == true) {
            jsforeach($session_variable);
        }
        else {
            jsecho($session_variable);
        }*/
    }
    else {
        echo $session_variable;
    }
    

?>