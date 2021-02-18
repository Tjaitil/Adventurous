<?php
    require('../libs/handler.php');
    $handler = new handler(false);
    $handler->sessionCheck();
    $log = $_GET['log'];
    $_SESSION['log'][] = $log;
?>