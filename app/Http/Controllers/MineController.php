<?php

namespace App\Http\Controllers;

use App\Enums\GameLocations;
use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\Models\Miner;
use App\Models\Mineral;
use App\Models\MinerPermitCost;
use App\Models\MinerWorkforce;
use App\Services\HungerService;
use App\Services\InventoryService;
use App\Services\LocationService;
use App\Services\SessionService;
use App\Services\SkillsService;
use Carbon\Carbon;
use Respect\Validation\Validator;

class MineController extends controller
{
    function __construct(
        private InventoryService $inventoryService,
        private SessionService $sessionService,
        private SkillsService $skillsService,
        private HungerService $hungerService,
        private LocationService $locationService
    ) {
        parent::__construct();
    }

    /**
     * 
     * @return void 
     */
    public function index()
    {
        $this->getViewData();
        $data['action_items'] = Mineral::all()->sortBy('miner_level')->values();
        $data['workforce_data'] = MinerWorkforce::where('username', $this->sessionService->getCurrentUsername())
            ->first()
            ->toArray();

        $data['permits'] = Miner::select('permits')->where('username', $this->sessionService->getCurrentUsername())
            ->first()->permits;

        $this->render('mine', 'Mine', $data, true, true, true);
    }

    /**
     * 
     * @return Response 
     */
    public function getViewData()
    {
        $data['minerals'] = Mineral::all()->sortBy('miner_level')->values();
        $data['workforce_data'] = MinerWorkforce::where('username', $this->sessionService->getCurrentUsername())
            ->first()
            ->toArray();

        $data['permits'] = Miner::select('permits')->where('username', $this->sessionService->getCurrentUsername())
            ->first()->permits;

        return Response::setData($data);
    }

    /**
     * Get countdown for one location
     *
     * @return Response
     */
    public function getCountdown()
    {
        $Miner = Miner::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $this->sessionService->getCurrentLocation())
            ->first();

        if ($Miner instanceof Miner) {
            $minerResource = [
                ...$Miner->toArray(),
                'mining_finishes_at' => $Miner->mining_finishes_at->timestamp,
            ];
        }

        return Response::setData($Miner ? $minerResource : []);
    }



    /**
     *
     * @param Request $request
     *
     * @return void
     */
    public function start(Request $request)
    {
        $type = strtolower($request->getInput('mineral_ore'));
        $workforce = $request->getInput('workforce_amount');
        $request->validate([
            'mineral_ore' => Validator::stringVal()->notEmpty(),
            'workforce_amount' => Validator::intVal()->min(1)
        ]);

        $location = $this->sessionService->getCurrentLocation();

        // Check if user is in right location
        if (!$this->locationService->isMineLocation($location)) {
            return Response::addMessage("You are in the wrong location to mine minerals")->setStatus(422);
        } else if ($this->hungerService->isHungerTooLow()) {
            return $this->hungerService->logHungerTooLow();
        }

        $Mineral = Mineral::where('mineral_ore', $type)
            ->first();

        if (!$Mineral instanceof Mineral) {
            return Response::addMessage('Unvalid mineral')->setStatus(422);
        } else if ($Mineral->location !== $location) {
            return Response::addMessage("You are in the wrong location to mine this mineral")->setStatus(422);
        } else if (!$this->skillsService->hasRequiredLevel($Mineral->miner_level, 'miner')) {
            return Response::addMessage("You don't have the required level to mine this mineral")->setStatus(422);
        }

        $Miner = Miner::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $location)
            ->first();

        if (!$Miner instanceof Miner) {
            return Response::addMessage('Unvalid miner')->setStatus(422);
        }

        $MinerWorkforce = MinerWorkforce::where('username', $this->sessionService->getCurrentUsername())
            ->first();

        if (!$MinerWorkforce instanceof MinerWorkforce) {
            return Response::addMessage('Unvalid miner location')->setStatus(422);
        }

        $new_permits = $Miner->permits - $Mineral->permit_cost;

        if ($new_permits < 0) {
            return Response::addMessage("You don't have enough permits!")->setStatus(422);
        }

        if ($Miner->mining_type) {
            return Response::addMessage('Unvalid miner')->setStatus(422);
        } else if ($Miner->location !== $location) {
            return Response::addMessage('You are in the wrong location to mine this mineral')->setStatus(422);
        }

        $new_workforce_amount = $MinerWorkforce->avail_workforce - $workforce;

        if (
            $new_workforce_amount < 0
        ) {
            return Response::addMessage("You don't have enough workers ready")->setStatus(422);
        }

        $this->hungerService->setHungerForSkillAction();
        $addTime = $Mineral->time - (0.1 * $MinerWorkforce->efficiency_level + $workforce * 0.05);

        $Miner->mineral_type = $type;
        $Miner->mining_finishes_at = Carbon::now()->addSeconds($addTime);
        $Miner->save();


        $MinerWorkforce->avail_workforce = $new_workforce_amount;
        $MinerWorkforce->$location = $workforce;
        $MinerWorkforce->save();

        return Response::addMessage("You have started mining for $type")
            ->setData([
                'avail_workforce' => $new_workforce_amount,
                'new_permits' => $new_permits,
                'new_hunger' => $this->hungerService->getCurrentHunger(),
            ])
            ->setStatus(200);
    }



    /**
     *
     * @param Request $request
     *
     * @return Response
     */
    public function endMining(Request $request)
    {

        $is_cancelling = $request->getInput('is_cancelling');

        $request->validate([
            'is_cancelling' => Validator::boolVal()
        ]);

        $location = $this->sessionService->getCurrentLocation();
        $MinerWorkforce = MinerWorkforce::where('username', $this->sessionService->getCurrentUsername())
            ->first();

        if (!$MinerWorkforce instanceof MinerWorkforce) {
            return Response::addMessage("Unvalid Miner location")->setStatus(422);
        }

        $Miner = Miner::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $location)
            ->first();

        if (!$Miner instanceof Miner) {
            return Response::addMessage("You don't have enough permits")->setStatus(422);
        }

        $Mineral = Mineral::where('location', $location)
            ->where('mineral_ore', $Miner->mineral_type)
            ->first();

        if (!$Mineral instanceof Mineral) {
            return Response::addMessage("Unvalid Mineral")->setStatus(422);
        }

        if (
            Carbon::now()->isAfter($Miner->mining_finishes_at) &&
            $Miner->mineral_type &&
            $is_cancelling
        ) {
            return Response::addMessage("Why quit mining that is already finished")->setStatus(422);
        } else if (!Carbon::now()->isAfter($Miner->mining_finishes_at) && !$is_cancelling) {
            return Response::addMessage("The mining is not yet finished")->setStatus(422);
        }

        $MinerWorkforce->avail_workforce += $MinerWorkforce->{$location};
        $MinerWorkforce->{$location} = 0;
        $MinerWorkforce->save();

        $Miner->mineral_type = null;
        $Miner->save();

        if (!$is_cancelling) {

            $amount = rand($Mineral->min_per_period, $Mineral->max_per_period);
            $this->inventoryService->edit($Mineral->mineral_ore, $amount);


            $this->skillsService
                ->updateMinerXP($Mineral->experience)
                ->updateSkills();
            return Response::addMessage("You have finished mining " . $Mineral->mineral_type);
        } else {
            return Response::addMessage("You have cancelled mining");
        }

        return Response::setData(['avail_workforce' => $MinerWorkforce->avail_workforce])
            ->setStatus(200);
    }

    /**
     * 
     * @param Request $request 
     * @return Response
     */
    public function buyPermits(Request $request)
    {
        $location = $request->getInput('location');

        $request->validate([
            'skill' => Validator::in(GameLocations::getMineLocations())
        ]);

        $Miner = Miner::where('username', $this->sessionService->user())
            ->where('location', $location)
            ->first();

        if (!$Miner instanceof Miner) {
            return Response::addMessage("Unvalid miner")->setStatus(422);
        }


        $MinerPermitCost = MinerPermitCost::where('location', $location)
            ->first();

        if (!$MinerPermitCost instanceof MinerPermitCost) {
            return Response::addMessage("Unvalid location")->setStatus(422);
        }

        if (!$this->inventoryService->hasEnoughAmount(\CURRENCY, $MinerPermitCost->permit_cost)) {
            return $this->inventoryService->logNotEnoughAmount(\CURRENCY);
        }

        $this->inventoryService->edit(\CURRENCY, $MinerPermitCost->permit_cost);

        $Miner->permits += $MinerPermitCost->permit_amount;
        $Miner->save();

        return Response::addMessage(\sprintf("You bought 50 permits for %s", [$location]))
            ->setData([
                'newPermits' => $Miner->permits
            ]);
    }
}
