<?php

/**
 * @deprecated
 * 
 */
function get_template($name, $data, $up = false, $flag = false)
{
    $filename = $name . '_tpl.php';
    $path = constant('ROUTE_TEMPLATE') . $filename;
    if (file_exists($path)) {
        require($path);
    } else {
        return;
    }
}

function restore_file($file, $up = false)
{
    $filepath = constant('ROUTE_GAMEDATA') . $file . ".json";
    if ($up == true) {
        $filepath = '../' . constant('ROUTE_GAMEDATA') . $file . ".json";
    }
    if (file_exists($filepath)) {
        return (json_decode(file_get_contents($filepath, true), true));
    } else {
        return null;
    }
}

/**
 * Wrapper around TemplateFetcher functionality
 *
 * @param string $name
 * @param array $data
 *
 * @return string;
 */
function fetchTemplate(string $name, array $data = [])
{
    try {
        echo \App\libs\TemplateFetcher::loadTemplate($name, $data);
    } catch (Exception $e) {
        echo "Unable to load template";
    }
}
