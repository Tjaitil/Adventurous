<?php

use App\libs\App;
use App\libs\Bootstrap;
use App\libs\session;

require __DIR__ . '/vendor/autoload.php';
require_once('root/routes.php');
require_once('root/config.php');
require_once('config/GameConstants.php');
require_once(constant('ROUTE_HELPER') . 'model_helpers.php');
App::getInstance()->boot();

$bootstrap = new Bootstrap(new session());
$bootstrap->init();
