<?php

namespace App\controllers;

use App\builders\WorkforceBuilder;
use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\models\FarmerWorkforce_model;
use App\models\LevelData;
use App\models\MineWorkforce_model;
use App\models\UserLevels;
use App\services\InventoryService;
use App\services\SessionService;
use GameConstants;
use Respect\Validation\Validator;

class CityCentreController extends controller
{
    public $data = array();
    function __construct(
        private FarmerWorkforce_model $farmerWorkforce_model,
        private MineWorkforce_model $mineWorkforce_model,
        private LevelData $levelData,
        private InventoryService $inventoryService,
        private SessionService $sessionService
    ) {
        parent::__construct();
    }

    public function index()
    {
        $this->loadModel('CityCentre', true);
        $this->data = $this->model->getData();
        $this->render('citycentre', 'City Centre', $this->data, true, true);
    }


    public function upgradeEfficiency(Request $request)
    {
        $skill = $request->getInput('skill');

        $request->validate([
            'skill' => Validator::in([FARMER_SKILL_NAME, MINER_SKILL_NAME])
        ]);

        $skills = UserLevels::all()->where('username', $this->sessionService->getCurrentUsername());
        if ($skill === \FARMER_SKILL_NAME) {
            $data = $this->farmerWorkforce_model->all();
            $level = $skills['farmer_level'];
        } else {
            $data = $this->mineWorkforce_model->all();
            $level = $skills['miner_level'];
        }

        $efficiency_level_max = $this->levelData
            ->select('max_efficiency_level')
            ->where('level=?', [$level])
            ->get()[0]['max_efficiency_level'];

        $workforce_builder = WorkforceBuilder::create($data);


        if ($efficiency_level_max > $workforce_builder->build()->efficiency_level) {
            return Response::addMessage("Level up to increase efficiency further")->setStatus(422);
        }

        $price = $workforce_builder->build()->efficiency_level * 150;

        if (!$this->inventoryService->hasEnoughAmount(GameConstants::CURRENCY, $price)) {
            return $this->inventoryService->logNotEnoughAmount(GameConstants::CURRENCY);
        }

        $this->inventoryService->edit(GameConstants::CURRENCY, $price);

        $workforce_resource = $workforce_builder
            ->incrementEfficiency()->build();

        if ($skill) {
            $this->farmerWorkforce_model->update($workforce_resource);
        } else {
            $this->mineWorkforce_model->update($workforce_resource);
        }

        return Response::setData([
            'efficiencyLevel' =>  $workforce_resource->efficiency_level,
            'newEfficiencyPrice' => $workforce_resource->efficiency_level * 150
        ]);
    }
}
