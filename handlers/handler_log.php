<?php
    require('../libs/handler.php');
    $handler = new handler(false);
    $handler->sessionCheck();
    $log = json_decode($_GET['log']);

    for($i = 0; $i < count($log); $i++) {
        $_SESSION['log'][] = $log[$i];
    }
?>