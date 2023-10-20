<?php

namespace App\controllers;

use App\actions\CalculateCharacterHealthAction;
use App\actions\CanLevelUpAction;
use App\actions\MergeIntoSubArrayAction;
use App\builders\WarriorBuilder;
use App\builders\WarriorLevelsBuilder;
use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\models\HealingItem;
use App\models\TrainingTypeData;
use App\models\Warriors;
use App\resources\WarriorResource;
use App\services\CountdownService;
use App\services\InventoryService;
use App\services\SessionService;
use App\services\SkillsService;
use App\services\WarriorService;
use \Exception;
use Respect\Validation\Validator;

class ArmyCampController extends controller
{
    public $data;
    public $warrior_data = array();
    public $error = array();

    public function __construct(
        private WarriorBuilder $warriorBuilder,
        private WarriorService $warriorService,
        private SessionService $sessionService,
        private CountdownService $countdownService,
        private InventoryService $inventoryService,
        private WarriorLevelsBuilder $warriorLevelsBuilder,
        private CalculateCharacterHealthAction $calculateCharacterHealthAction,
        private MergeIntoSubArrayAction $mergeIntoSubArrayAction,
        private SkillsService $skillsService,
    ) {
        parent::__construct();
    }
    public function index()
    {
        $resources = $this->createResources();

        $data['warrior_data'] = $resources;

        $this->render('armycamp', 'Army Camp', $data, true, true);
    }

    /**
     * Create warrior ressources
     *
     * @return Warrioresource[]
     */
    private function createResources(): array
    {
        $warriors = $this->warriorService->getWarriorDataWithRelations();
        $resources = [];
        foreach ($warriors as $key => $value) {
            array_push($resources, (new WarriorResource($value))->toArray());
        }

        return $resources;
    }

    /**
     * Get warrior data
     * 
     * @return Response
     */
    public function get()
    {
        $resources = $this->createResources();

        return Response::addData('warriors', $resources);
    }

    /**
     * Heal warrior with item
     *
     * @param \App\libs\Request $request
     *
     * @return void
     */
    public function healWarrior(Request $request)
    {
        $warrior_id = $request->getInput('warrior_id');
        $item = $request->getInput('item');
        $amount = $request->getInput('amount');

        $request->validate([
            'warrior_id' => Validator::intVal(),
            'item' => Validator::stringVal()->notEmpty(),
            'amount' => Validator::intVal()->min(1)
        ]);

        $warriors = $this->warriorService->getWarriors([$warrior_id]);
        $warrior = $warriors[0];

        if (!$this->checkWarriorExist([$warrior_id])) {
            return $this->logWarriorsNotExist();
        }

        $health_item = HealingItem::where('item_name', $item)->first();

        if (!$health_item->count()) {
            return Response::addMessage("Item not found")->setStatus(404);
        }

        if (!$this->inventoryService->hasEnoughAmount($item, $amount)) {
            $this->inventoryService->logNotEnoughAmount($item);
        }

        $new_health_data =
            $this->calculateCharacterHealthAction->handle(
                $warrior->health,
                100,
                $health_item->healing_amount,
                $amount
            );

        $warrior->health = $new_health_data['new_health'];
        $warrior->save();

        $resource = new WarriorResource($warrior);
        return Response::addData('warrior', $resource->toArray())->setStatus(200);
    }

    /**
     * Transfer warriors to new location
     *
     * @param \App\libs\Request $request
     *
     * @return Response
     */
    public function transferWarriors(Request $request)
    {

        $warrior_ids = $request->getInput('warrior_ids');
        $location = $request->getInput('location');

        $request->validate([
            'warrior_ids' => Validator::arrayType()->notEmpty(),
            'location' => Validator::stringVal()->notEmpty(),
        ]);

        if (!$this->warriorService->isValidWarriorLocation($location)) {
            return $this->warriorService->logInvalidWarriorLocation();
        }

        $warriors = $this->warriorService->getAvailableWarriors($warrior_ids);

        if (!$this->checkWarriorExist($warrior_ids)) {
            return $this->logWarriorsNotExist();
        }
        foreach ($warriors as $key => $value) {
            $value->location = $location;
            $value->save();
        }

        return Response::addMessage("Warriors transfered")->setStatus(200);
    }

    /**
     * Toggle Warrior rest
     *
     * @param \App\libs\Request $request
     *
     * @return Response
     */
    public function toggleWarriorsRest(Request $request)
    {
        $warrior_ids = $request->getInput('warrior_ids');
        $is_starting_rest = $request->getInput('is_starting_rest');

        $request->validate([
            'warrior_ids' => Validator::arrayType()->notEmpty(),
            'is_starting_rest' => Validator::boolVal()
        ]);

        $warriors = Warriors::where([
            ['username', $this->sessionService->getCurrentUsername()],
            ['fetch_report', 0],
            ['army_mission', 0],
        ])->whereIn('warrior_id', $warrior_ids)->get();

        $this->warriorService->warriors = $warriors;
        $this->warriorService->warrior_count = $warriors->count();

        if (!$this->checkWarriorExist($warrior_ids)) {
            return $this->logWarriorsNotExist();
        }

        if ($is_starting_rest) {

            foreach ($warriors as $key => $value) {
                if ($value->rest) {
                    return Response::addMessage('One or more of your warriors is already resting');
                } else {
                    $value->rest = 1;
                    $value->rest_start = $this->countdownService->getNow()->toDateFormat();
                    $value->save();
                }
            }
        } else {
            foreach ($warriors as $key => $value) {
                if (!$value->rest) {
                    return Response::addMessage('One or more of you warriors is not resting');
                } else {

                    $timestamp_now = $this->countdownService->getTimestampNow();
                    $rest_start = $this->countdownService->getTimestamp($value->rest_start);


                    $health_gained = (($timestamp_now - $rest_start) / 60) * \WARRIOR_REST_PER_MINUTE;
                    $new_health = $value->health + $health_gained;


                    if ($new_health > 100) {
                        $new_health = 100;
                    }
                    $value->rest = 0;
                    $value->health = $new_health;
                    $value->save();
                }
            }
        }

        return Response::addData('warriors', $this->warriorService->createResourceFromCollection())->setStatus(200);
    }

    /**
     * Upgrade Warrior level
     *
     * @param \App\libs\Request $request
     *
     * @return Response
     */
    public function upgradeWarriorLevel(Request $request, CanLevelUpAction $canLevelUpAction)
    {
        $warrior_id = $request->getInput('warrior_id');

        $request->validate([
            'warrior_id' => Validator::intVal()
        ]);

        $warrior = $this->warriorService->getAvailableWarriorsWithRelations([$warrior_id])->first();

        if (!$this->checkWarriorExist([$warrior_id])) {
            return $this->logWarriorsNotExist();
        }
        $levels_updated = false;

        if (
            $canLevelUpAction->handle($warrior->levels->precision_xp, $warrior->levels->precision_next_level_xp)
        ) {
            $warrior->levels->precision_level += 1;
            $levels_updated = true;
        }

        if (
            $canLevelUpAction->handle($warrior->levels->technique_xp, $warrior->levels->technique_next_level_xp)
        ) {
            $warrior->levels->technique_level += 1;
            $levels_updated = true;
        }

        if (
            $canLevelUpAction->handle($warrior->levels->stamina_xp, $warrior->levels->stamina_next_level_xp)
        ) {
            $warrior->levels->stamina_level += 1;
            $levels_updated = true;
        }

        if (
            $canLevelUpAction->handle($warrior->levels->strength_xp, $warrior->levels->strength_next_level_xp)
        ) {
            $warrior->levels->strength_level += 1;
            $levels_updated = true;
        }

        if ($levels_updated) {
            // \var_dump($warrior->levels->strength_level);
            $warrior->save();
            $resource = new WarriorResource($warrior);
            return Response::addData('warrior', $resource->toArray())->setStatus(200);
        } else {
            // TODO: Correct response code
            return Response::addMessage("Warrior levels does need to be updated")->setStatus(200);
        }
    }


    /**
     * Change warrior type
     *
     * @param \App\libs\Request $request
     *
     * @return Response
     */
    public function changeWarriorType(Request $request)
    {
        $new_warrior_type = $request->getInput('new_warrior_type');
        $warrior_id = $request->getInput('warrior_id');

        $request->validate([
            'warrior_id' => Validator::intVal(),
            'new_warrior_type' => Validator::in(\WARRIOR_TYPES),
        ]);

        $warrior = $this->warriorService->getAvailableWarriors([$warrior_id])->first();

        if (!$this->checkWarriorExist([$warrior_id])) {
            return $this->logWarriorsNotExist();
        }

        if ($warrior->type === $new_warrior_type) {
            return Response::addMessage("Your warrior already has the selected type")->setStatus(400);
        }

        // TODO: Insert this into database?
        $warrior_type_prices = array("ranged" => 600, "melee" => 500);
        $price = $warrior_type_prices[$new_warrior_type];

        if (!$this->inventoryService->hasEnoughAmount(\CURRENCY, $price)) {
            return $this->inventoryService->logNotEnoughAmount(\CURRENCY);
        }
        $warrior->type = $new_warrior_type;
        $warrior->save();
        $this->inventoryService->edit(\CURRENCY, $price);

        $resource = new WarriorResource($warrior);

        return Response::addMessage("Warrior type changed to {$new_warrior_type}")
            ->addData('warrior', $resource->toArray())
            ->setStatus(200);
    }

    /**
     * Start warrrior training
     *
     * @param \App\libs\Request $request
     *
     * @return Response
     */
    public function startTraining(Request $request)
    {
        $training_type = $request->getInput('training_type');
        $warrior_ids = $request->getInput('warrior_ids');

        $request->validate([
            'warrior_ids' => Validator::arrayType()->notEmpty(),
            'training_type' => Validator::in(\WARRIOR_TRAINING_TYPES)
        ]);

        $warriors = $this->warriorService->getAvailableWarriors($warrior_ids);

        if (!$this->checkWarriorExist($warrior_ids)) {
            return $this->logWarriorsNotExist();
        }

        $training_type_data = TrainingTypeData::where('training_type', $training_type);
        if (empty($training_type_data)) {
            return Response::addMessage("Unvalid training type");
        }

        // Data will be the same for all the selected warriors
        $warrior = $this->warriorService->warriors[0];

        $new_time = $this->countdownService
            ->getNow()
            ->addSeconds($training_type_data[0]['time'])
            ->toDateFormat();


        $warrior->training_countdown = $new_time;
        $warrior->training_type = $training_type;
        $warrior->fetch_report = 1;
        $warrior->save();

        return Response::setStatus(200);
    }

    /**
     * End warrior training
     *
     * @param \App\libs\Request $request
     *
     * @return Response
     */
    public function endTraining(Request $request)
    {
        $warrior_id = $request->getInput('warrior_id');

        $request->validate([
            'warrior_id' => Validator::intVal(),
        ]);

        $warriors = $this->warriorService->getAvailableWarriorsWithRelations([$warrior_id]);

        if (!$this->checkWarriorExist([$warrior_id])) {
            return $this->logWarriorsNotExist();
        }

        $warrior = $warriors[0];

        if (!$this->countdownService->hasTimestampPassed($warrior->training_countdown)) {
            return Response::addMessage("The training is not finished")->setStatus(422);
        }

        if ($warrior->fetch_report === 0) {
            return Response::addMessage("You don't have any training active")->setStatus(422);
        }

        $training_type_data = TrainingTypeData::where('training_type', $warrior->training_type);
        if (empty($training_type_data)) {
            return Response::addMessage("Unvalid training data")->setStatus(422);
        }

        $new_earned_xp = rand(25, 35);

        switch ($warrior->training_type) {
            case 'strength':
                $warrior->levels->strength_xp += $new_earned_xp;
                break;

            case 'technique':
                $warrior->levels->technique_xp += $new_earned_xp;
                break;

            case 'precision':
                $warrior->levels->precision_xp += $new_earned_xp;
                break;

            case 'stamina':
                $warrior->levels->stamina_xp += $new_earned_xp;
                break;
            default:
                throw new Exception("Unvalid training type");
                break;
        }

        $warrior->fetch_report = 0;
        $warrior->training_type = 'none';

        $this->skillsService
            ->updateWarriorXP(40)
            ->updateSkills();

        return Response::setData($this->warriorService->warriors[0]->build()->toArray());
    }

    public function testSkills(Request $request)
    {

        $this->skillsService
            ->updateMinerXP(50)
            ->updateFarmerXP(50)
            ->updateSkills();

        return Response::setStatus(200);
    }

    private function checkWarriorExist(array $warrior_ids)
    {
        if (count($warrior_ids) > $this->warriorService->warrior_count) {
            return false;
        }

        return true;
    }

    private function logWarriorsNotExist()
    {
        return Response::addMessage("One or more of you warriors is unavailable")->setStatus(422);
    }
}
