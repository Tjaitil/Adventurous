<?php

namespace App\libs;

use \Exception;

class TemplateFetcher
{

    public static function loadTemplate(string $name, array $data = [])
    {
        $filename = $name . '_tpl.php';
        $path = constant('ROUTE_TEMPLATE') . $filename;
        if (file_exists($path)) {
            ob_start();
            require($path);
        } else {
            throw new Exception("Unable to load template " . $name . " from " . $path);
        }

        return ob_get_clean();
    }
}
