<?php
    function checkMethod ($class, $methodname)  {
        if (method_exists($class, $methodname)) {
            return true;
        }
        else {
            return false;
        }
    }

?>