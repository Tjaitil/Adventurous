<?php

namespace App\Http\Controllers;

use App\Enums\GameEvents;
use App\Enums\SkillNames;
use App\Exceptions\JsonException;
use App\Http\Builders\SkillsBuilder;
use App\Http\Responses\AdvResponse;
use App\Models\Crop;
use App\Models\Farmer;
use App\Models\FarmerWorkforce;
use App\Models\User;
use App\Services\CountdownService;
use App\Services\GameLogService;
use App\Services\HungerService;
use App\Services\InventoryService;
use App\Services\LocationService;
use App\Services\SessionService;
use App\Services\SkillsService;
use App\Updaters\UserLevelsUpdater;
use Carbon\Carbon;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CropsController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService,
        private CountdownService $countdownService,
        private SessionService $sessionService,
        private HungerService $hungerService,
        private SkillsService $skillsService,
        private LocationService $locationService
    ) {}

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $action_items = Crop::all()->sortBy('farmer_level');
        $workforce_data = FarmerWorkforce::where('user_id', Auth::user()->id)
            ->first()
            ?->toArray();

        return view('crops')
            ->with('title', 'Crops')
            ->with('action_items', $action_items)
            ->with('workforce_data', $workforce_data);
    }

    public function getViewData(): JsonResponse
    {
        $Crop = Crop::all();
        $Workforce = FarmerWorkforce::where('user_id', Auth::user()->id)->first();
        $Farmer = Farmer::where('user_id', Auth::user()->id)
            ->where('location', $this->sessionService->getCurrentLocation())
            ->first();

        return response()->json([
            'crops' => $Crop,
            'workforce' => $Workforce,
            'farmer' => $Farmer,
        ]);
    }

    /**
     * Get countdown for one location
     */
    public function getCountdown(): JsonResponse
    {
        $Farmer = Farmer::where('user_id', Auth::user()->id)
            ->where('location', $this->sessionService->getCurrentLocation())
            ->first();

        if (! $Farmer instanceof Farmer) {
            throw new JsonException(Farmer::class.' model could not be retrieved for user');
        }

        return response()->json([
            'crop_finishes_at' => $Farmer->crop_finishes_at?->timestamp,
            'crop_type' => $Farmer->crop_type,
        ]);
    }

    /**
     * Grow new crops
     */
    public function growCrops(#[CurrentUser] User $User, Request $request): AdvResponse|JsonResponse
    {
        // Get data and validate
        $crop_type = strtolower($request->input('crop_type'));
        $workforce = $request->integer('workforce_amount');
        $User = Auth::user();
        $Inventory = $User->inventory;
        $validator = Validator::make($request->all(),
            [
                'crop_type' => 'required|string',
                'workforce_amount' => 'required|integer',
            ]);

        if ($validator->fails()) {
            return advResponse([])
                ->addMessage(
                    GameLogService::addErrorLog('Information provided is not valid')
                );
        }

        $location = Auth::user()->player?->location ?? '';

        // Check if user is in right location
        if (! $this->locationService->isCropsLocation($location)) {
            return advResponse([])
                ->addMessage(
                    GameLogService::addErrorLog('You are in the wrong location to grow crops')
                );
        } elseif ($this->hungerService->isHungerTooLow($User->hunger)) {
            return $this->hungerService->logHungerTooLow();
        }

        $Farmer = Farmer::where('user_id', Auth::user()->id)
            ->where('location', $location)
            ->first();

        if (! $Farmer instanceof Farmer) {
            throw new JsonException(Farmer::class.' model could not be retrieved for user');
        }

        $Crop = Crop::where('crop_type', $crop_type)->first();

        // Check if crop is correct
        if (! $Crop instanceof Crop) {
            return advResponse([], 422)
                ->addMessage(
                    GameLogService::addErrorLog('Unvalid crop')
                );
        } elseif ($Crop->location !== $location) {
            return advResponse([], 422)
                ->addMessage(
                    GameLogService::addErrorLog('You are in the wrong location to grow this crop')
                );
        } elseif (! $this->skillsService->hasRequiredLevel($User->userLevels, $Crop->farmer_level, SkillNames::FARMER->value)) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('You have too low farmer level'));
        }

        $FarmerWorkforce = FarmerWorkforce::where('user_id', Auth::user()->id)->first();
        if (! $FarmerWorkforce instanceof FarmerWorkforce) {
            throw new JsonException(FarmerWorkforce::class.' model could not be retrieved for user');
        }

        if ($Farmer->crop_type) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('Your previous crops are not finished growing yet'));
        }

        if (! $this->inventoryService->hasEnoughAmount($Inventory, $Crop->seed_item, $Crop->seed_required)) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('You don\'t have enough seeds to grow this crop'));
        }

        // Check if user has the specified workforce
        if (
            $FarmerWorkforce->avail_workforce < $workforce
        ) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('You don\'t have enough workers ready'));
        }

        // Calculate new countdown
        $workforce_reduction = ($Crop->time) * ($workforce * 0.005);
        $base_reduction = $Crop->time * ($FarmerWorkforce->efficiency_level * 0.01);
        $addTime = intval($Crop->time - $workforce_reduction - $base_reduction);

        $new_countdown = Carbon::now()->addSeconds($addTime);

        $new_workforce_amount = $FarmerWorkforce->avail_workforce - $workforce;
        $FarmerWorkforce->$location = $workforce;
        $FarmerWorkforce->avail_workforce = $new_workforce_amount;
        $FarmerWorkforce->save();

        $Farmer->crop_finishes_at = $new_countdown;
        $Farmer->crop_type = $crop_type;
        $Farmer->save();

        $this->inventoryService->edit($User->inventory, $Crop->seed_item, -$Crop->seed_required, $User->id);

        return advResponse([
            'avail_workforce' => $new_workforce_amount,
            'new_hunger' => $User->hunger->refresh(),
        ])->addMessage(GameLogService::addSuccessLog("You have started growing $crop_type"));
    }

    /**
     * Harvest crops
     */
    public function harvestCrops(Request $request): AdvResponse
    {
        $User = Auth::user();
        $is_cancelling = $request->boolean('is_cancelling');

        $validator = Validator::make($request->all(),
            [
                'is_cancelling' => 'required|boolean',
            ]
        );

        if ($validator->fails()) {
            return advResponse([], 400)
                ->addMessage(GameLogService::addErrorLog('Invalid input provided'));
        }

        $location = $this->sessionService->getCurrentLocation();
        if (! $this->locationService->isCropsLocation($location)) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('You are in the wrong location to grow crops'));
        }

        $Farmer = Farmer::where('user_id', Auth::user()->id)
            ->where('location', $location)
            ->first();

        if (! $Farmer instanceof Farmer) {
            throw new JsonException(Farmer::class, ' model could not be retrieved');
        }
        if (is_null($Farmer->crop_type)) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('You are in the wrong location to grow crops'));
        }
        $Crop = Crop::where('crop_type', $Farmer->crop_type)->first();

        if (! $Crop instanceof Crop) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('Unvalid crop'));
        }

        $Workforce = FarmerWorkforce::where('user_id', Auth::user()->id)->firstOrFail();

        if (! $Workforce instanceof FarmerWorkforce) {
            throw new JsonException(FarmerWorkforce::class, ' model could not be retrieved');
        }

        if (
            Carbon::now()->isAfter($Farmer->crop_finishes_at) &&
            $Farmer->crop_type &&
            $is_cancelling
        ) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('Why destroy crops that is finished'));
        } elseif (! Carbon::now()->isAfter($Farmer->crop_finishes_at) && ! $is_cancelling) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('Growing of crops is not finished yet'));
        }

        $response = new AdvResponse([], 200);

        if (! $is_cancelling) {

            $amount = rand($Crop->min_crop_count, $Crop->max_crop_count);

            $experience = intval($Crop->experience + (round($Crop->experience / 100 * $amount)));

            $this->inventoryService->edit($User->inventory, $Farmer->crop_type, $amount, $User->id);

            UserLevelsUpdater::create($User->userLevels)
                ->addFarmerXP($experience)
                ->update();
                
            $response->addEvent(GameEvents::XpGainedEvent);
        }

        $Farmer->crop_type = null;
        $Farmer->save();

        $Workforce->avail_workforce += $Workforce->{$location};
        $Workforce->{$location} = 0;
        $Workforce->save();

        $response->setData(['avail_workforce' => $Workforce->avail_workforce]);

        if (! $is_cancelling) {
            $response->addMessage(
                GameLogService::addSuccessLog("You have harvested $amount $Farmer->crop_type"));

        } else {
            $response->addMessage(
                GameLogService::addInfoLog('You have cancelled growing crops'));
        }

        return $response;
    }

    public function collectSeeds(#[CurrentUser] User $User, Request $request, GameLogService $gameLogService): AdvResponse
    {
        $item = $request->input('item');
        $amount = $request->integer('amount');

        $validator = Validator::make($request->all(),
            [
                'item' => 'required|string',
                'amount' => 'required|integer|min:1',
            ]);

        if ($validator->fails()) {
            return advResponse([], 400)
                ->addMessage(GameLogService::addErrorLog('Invalid input provided'));
        }

        $Crop = Crop::where('crop_type', $item)->first();

        if (! $Crop instanceof Crop) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('Unvalid crop'));
        }

        $this->inventoryService->edit($User->inventory, $Crop->seed_item, 1 * $amount, $User->id);
        $this->inventoryService->edit($User->inventory, $item, -$amount, $User->id);

        return advResponse([], 200)
            ->addMessage(GameLogService::addSuccessLog("You have generated $amount seed"));
    }
}
