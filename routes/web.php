<?php

use App\Http\Controllers\AdvclientController;
use App\Http\Controllers\ArcheryShopController;
use App\Http\Controllers\ArmoryController;
use App\Http\Controllers\ArmyCampController;
use App\Http\Controllers\BakeryController;
use App\Http\Controllers\CityCentreController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\CropsController;
use App\Http\Controllers\GameLoggerController;
use App\Http\Controllers\HungerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MineController;
use App\Http\Controllers\ProfiencyStatusController;
use App\Http\Controllers\SkillsController;
use App\Http\Controllers\SmithyController;
use App\Http\Controllers\StockpileController;
use App\Http\Controllers\TavernController;
use App\Http\Controllers\TraderAssignmentController;
use App\Http\Controllers\TravelBureauController;
use App\Http\Controllers\WorkforceLodgeController;
use App\Http\Controllers\WorldLoaderController;
use App\Http\Controllers\ZinsstoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [LandingController::class, 'index']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/authenticate', [LoginController::class, 'authenticate']);

Route::middleware(['auth'])->group(function () {
    Route::get('/main', [AdvclientController::class, 'index'])->name('main');

    Route::get('/advclient', [AdvclientController::class, 'index'])->name('clent');
    Route::get('/world', [WorldLoaderController::class, 'loadWorld']);
    Route::post('/world/change', [WorldLoaderController::class, 'changeMap']);

    Route::get('/inventory/items', [InventoryController::class, 'getItems']);
    Route::get('/inventory/prices', [InventoryController::class, 'getPrices']);

    Route::post(('conversation/next'), [ConversationController::class, 'index']);

    Route::get('/profile', fn () => view('profile'));
    Route::get('/crops', [CropsController::class, 'index']);
    Route::post('/crops/start', [CropsController::class, 'growCrops']);
    Route::post('/crops/end', [CropsController::class, 'harvestCrops']);
    Route::post('/crops/collect-seeds', [CropsController::class, 'collectSeeds']);
    Route::get('/crops/data', [CropsController::class, 'getViewData']);
    Route::get('/crops/countdown', [CropsController::class, 'getCountdown']);

    Route::get('/mine', [MineController::class, 'index']);
    Route::get('/mine/countdown', [MineController::class, 'getCountdown']);
    Route::post('/mine/start', [MineController::class, 'start']);
    Route::post('/mine/end', [MineController::class, 'endMining']);
    Route::get('/mine/data', [MineController::class, 'getViewData']);

    Route::get('/stockpile', [StockpileController::class, 'index']);
    Route::get('/stockpile/{item}', [StockpileController::class, 'show']);
    Route::post('/stockpile/update', [StockpileController::class, 'update']);

    Route::get('/zinsstore', [ZinsstoreController::class, 'index']);
    Route::get('/zinsstore/store', [ZinsstoreController::class, 'getStoreItems']);
    Route::post('/zinsstore/sell', [ZinsstoreController::class, 'sellItem']);

    Route::get('/merchant/{item}/price', [MerchantController::class]);
    Route::get('/merchant/countdown', [MerchantController::class]);
    Route::get('/merchant/store/items', [MerchantController::class, 'getStoreItems']);
    Route::get('/merchant/store', [MerchantController::class, 'getStore']);
    Route::post('/merchant/trade/open', [MerchantController::class, 'tradeOpen']);
    Route::post('/merchant/trade', [MerchantController::class, 'trade']);

    Route::get('/trader/assignments', []);
    Route::post('/trader/assignment/new', [TraderAssignmentController::class, 'newAssignment']);
    Route::post('/trader/assignment/update', [TraderAssignmentController::class, 'updateAssignment']);
    Route::post('/trader/assignment/countdown', [TraderAssignmentController::class, 'getAssignmentCountdown']);

    Route::get('/smithy', [SmithyController::class, 'index']);
    Route::post('/smithy/smith', [SmithyController::class, 'smithItem']);
    Route::get('/smithy/store', [SmithyController::class, 'getStoreItems']);

    Route::get('/archeryshop', [ArcheryShopController::class, 'index']);
    Route::get('/archeryshop/get', [ArcheryShopController::class, 'getStoreItems']);
    Route::post('/archeryshop/fletch', [ArcheryShopController::class, 'fletchItem']);

    Route::get('/travelbureau', [TravelBureauController::class, 'index']);
    Route::post('/travelbureau/buy', [TravelBureauController::class, 'buyCart']);
    Route::get('/travelbureau/store', [TravelBureauController::class, 'getStoreItems']);

    Route::get('/bakery', [BakeryController::class, 'index']);
    Route::get('/bakery/get', [BakeryController::class, 'get']);
    Route::post('/bakery/make', [BakeryController::class, 'makeItem']);
    Route::get('/bakery/store', [BakeryController::class, 'getStoreItems']);

    Route::post('/tavern/recruit', [TavernController::class, 'recruitPersonell']);

    Route::get('/hunger/get', [HungerController::class, 'getHunger']);
    Route::get('/hunger/item/get', [HungerController::class, 'getHealData']);
    Route::post('/hunger/restore', [HungerController::class, 'restoreHealth']);

    Route::get('/citycentre/upgradeEfficiency', [CityCentreController::class, 'upgradeEfficiency']);

    Route::get('/profiencystatus/get', [ProfiencyStatusController::class, 'getStatuses']);

    Route::get('/worldloader', [WorldLoaderController::class, 'loadWorld']);
    Route::post('/worldloader/change', [WorldLoaderController::class, 'changeMap']);

    Route::get('/armycamp/warriors', [ArmyCampController::class, 'get']);
    Route::post('/armycamp/transfer', [ArmyCampController::class, 'transferWarriors']);
    Route::post('/armycamp/toggleRest', [ArmyCampController::class, 'toggleWarriorsRest']);
    Route::post('/armycamp/upgradeWarrior', [ArmyCampController::class, 'upgradeWarriorLevel']);
    Route::post('/armycamp/changeWarriorType', [ArmyCampController::class, 'changeWarriorType']);
    Route::post('/armycamp/startTraining', [ArmyCampController::class, 'startTraining']);
    Route::post('/armycamp/endTraining', [ArmyCampController::class, 'endTraining']);
    Route::post('/armycamp/healWarrior', [ArmyCampController::class, 'healWarrior']);

    Route::post('/skills/update', [SkillsController::class, 'handleUpdate']);

    Route::get('/workforcelodge', [WorkforceLodgeController::class, 'index']);
    Route::post('/workforcelodge/efficiency/upgrade', [WorkforceLodgeController::class, 'upgradeEfficiency']);

    Route::post('/log', [GameLoggerController::class, 'log']);

    Route::get('/armory', [ArmoryController::class, 'index']);
    Route::get('/armory/soldiers', [ArmoryController::class, 'getSoldiers']);
    Route::post('/armory/soldier/remove', [ArmoryController::class, 'remove']);
    Route::post('/armory/soldier/add', [ArmoryController::class, 'add']);
    Route::post('/log/error', [LogController::class, 'logFrontendError']);
});

Route::fallback(function () {
    abort(404);
});
