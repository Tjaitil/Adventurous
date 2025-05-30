<?php

namespace App\Http\Controllers;

use App\Http\Responses\AdvResponse;
use App\Models\Miner;
use App\Models\Mineral;
use App\Models\MinerWorkforce;
use App\Models\User;
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
use JsonException;
use Validator;

class MineController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService,
        private SessionService $sessionService,
        private SkillsService $skillsService,
        private HungerService $hungerService,
        private LocationService $locationService
    ) {}

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $this->getViewData();
        $action_items = Mineral::all()->sortBy('miner_level')->values();
        $workforce_data = MinerWorkforce::where('user_id', Auth::user()->id)
            ->first()
            ?->toArray();

        $permits = Miner::select('permits')->where('user_id', Auth::user()->id)
            ->first()?->permits;

        return view('mine')
            ->with('title', 'Mine')
            ->with('action_items', $action_items)
            ->with('workforce_data', $workforce_data)
            ->with('permits', $permits);
    }

    public function getViewData(): JsonResponse
    {
        $Minerals = Mineral::all()->sortBy('miner_level')->values();
        $Workforce = MinerWorkforce::where('user_id', Auth::user()->id)
            ->first()
            ?->toArray();

        $permits = Miner::select('permits')->where('user_id', Auth::user()->id)
            ->first()?->permits;

        return response()->json([
            'minerals' => $Minerals,
            'workforce' => $Workforce,
            'permits' => $permits,
        ]);
    }

    /**
     * Get countdown for one location
     */
    public function getCountdown(): JsonResponse
    {
        $Miner = Miner::where('user_id', Auth::user()->id)
            ->where('location', $this->sessionService->getCurrentLocation())
            ->first();

        if (! $Miner instanceof Miner) {
            throw new JsonException(Miner::class.' model could not be retrieved for user');
        }

        return response()->json([
            'mining_finishes_at' => $Miner->mining_finishes_at->timestamp,
            'mineral_ore' => $Miner->mineral_ore,
        ]);
    }

    public function start(#[CurrentUser] User $User, Request $request): AdvResponse
    {
        $mineralOre = strtolower($request->string('mineral_ore'));
        $workforce = $request->integer('workforce_amount');

        $validator = Validator::make($request->all(), [
            'mineral_ore' => 'required|string',
            'workforce_amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return advResponse([], 400)
                ->addMessage(GameLogService::addErrorLog('Information provided is not valid'));

        }
        $location = $this->sessionService->getCurrentLocation();

        // Check if user is in right location
        if (! $this->locationService->isMineLocation($location)) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('You are in the wrong location to mine minerals'));
        } elseif ($this->hungerService->isHungerTooLow($User->hunger)) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('Your hunger status is too low'));
        }

        $Mineral = Mineral::where('mineral_ore', $mineralOre)
            ->first();

        if (! $Mineral instanceof Mineral) {
            return advresponse([], 422)
                ->addMessage(GameLogService::addErrorLog('Unvalid mineral'));
        } elseif ($Mineral->location !== $location) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('You are in the wrong location to mine this mineral'));
        } elseif (! $this->skillsService->hasRequiredLevel($User->userLevels, $Mineral->miner_level, 'miner')) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('You don\'t have the required level to mine this mineral'));
        }

        $Miner = Miner::where('user_id', Auth::user()->id)
            ->where('location', $location)->first();

        if (! $Miner instanceof Miner) {
            throw new JsonException(Miner::class.' model could not be retrieved for user');
        }

        $MinerWorkforce = MinerWorkforce::where('user_id', Auth::user()->id)->first();

        if (! $MinerWorkforce instanceof MinerWorkforce) {
            throw new JsonException(MinerWorkforce::class.' model could not be retrieved for user');
        }

        $new_permits = $Miner->permits - $Mineral->permit_cost;

        if ($new_permits < 0) {
            return advResponse([], 422, [])
                ->addMessage(GameLogService::addErrorLog('You don\'t have enough permits'));
        }

        if ($Miner->mineral_ore !== null) {
            return advResponse([], 422)->addMessage(GameLogService::addErrorLog('You are already mining'));
        } elseif ($Miner->location !== $location) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('You are in the wrong location to mine this mineral'));
        }

        $new_workforce_amount = $MinerWorkforce->avail_workforce - $workforce;

        if (! $new_workforce_amount < 0 || $MinerWorkforce->avail_workforce === 0) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('You don\'t have enough workers ready'));
        }

        $this->hungerService->setHungerForSkillAction($User->hunger);
        $addTime = $Mineral->time - (0.1 * $MinerWorkforce->efficiency_level + $workforce * 0.05);

        $Miner->mineral_ore = $mineralOre;
        $Miner->mining_finishes_at = Carbon::now()->addSeconds((int) $addTime);
        $Miner->save();

        $MinerWorkforce->avail_workforce = $new_workforce_amount;
        $MinerWorkforce->$location = $workforce;
        $MinerWorkforce->save();

        return advresponse([
            'avail_workforce' => $new_workforce_amount,
            'new_permits' => $new_permits,
            'new_hunger' => $User->hunger->refresh()->current,
        ])
            ->addMessage(GameLogService::addSuccessLog("You have started mining for $mineralOre"));
    }

    public function endMining(#[CurrentUser] User $User, Request $request, GameLogService $loggertService): AdvResponse
    {

        $is_cancelling = $request->boolean('is_cancelling');

        $validator = Validator::make($request->all(), [
            'is_cancelling' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return advResponse([], 522)->addMessage(
                $loggertService->addErrorLog('Information provided is not valid'));
        }

        $location = $this->sessionService->getCurrentLocation();
        $MinerWorkforce = MinerWorkforce::where('user_id', Auth::user()->id)
            ->first();

        if (! $MinerWorkforce instanceof MinerWorkforce) {
            throw new JsonException(MinerWorkforce::class.' model could not be retrieved for user');
        }

        $Miner = Miner::where('user_id', Auth::user()->id)
            ->where('location', $location)
            ->first();

        if (! $Miner instanceof Miner) {
            throw new JsonException(Miner::class.' model could not be retrieved for user');
        }

        if ($Miner->mineral_ore === null) {
            return advResponse([], 422)
                ->addMessage($loggertService->addErrorLog('You are not mining anything'));
        }

        $Mineral = Mineral::where('location', $location)
            ->where('mineral_ore', $Miner->mineral_ore)
            ->first();

        if (! $Mineral instanceof Mineral) {
            throw new JsonException(Mineral::class.' model could not be retrieved for user');
        }

        if (
            Carbon::now()->isAfter($Miner->mining_finishes_at) &&
            $Miner->mineral_ore &&
            $is_cancelling
        ) {
            return advResponse([], 422)
                ->addMessage($loggertService->addErrorLog('You cannot cancel mining that is already finished'));
        } elseif (! Carbon::now()->isAfter($Miner->mining_finishes_at) && ! $is_cancelling) {
            return advResponse([], 422)
                ->addMessage($loggertService->addErrorLog('The mining is not yet finished'));
        }

        $MinerWorkforce->avail_workforce += $MinerWorkforce->{$location};
        $MinerWorkforce->{$location} = 0;
        $MinerWorkforce->save();

        $Miner->mineral_ore = null;
        $Miner->save();

        $response = advResponse([]);

        if (! $is_cancelling) {

            $amount = rand($Mineral->min_per_period, $Mineral->max_per_period);
            $this->inventoryService->edit($User->inventory, $Mineral->mineral_ore, $amount, $User->id);

            UserLevelsUpdater::create($User->userLevels)
                ->addMinerXP($Mineral->experience)
                ->update();

            $message = sprintf('You have finished mining %s', $Mineral->mineral_ore);
        } else {
            $message = sprintf('You have cancelled mining.');
        }

        return $response->setData([
            'avail_workforce' => $MinerWorkforce->avail_workforce,
            'new_hunger' => $User->hunger->refresh()->current,
        ])
            ->addMessage($loggertService->addSuccessLog($message));
    }
}
