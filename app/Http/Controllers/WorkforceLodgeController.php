<?php

namespace App\Http\Controllers;

use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\Models\EfficiencyUpgrade;
use App\Models\FarmerWorkforce;
use App\Models\MinerWorkforce;
use App\Services\InventoryService;
use App\Services\LevelDataService;
use App\Services\SessionService;
use App\Services\SkillsService;

class WorkforceLodgeController extends controller
{
    public function __construct(
        public SessionService $sessionService,
        public SkillsService $skillsService,
        public InventoryService $inventoryService,
        public LevelDataService $levelDataService
    ) {
        parent::__construct();
    }

    /**
     * 
     * @return void 
     */
    public function index()
    {
        $FarmerWorkforce = FarmerWorkforce::where('username', $this->sessionService->user())->first();
        $MinerWorkforce = MinerWorkforce::where('username', $this->sessionService->user())->first();

        $maxFarmerWorkers = $this->levelDataService->getMaxFarmers($this->skillsService->userLevels->farmer_level);
        $maxMinerWorkers = $this->levelDataService->getMaxMiners($this->skillsService->userLevels->miner_level);

        $farmer_efficiency_cost = EfficiencyUpgrade::where('level', $FarmerWorkforce->efficiency_level)->first()->price;
        $miner_efficiency_cost = EfficiencyUpgrade::where('level', $MinerWorkforce->efficiency_level)->first()->price;

        return $this->render(
            'workforcelodge',
            'workforcelodge',
            [
                'FarmerWorkforce' => $FarmerWorkforce,
                'maxFarmerWorkers' => $maxFarmerWorkers,
                'MinerWorkforce' => $MinerWorkforce,
                'maxMinerWorkers' => $maxMinerWorkers,
                'farmer_efficiency_cost' => $farmer_efficiency_cost,
                'miner_efficiency_cost' => $miner_efficiency_cost,
            ],
            true,
            true,
            true
        );
    }

    /**
     * 
     * @param Request $request 
     * @return void 
     */
    public function upgradeEfficiency(Request $request)
    {
        $skill = $request->getInput('skill');


        if ($skill === 'farmer') {
            $skillLevel = $this->skillsService->userLevels->farmer_level;
            $Workforce = FarmerWorkforce::where('username', $this->sessionService->user())->first();
            $maxWorkers = $this->levelDataService->getMaxFarmers($skillLevel);
        } else {
            $skillLevel = $this->skillsService->userLevels->miner_level;
            $Workforce = MinerWorkforce::where('username', $this->sessionService->user())->first();
            $maxWorkers = $this->levelDataService->getMaxMiners($$skillLevel);
        }

        if (!$Workforce instanceof FarmerWorkforce && !$Workforce instanceof MinerWorkforce) {
            return Response::addMessage("Something unexpected happened", 500);
        }

        if ($maxWorkers < $this->levelDataService->getMaxEfficiencyLevel($skillLevel)) {
            return Response::addMessage("You need to level up your skill before upgrading efficiency more", 422);
        }

        $price = EfficiencyUpgrade::where('level', $Workforce->efficiency_level)->first()->price;

        if (!$this->inventoryService->hasEnoughAmount(CURRENCY, $price)) {
            return $this->inventoryService->logNotEnoughAmount(CURRENCY);
        }

        $Workforce->efficiency_level += 1;
        $Workforce->save();

        return Response::setData([
            'efficiency_level' => $Workforce->efficiency_level,
            'new_efficiency_price' => EfficiencyUpgrade::where('level', $Workforce->efficiency_level)->first()->price,
        ])->addMessage("Efficiency upgraded")
            ->setStatus(200);
    }
}
