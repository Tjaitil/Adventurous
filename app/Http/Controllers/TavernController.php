<?php

namespace App\Http\Controllers;

use App\Http\Builders\WarriorBuilder;
use App\Http\Builders\WorkforceBuilder;
use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\Models\Armory_model;
use App\Models\FarmerWorkforce_model;
use App\Models\LevelData;
use App\Models\MineWorkforce_model;
use App\Models\TavernPrices_model;
use App\Models\TavernTimes_model;
use App\Models\TavernWorkers_model;
use App\Models\UserLevels;
use App\Models\Warrior_model;
use App\Models\Warriors;
use App\Models\Warriors_model;
use App\Models\WarriorsLevels_model;
use App\Models\WarriorsLevelsData;
use App\Services\SessionService;
use App\Services\InventoryService;
use GameConstants;
use Exception;
use Respect\Validation\Validator;

class TavernController extends controller
{
    public $data = array();

    function __construct(
        private TavernTimes_model $tavernTimes_model,
        private TavernWorkers_model $tavernWorkers_model,
        private MineWorkforce_model $mineWorkforce_model,
        private FarmerWorkforce_model $farmerWorkforce_model,
        private WorkforceBuilder $workforceBuilder,
        private SessionService $sessionService,
        private LevelData $levelData,
        private Armory_model $armory_model,
        private Warriors_model $warriors_model,
        private WarriorBuilder $warriorBuilder,
        private Warrior_model $warrior_model,
        private InventoryService $inventoryService,
    ) {
        parent::__construct();
    }

    public function index()
    {
        // TODO: Check if user can access tavern?
        $this->data['workers'] = $this->tavernWorkers_model->all($this->sessionService->getLocation());
        $this->shouldGenerateWorkers();
        $this->render('tavern', 'Tavern', $this->data, true, true);
    }

    /**
     * Recruit worker
     *
     * @param Request $request
     *
     * @return Response
     */
    public function recruitPersonell(Request $request)
    {
        $type = $request->getInput('type');
        $level = $request->getInput('level');

        // TODO: Research validator to see if we can check against an array
        $request->validate([
            'type' => Validator::StringVal()->notEmpty(),
            'level' => Validator::IntVal(),
        ]);


        try {

            //Retrieve worker
            $workers = $this->tavernWorkers_model->find($this->sessionService->getLocation(), $level, $type);
            if (count($workers) === 0) {
                return Response::addMessage("The worker you are trying to recruit doesn't exist")->setStatus(422);
            }

            $price = $this->tavernPrices_model->find($type)['price'];
            if (!$this->inventoryService->hasEnoughAmount(
                GameConstants::CURRENCY,
                $this->tavernPrices_model->find($type)['price']
            )) {
                return $this->inventoryService->logNotEnoughAmount(GameConstants::CURRENCY);
            }

            $skills = UserLevels::where('username', $this->sessionService->getCurrentUsername());

            switch ($type) {
                case "farmer":
                case "miner":
                    if (!$this->recruitWorker($workers, $skills, $type)) {
                        return;
                    }
                    break;
                case "melee":
                case "ranged":

                    if (!$this->recruitWarrior($workers, $skills->warrior_level, $type)) {
                        return;
                    }
                    break;
                default:
                    return Response::addErrorMessage("Unvalid type")->setStatus(422);
                    break;
            }

            $this->inventoryService->edit(GameConstants::CURRENCY, -$price);

            $this->tavernWorkers_model->destroy($this->sessionService->getLocation(), $level, $type);
            // Tavern workers update 

            return Response::setStatus(200);
        } catch (Exception $e) {
            return Response::addMessage($e->getMessage());
        }
    }

    /**
     * Undocumented function
     *
     * @param array $workers
     *
     * @return void
     */
    private function recruitWorker(array $worker, UserLevels $skills, string $type)
    {

        if ($type === 'miner') {
            $data = $this->mineWorkforce_model->all();
            $max_amount = $this->levelData
                ->select('max_mine_workers')
                ->where('level=?', [$skills->miner_level])
                ->get()[0]['max_mine_workers'];
        } else {
            $data = $this->farmerWorkforce_model->all();
            $max_amount = $this->levelData
                ->select('max_mine_workers')
                ->where('level=?', [$skills->farmer_level])
                ->get();
        }

        $workforce_builder = $this->workforceBuilder::create($data);
        $workforce_builder
            ->addToAvailAmount(1)
            ->addToTotalAmount(1);

        // if (
        //     $workforce_builder->build()->total_amount >
        //     $max_amount
        // ) {
        //     Response::addMessage("Your level is not high enough to recruit")->setStatus(422);
        //     return false;
        // }

        if ($type === 'miner') {
            $workforce_builder->setType('miner');
            $this->mineWorkforce_model->updateWorkforce($workforce_builder->build());
        } else {
            $workforce_builder->setType('farmer');
            $this->farmerWorkforce_model->updateWorkforce($workforce_builder->build());
        }
    }

    /**
     * Recruit warrior
     *
     * @param array $worker
     * @param int $warrior_level
     * @param string $type
     *
     * @return void
     */
    public function recruitWarrior(array $worker, int $warrior_level, string $type)
    {

        $current_warrior_amount = $this->warrior_model
            ->select('warrior_amount')
            ->where('username=?', [$this->sessionService->username])
            ->get()[0]['warrior_amount'];

        $max_warrior_amount = $this->levelData
            ->select('max_warriors')
            ->where('level=?', [$warrior_level])
            ->get()[0]['max_warriors'];

        if (
            $current_warrior_amount === $max_warrior_amount
        ) {
            Response::addMessage("Your level is not high enough to recruit")->setStatus(422);
            return false;
        }

        $max = Warriors::select()
            ->where('username=?', [$this->sessionService->username])
            ->orderBy('warrior_id', 'desc')
            ->first();

        $max_id = $max->warrior_id + 1;

        if ($worker['level'] === 1) {
            $current_xp = 0;
        } else {
            $level_data = WarriorsLevelsData::where('skill_level', $worker['level'] - 1);
            $current_xp = $level_data->next_level;
        }

        $builder = $this->warriorBuilder::create();
        $builder
            ->setWarriorID($max_id)
            ->setType($type)
            ->setStaminaLevel($worker['level'])
            ->setStrengthLevel($worker['level'])
            ->setTechniqueLevel($worker['level'])
            ->setPrecisionLevel($worker['level'])
            ->setStaminaXP($current_xp)
            ->setStrengthXP($current_xp)
            ->setTechniqueXP($current_xp)
            ->setPrecisionXP($current_xp);

        $resource = $builder->build();
        $this->warriors_model->create($resource);
        $this->warriorsLevels_model->create($resource);
        $this->armory_model->create($resource->warrior_id);
    }
    /**
     * Check if new workers should be generated
     *
     * @return bool
     */
    private function shouldGenerateWorkers()
    {
        $date = date("Y-m-d");

        $city = $this->sessionService->getCurrentLocation();

        if ($date < $this->data['workers']['new_workers']) {
            return false;
        } else if (
            intval($this->data['workers'][$city]) === 0 ||
            $date > $this->data['workers']['new_workers']
        ) {
            switch ($city) {
                case 'snerpiir' || 'golbak':
                    $farmer_amount = rand(0, 2);
                    $miner_amount = rand(1, 3);
                    $warrior_amount = rand(1, 2);
                    break;
                case 'towhar' || 'krasnur':
                    $farmer_amount = rand(1, 3);
                    $miner_amount  = rand(0, 2);
                    $warrior_amount = rand(0, 2);
                    break;
                case 'tasnobil' || 'cruendo':
                    $farmer_amount = rand(0, 2);
                    $miner_amount  = rand(0, 2);
                    $warrior_amount = rand(1, 5);
                    break;
                case 'fagna':
                    $farmer_amount = rand(0, 2);
                    $miner_amount  = rand(0, 2);
                    $warrior_amount = rand(0, 2);
                    break;
            }
            $warrior_types = array('melee', 'ranged');
            for ($i = 0; $i < ($farmer_amount + $miner_amount + $warrior_amount); $i++) {
                if ($i < $farmer_amount) {
                    $this->data['workers'][$i]['type'] = 'farmer';
                    $this->data['workers'][$i]['level'] = 0;
                } else if ($i < ($farmer_amount + $miner_amount)) {
                    $this->data['workers'][$i]['type'] = 'miner';
                    $this->data['workers'][$i]['level'] = 0;
                } else {
                    $rand = array_rand($warrior_types);
                    $this->data['workers'][$i]['type'] = $warrior_types[$rand];
                    $this->data['workers'][$i]['level'] = rand(1, 3);
                }
            }
            $this->data['workers'] = $this->data['workers'];

            try {
                $this->tavernWorkers_model->destroyAll();

                foreach ($this->data['workers'] as $key) {
                    $this->tavernWorkers_model->create($city, $key['level'], $key['type']);
                }

                $this->tavernTimes_model->update($city);
            } catch (Exception $e) {
                return;
            }
        }
    }

    public function restoreHealth()
    {
    }
}
