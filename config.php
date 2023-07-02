<?php
define('APP_NAME', 'Adventurous');
define('HTTP_SERVER', 'http://adventurous.com');
define('URL', 'http://adventurous.com');
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);

const CONFIGURATION = [
    'testing' => $_ENV['TESTING'] ?? false,
    'skip_inventory' => $_ENV['SKIP_INVENTORY'] ?? false,
];
