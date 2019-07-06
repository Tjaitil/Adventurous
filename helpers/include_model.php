<?php
    function include_model ($username, $modelname, $session, $directoryup) {
        $modelloc = $modelname . '_model.php';
        $model = $modelname . '_model';
        if ($directoryup == true) {
            $file =  '../' . constant('ROUTE_MODEL') . $modelloc; 
        }
        else if ($directoryup == false) {
            $file = constant('ROUTE_MODEL') . $modelloc;
        }
        require($file);
        if(class_exists($model)) {
            if($username === true) {
                $username = $_SESSION['username'];
                return new $model($username, $session);
            }
            else if ($username === false) {
                return new $model($session);
            }
        }
        else {
            return false;
        }

    }
?>