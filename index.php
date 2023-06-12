<?php

use App\libs\App;
use App\libs\Bootstrap;
use App\libs\session;

require __DIR__ . '/vendor/autoload.php';
require('root/routes.php');
require('root/config.php');
require('libs/DependencyContainer.php');
require('config/GameConstants.php');
require(constant('ROUTE_HELPER') . 'model_helpers.php');
App::getInstance()->boot();

$bootstrap = new Bootstrap(new session());
$bootstrap->init();
