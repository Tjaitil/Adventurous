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
?>