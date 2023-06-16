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
use GameConstants;

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
        $this->data['action_items'] = Mineral::all();
        $this->data['workforce_data'] = MinerWorkforce::where('username', $this->sessionService->getCurrentUsername())
            ->first()
            ->toArray();

        $this->data['permits'] = Miner::select('permits')->where('username', $this->sessionService->getCurrentUsername())
            ->first()->permits;

        $this->render('mine', 'Mine', $this->data, true, true);
    }


    // TODO: create getData function


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

        $minerResource = [
            ...$Miner->toArray(),
            'mining_countdown' => $Miner->mining_countdown->timestamp,
        ];

        return Response::setData($Miner ? $minerResource : []);
    }



    public function start(Request $request)
    {
        $type = $request->getInput('type');
        $workforce = $request->getInput('workforce_amount');

        $request->validate([
            'type' => Validator::stringVal()->notEmpty(),
            'workforce_amount' => Validator::intVal()->min(1)
        ]);

        $location = $this->sessionService->getCurrentLocation();

        // Check if user is in right location
        if (!$this->sessionService->isValidMineLocation($location)) {
            return Response::addMessage("You are in the wrong location to mine minerals")->setStatus(422);
        } else if ($this->hungerService->isHungerTooLow()) {
            return $this->hungerService->logHungerTooLow();
        }

        $Mineral = Mineral::where('mineral_type', $type)
            ->where('location', $location)
            ->first();

        if (is_null($Mineral)) {
            return Response::addMessage('Unvalid mineral')->setStatus(422);
        }

        $Miner = Miner::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $location);

        $MinerWorkforce = MinerWorkforce::where('location', $location)
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

        // Check if user has the specified workforce
        if (
            $new_workforce_amount < 0
        ) {
            return Response::addMessage("You don't have enough workers ready")->setStatus(422);
        }

        $addTime = $Mineral->time - (0.1 * $MinerWorkforce->efficiency_level + $workforce * 0.05);

        $Miner->mining_type = $type;
        $Miner->mining_countdown = Carbon::now()->addSeconds($addTime);
        $Miner->save();

        $location_table = $location . "_workforce";

        $MinerWorkforce->avail_workforce = $new_workforce_amount;
        $MinerWorkforce->{$location_table} = $workforce;
        $MinerWorkforce->save();

        return Response::addMessage("You have started mining for $type")
            ->setData([
                'availWorkforce' => $new_workforce_amount,
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

        $is_cancelling = $request->getInput('isCancelling');

        $request->validate([
            'isCancelling' => Validator::boolVal()
        ]);

        $location = $this->sessionService->getCurrentLocation();

        $MinerWorkforce = MinerWorkforce::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $location)
            ->first();

        if (is_null($MinerWorkforce)) {
            return Response::addMessage("Unvalid Miner location")->setStatus(422);
        }

        $Mineral = Mineral::where('location', $location)
            ->where('mineral_type', $MinerWorkforce->mineral_type)
            ->first();

        if (is_null($Mineral)) {
            return Response::addMessage("Unvalid Mineral")->setStatus(422);
        }

        $Miner = Miner::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $location)
            ->first();

        if (
            Carbon::now()->isAfter($Miner->mining_countdown) &&
            $Miner->mining_countdown &&
            $is_cancelling
        ) {
            return Response::addMessage("Why quit mining that is already finished")->setStatus(422);
        } else if (!Carbon::now()->isAfter($Miner->mining_countdown) && !$is_cancelling) {
            return Response::addMessage("The mining is not yet finished")->setStatus(422);
        }

        $location_table = $location . "_workforce";

        $MinerWorkforce->{$location_table} = 0;
        $MinerWorkforce->avail_workforce += $MinerWorkforce->{$location_table};
        $MinerWorkforce->save();

        $Miner->mining_type = null;
        $Miner->save();

        if (!$is_cancelling) {

            $amount = rand($Mineral->min_per_period, $Mineral->max_per_period);
            $this->inventoryService->edit($Mineral->mineral_ore, $amount);


            $this->skillsService
                ->updateMinerXP($Mineral->experience)
                ->updateSkills();
        }

        return Response::addMessage("You have finished mining " . $Mineral->mineral_type)
            ->setData(['available_workforce' => $MinerWorkforce->avail_workforce])
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
