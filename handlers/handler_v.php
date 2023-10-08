<?php

use App\controllers\ArcheryShopController;
use App\controllers\ArmoryController;
use App\controllers\ArmyCampController;
use App\controllers\ArmymissionsController;
use App\controllers\BakeryController;
use App\controllers\CityCentreController;
use App\controllers\CropsController;
use App\controllers\MerchantController;
use App\controllers\MineController;
use App\controllers\SmithyController;
use App\controllers\StockpileController;
use App\controllers\TravelBureauController;
use App\controllers\WorkforceLodgeController;
use App\controllers\ZinsstoreController;
use App\libs\database;
use App\libs\DependencyContainer;
use App\libs\handler;
use App\libs\Logger;
use App\libs\App;
use App\libs\session;

$root = $_SERVER["PWD"] ?? dirname(__FILE__, 2) . '/';

require($root . '/root/routes.php');
require_once(ROUTE_ROOT . '/vendor/autoload.php');

App::getInstance()->boot();
new handler();
$session = new session();

database::getInstance()->openConn();

$building = isset($_GET['building']) ? strtolower($_GET['building']) : null;

// Switch to map custom building names to views
switch ($building) {
    case 'adventure base':
        $building = 'adventures';
        break;
    case 'stockpile desert':
        $building = 'stockpile';
        break;
    case 'merchant desert':
        $building = 'merchant';
        break;
    case 'travel bureau':
        $building = 'travelbureau';
        break;
    default:

        break;
}

$controller_mapping = [
    'armycamp' => ArmyCampController::class,
    'stockpile' => StockpileController::class,
    'merchant' => MerchantController::class,
    'crops' => CropsController::class,
    'mine' => MineController::class,
    'citycentre' => CityCentreController::class,
    'armory' => ArmoryController::class,
    'armymission' => ArmymissionsController::class,
    'archeryshop' => ArcheryShopController::class,
    'smithy' => SmithyController::class,
    'trader' => MerchantController::class,
    'travelbureau' => TravelBureauController::class,
    'bakery' => BakeryController::class,
    'zinsstore' => ZinsstoreController::class,
    'workforcelodge' => WorkforceLodgeController::class,
];

if (is_null($building) || !array_key_exists($building, $controller_mapping)) {
    Logger::log("Building not found: $building");
    http_response_code(422);
} else {
    $dependencyContainer = DependencyContainer::getInstance();
    $controller = $dependencyContainer->get($controller_mapping[$building]);
    $controller->index(...$dependencyContainer->getMethodParameters($controller, 'index'));
    http_response_code(200);
}
