<?php
define('APP_NAME', 'Adventurous');
define('HTTP_SERVER', 'http://adventurous.com');
define('URL', 'http://adventurous.com');
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('TIMEZONE', 'Europe/Oslo');
date_default_timezone_set(TIMEZONE);
ini_set("log_errors", 1);
ini_set("error_log", $_SERVER['DOCUMENT_ROOT'] . "adventurous.log");
