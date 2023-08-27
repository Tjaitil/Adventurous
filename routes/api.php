<?php

use App\controllers\ArcheryShopController;
use App\controllers\ArmoryController;
use App\controllers\ArmyCampController;
use App\controllers\BakeryController;
use App\controllers\CityCentreController;
use App\controllers\CropsController;
use App\controllers\HungerController;
use App\controllers\InventoryController;
use App\controllers\MineController;
use App\controllers\ProfiencyStatusController;
use App\controllers\SmithyController;
use App\controllers\StockpileController;
use App\controllers\UserLevelsController;
use App\controllers\WorldLoaderController;
use App\controllers\ZinsstoreController;
use App\libs\Response;
use App\libs\Route;
use App\libs\Router;
use App\controllers\TraderAssignmentController;
use App\controllers\TravelBureauController;

require(ROUTE_ROOT . 'vendor/autoload.php');

$uri = urldecode(str_replace('/api', '',  $_SERVER['REQUEST_URI']));
Response::clear();
http_response_code(200);


Route::get('/world', [WorldLoaderController::class, "loadWorld"]);
Route::post('/world/change', [WorldLoaderController::class, "changeMap"]);

Route::get('/inventory', [InventoryController::class, "get"]);
Route::get('/inventory/prices', [InventoryController::class, "getPrices"]);

Route::post('/crops/start', [CropsController::class, "growCrops"]);
Route::post('/crops/end', [CropsController::class, "updateCrops"]);
Route::post('/crops/generate', [CropsController::class, "generateSeed"]);
Route::get('/crops/data', [CropsController::class, "getData"]);
Route::get('/crops/countdown', [CropsController::class, "getCountdown"]);

Route::get('/mine/countdown', [MineController::class, "getCountdown"]);
Route::post('/mine/start', [MineController::class, "start"]);
Route::post('/mine/end', [MineController::class, "endMining"]);
Route::get('/mine/data', [MineController::class, "getViewData"]);

Route::get('/stockpile/{item}', [StockpileController::class, "show"]);
Route::post('/stockpile/update', [StockpileController::class, "update"]);

Route::post('/zinsstore', [ZinsstoreController::class, "buy"]);

Route::get('/merchant/{item}/price', [MerchantController::class]);
Route::get('/merchant/countdown', [MerchantController::class]);
Route::get('/merchant/store', [MerchantController::class]);
Route::post('/merchant/trade', [MerchantController::class]);

Route::get('/trader/assignments', []);
Route::post('/trader/assignment/new', [TraderAssignmentController::class, "newAssignment"]);
Route::post('/trader/assignment/update', [TraderAssignmentController::class, "updateAssignment"]);
Route::post('/trader/assignment/countdown', [TraderAssignmentController::class, "getAssignmentCountdown"]);

Route::post('/smithy/smith', [SmithyController::class, "smithItem"]);
Route::get('/smithy', [SmithyController::class, "get"]);

Route::get('/archeryshop/get', [ArcheryShopController::class, "getItems"]);
Route::post('/archeryshop/fletch', [ArcheryShopController::class, "fletchItem"]);

Route::post('/travelbureau/buy', [TravelBureauController::class, "buyCart"]);
Route::get('/travelbureau/store', [TravelBureauController::class, "getStoreItems"]);

Route::post('/armory/add', [ArmoryController::class, "add"]);
Route::post('/armory/remove', [ArmoryController::class, "remove"]);

Route::get('/bakery', [BakeryController::class, "index"]);
Route::get('/bakery/get', [BakeryController::class, "get"]);
Route::post('/bakery/make', [BakeryController::class, "makeItem"]);
Route::get('/bakery/store', [BakeryController::class, "getStoreItems"]);

Route::post('/tavern/recruit', [TavernController::class, "recruitPersonell"]);

Route::get('/hunger/get', [HungerController::class, "getHunger"]);
Route::get('/hunger/item/get', [HungerController::class, "getHealData"]);
Route::post('/hunger/restore', [HungerController::class, "restoreHealth"]);

Route::get('/citycentre/upgradeEfficiency', [CityCentreController::class, "upgradeEfficiency"]);

Route::get('/profiencystatus/get', [ProfiencyStatusController::class, "getStatuses"]);

Route::get('/worldloader', [WorldLoaderController::class, "loadWorld"]);
Route::post('/worldloader/change', [WorldLoaderController::class, "changeMap"]);

Route::get('/armycamp/warriors', [ArmyCampController::class, "get"]);
Route::post('/armycamp/transfer', [ArmyCampController::class, "transferWarriors"]);
Route::post('/armycamp/toggleRest', [ArmyCampController::class, "toggleWarriorsRest"]);
Route::post('/armycamp/upgradeWarrior', [ArmyCampController::class, "upgradeWarriorLevel"]);
Route::post('/armycamp/changeWarriorType', [ArmyCampController::class, "changeWarriorType"]);
Route::post('/armycamp/startTraining', [ArmyCampController::class, "startTraining"]);
Route::post('/armycamp/endTraining', [ArmyCampController::class, "endTraining"]);
Route::post('/armycamp/healWarrior', [ArmyCampController::class, "healWarrior"]);

Route::get('/userlevels', [UserLevelsController::class, "getLevels"]);

$match = Router::getInstance()->matchRoute($_SERVER['REQUEST_METHOD'], $uri);

echo Response::get();
