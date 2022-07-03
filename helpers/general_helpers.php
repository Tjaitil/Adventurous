<?php
    function jsecho($data) {
        $count = count($data);
        $i = 0;
        foreach($data as $key) {
            $i++;
            if($i !== $count) {
                echo $key . "|";
            }
            else {
                echo $key;
            }
        }
    }
    function jsforeach($data) {
        foreach($data as $key) {
            foreach($key as $subkey) {
                echo $subkey . '|';
            }
            echo '|';
        }
    }
    function get_template($name, $data, $up = false, $flag = false) {
        $filename = $name . '_tpl.php';
        $path = constant('ROUTE_TEMPLATE') . $filename;
        if($up == true) {
            $path = '../' . constant('ROUTE_TEMPLATE') . $filename;   
        }
        if(file_exists($path)) {
            require($path);
        }
        else {
            return;
        }
    }
    function store_file($file, $arr) {
        $filepath = constant('ROUTE_GAMEDATA') . $file . ".json";
        if(file_exists($filepath)) {
            file_put_contents($filepath, json_encode($arr));
        }
        else {
            file_put_contents($filepath, json_encode($arr));
        }
    }
    function restore_file($file, $up = false) {
        $filepath = constant('ROUTE_GAMEDATA') . $file . ".json";
        if($up == true) {
            $filepath = '../' . constant('ROUTE_GAMEDATA') . $file . ".json";
        }
        if(file_exists($filepath)) {
            return (json_decode(file_get_contents($filepath, true), true));
        }
        else {
            return null;
        }
    }
?>