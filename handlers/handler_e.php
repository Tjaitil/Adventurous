<?php
    require('../libs/handler.php');
    $handler = new handler(true);
    $handler->sessionCheck(true);
    $model = $handler->includeModel('eventLoader', $_SESSION['gamedata']);
    $model->loadEvent($_GET['event']);
?>