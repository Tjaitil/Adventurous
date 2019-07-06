<?php
    function splitText($var, $stringReplace = false, $sign = false, $replacesign = false) {
        if($stringReplace === true) {
            $var = str_replace($sign, $replacesign, $var);
        }
        $newString = explode("|", $var);
        return $newString;
    }
?>