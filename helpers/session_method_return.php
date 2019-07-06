<?php
    function session_method_return ($methodname)  {
        if (method_exists($session, 'setsession')) {
            require('libs/session.php');
        }
        else {
            return false;
        }
    }
?>