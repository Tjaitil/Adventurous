<?php
    class session {
        function __construct () {
            session_start();
            if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
            header("location: /adventurous/KB.php");
            exit;
           }
        }
    }
?> 