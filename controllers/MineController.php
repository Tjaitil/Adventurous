<?php

namespace App\controllers;

use App\builders\SkillActionBuilder;
use App\builders\TimeBuilder;
use App\builders\WorkforceBuilder;
use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\models\Miner;
use App\models\Mineral;
use App\models\MinerWorkforce;
use App\services\CountdownService;
use App\services\HungerService;
use App\services\InventoryService;
use App\services\SessionService;
use App\services\SkillsService;
use Carbon\Carbon;
use Respect\Validation\Validator;

class MineController extends controller
{
    public $data = [];
    function __construct(
        private InventoryService $inventoryService,
        private CountdownService $countdownService,
        private SkillActionBuilder $skillActionBuilder,
        private TimeBuilder $countdownBuilder,
        private WorkforceBuilder $workforceBuilder,
        private SessionService $sessionService,
        private SkillsService $skillsService,
        private HungerService $hungerService
    ) {
        parent::__construct();
    }
    public function index()
    {
        $this->getViewData();
        $this->data['action_items'] = Mineral::all()->sortBy('miner_level')->values();
        $this->data['workforce_data'] = MinerWorkforce::where('username', $this->sessionService->getCurrentUsername())
            ->first()
            ->toArray();

        $this->data['permits'] = Miner::select('permits')->where('username', $this->sessionService->getCurrentUsername())
            ->first()->permits;

        $this->render('mine', 'Mine', $this->data, true, true);
    }



    public function getViewData()
    {
        $this->data['minerals'] = Mineral::all()->sortBy('miner_level')->values();
        $this->data['workforce_data'] = MinerWorkforce::where('username', $this->sessionService->getCurrentUsername())
            ->first()
            ->toArray();

        $this->data['permits'] = Miner::select('permits')->where('username', $this->sessionService->getCurrentUsername())
            ->first()->permits;

        return Response::setData($this->data);
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

        if (!is_null($Miner)) {
            $minerResource = [
                ...$Miner->toArray(),
                'mining_countdown' => $Miner->mining_countdown->timestamp,
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
        $type = $request->getInput('mineral_ore');
        $workforce = $request->getInput('workforce_amount');

        $request->validate([
            'mineral_ore' => Validator::stringVal()->notEmpty(),
            'workforce_amount' => Validator::intVal()->min(1)
        ]);

        $location = $this->sessionService->getCurrentLocation();

        // Check if user is in right location
        if (!$this->sessionService->isValidMineLocation($location)) {
            return Response::addMessage("You are in the wrong location to mine minerals")->setStatus(422);
        } else if ($this->hungerService->isHungerTooLow()) {
            return $this->hungerService->logHungerTooLow();
        }

        $Mineral = Mineral::where('mineral_ore', $type)
            ->first();

        if (is_null($Mineral)) {
            return Response::addMessage('Unvalid mineral')->setStatus(422);
        } else if ($Mineral->location !== $location) {
            return Response::addMessage("You are in the wrong location to mine this mineral")->setStatus(422);
        } else if (!$this->skillsService->hasRequiredLevel($Mineral->miner_level, 'miner')) {
            return Response::addMessage("You don't have the required level to mine this mineral")->setStatus(422);
        }

        $Miner = Miner::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $location)
            ->first();

        $MinerWorkforce = MinerWorkforce::where('username', $this->sessionService->getCurrentUsername())
            ->first();

        if (is_null($MinerWorkforce)) {
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

        $this->hungerService->setNewHunger(SKILL_ACTION);
        $addTime = $Mineral->time - (0.1 * $MinerWorkforce->efficiency_level + $workforce * 0.05);

        $Miner->mineral_type = $type;
        $Miner->mining_countdown = Carbon::now()->addSeconds($addTime);
        $Miner->save();

        $location_table = MinerWorkforce::getLocationTable($location);

        $MinerWorkforce->avail_workforce = $new_workforce_amount;
        $MinerWorkforce->{$location_table} = $workforce;
        $MinerWorkforce->save();

        return Response::addMessage("You have started mining for $type")
            ->setData([
                'avail_workforce' => $new_workforce_amount,
                'new_permits' => $new_permits,
                'new_hunger' => $this->hungerService->getHunger(),
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
        $location_table = MinerWorkforce::getLocationTable($location);

        $MinerWorkforce = MinerWorkforce::where('username', $this->sessionService->getCurrentUsername())
            ->first();

        if (is_null($MinerWorkforce)) {
            return Response::addMessage("Unvalid Miner location")->setStatus(422);
        }

        $Miner = Miner::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $location)
            ->first();

        $Mineral = Mineral::where('location', $location)
            ->where('mineral_ore', $Miner->mineral_type)
            ->first();

        if (is_null($Mineral)) {
            return Response::addMessage("Unvalid Mineral")->setStatus(422);
        }

        if (
            Carbon::now()->isAfter($Miner->mining_countdown) &&
            $Miner->mineral_type &&
            $is_cancelling
        ) {
            return Response::addMessage("Why quit mining that is already finished")->setStatus(422);
        } else if (!Carbon::now()->isAfter($Miner->mining_countdown) && !$is_cancelling) {
            return Response::addMessage("The mining is not yet finished")->setStatus(422);
        }

        $MinerWorkforce->avail_workforce += $MinerWorkforce->{$location_table};
        $MinerWorkforce->{$location_table} = 0;
        $MinerWorkforce->save();

        $Miner->mineral_type = null;
        $Miner->save();

        if (!$is_cancelling) {

            $amount = rand($Mineral->min_per_period, $Mineral->max_per_period);
            $this->inventoryService->edit($Mineral->mineral_ore, $amount);


            $this->skillsService
                ->updateMinerXP($Mineral->experience)
                ->updateSkills();
            Response::addMessage("You have finished mining " . $Mineral->mineral_type);
        } else {
            Response::addMessage("You have cancelled mining");
        }

        return Response::setData(['avail_workforce' => $MinerWorkforce->avail_workforce])
            ->setStatus(200);
    }



    private function buyPermits(Request $request)
    {

        $location = $request->getInput('location');

        $request->validate([
            'skill' => Validator::in(GameConstants::MINE_LOCATIONS)
        ]);

        $skill_action_builder = SkillActionBuilder::create(
            $this->mineCountdown_model->find($location)
        );

        // TODO: Move this price
        $price = 200;
        if (!$this->inventoryService->hasEnoughAmount(GameConstants::CURRENCY, $price)) {
            return $this->inventoryService->logNotEnoughAmount(GameConstants::CURRENCY);
        }

        $this->inventoryService->edit(GameConstants::CURRENCY, $price);


        $skill_action_resource = $skill_action_builder
            ->incrementPermits(50)
            ->build();

        $this->mineCountdown_model->updatePermits($skill_action_resource->permits);

        Response::addMessage(\sprintf("You bought 50 permits for %s", [$location]))
            ->setData([
                'newPermits' => $skill_action_resource->permits
            ]);
    }
}
