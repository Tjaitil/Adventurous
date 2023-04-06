<?php

namespace App\controllers;

use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\models\ArmoryItemsData;
use App\models\WarriorsArmory;
use App\resources\WarriorArmoryResource;
use App\services\ArmoryService;
use App\services\SessionService;
use App\services\InventoryService;
use App\services\SkillsService;
use App\services\TemplateFetcherService;
use App\services\UnlockableMineralsService;
use App\services\WarriorService;
use Illuminate\Database\Eloquent\Collection;
use Respect\Validation\Validator;

class ArmoryController extends controller
{
    public $data;

    function __construct(
        private WarriorService $warriorService,
        private InventoryService $inventoryService,
        private ArmoryService $armoryService,
        private TemplateFetcherService $templateFetcherService,
        private SessionService $sessionService,
        private WarriorsArmory $warriorsArmory,
        private ArmoryItemsData $armoryItemsData,
        private SkillsService $skillsService,
        private UnlockableMineralsService $unlockableMineralsService,
    ) {
        parent::__construct();
    }

    public function index()
    {
        $collection = new Collection(
            $this->warriorsArmory->with('warrior:warrior_id,type')
                ->where('username', $this->sessionService->getCurrentUsername())->get()
        );
        $this->data['warrior_armory'] = [];
        foreach ($collection as $key => $value) {
            $resource = new WarriorArmoryResource($value);
            $this->data['warrior_armory'][] = $resource->toArray();
        }

        $this->render('armory', 'Armory', $this->data, true, true);
    }

    /**
     * Remove armor item
     *
     * @param Request $request
     *
     * @return Response
     */
    public function remove(Request $request)
    {
        $request->validate([
            'warrior_id' => Validator::NumericVal()->min(1),
            'part' => Validator::StringType()->notEmpty(),
            'is_removing' => Validator::BoolType(),
            'item' => Validator::StringType(),
            'amount' => Validator::NumericVal(),
            'hand' => Validator::StringType(),
        ]);
        return $this->changeArmor($request);
    }

    /**
     * Add armor item
     *
     * @param Request $request
     *
     * @return Response
     */
    public function add(Request $request)
    {
        $request->validate([
            'warrior_id' => Validator::NumericVal()->min(1),
            'part' => Validator::StringType(),
            'is_removing' => Validator::BoolType(),
            'item' => Validator::StringType()->notEmpty(),
            'amount' => Validator::NumericVal(),
            'hand' => Validator::StringType(),
        ]);

        return $this->changeArmor($request);
    }

    /**
     * Change warrior Armor
     *
     * @param Request $request
     *
     * @return Response
     */
    private function changeArmor(Request $request)
    {
        $warrior_id = $request->getInput("warrior_id");
        $part = $request->getInput("part");
        $is_removing = $request->getInput("is_removing");
        $item = strtolower($request->getInput("item"));
        $amount = $request->getInput("amount");
        $hand = $request->getInput("hand");


        $warrior = $this->warriorsArmory->with('warrior')->where('warrior_id', $warrior_id)->firstOrFail();

        if (!$this->warriorService->isWarriorsAvailable([$warrior->warrior])) {
            return $this->warriorService->logWarriorsNotAvailable();
        }

        $item_data = $this->armoryItemsData->where('item', $item)->first();
        if ($is_removing === false) {

            if ($item_data === null) {
                return Response::addMessage("Unvalid item data")->setStatus(400);
            }

            if (!$this->armoryService->hasCorrectWarriorTypeForItem($warrior, $item_data)) {
                return Response::addMessage("This warrior cannot wear the requested item")->setStatus(400);
            }

            // Check if item needs to be unlocked first
            $unlockable_status = true;
            if (\strpos($item_data->item, 'wujkin') !== false) {
                $mineral = "wujkin";
                $unlockable_status = $this->unlockableMineralsService->isWujkinItemUnlocked();
            } else if (\strpos($item_data->item, 'frajrite') !== false) {
                $mineral = "frajrite";
                $unlockable_status = $this->unlockableMineralsService->isFrajriteItemUnlocked();
            }

            if (!$unlockable_status) {
                return $this->unlockableMineralsService->logNotUnlockedMineral($mineral);
            }

            if (!$this->skillsService->hasRequiredLevel($item_data->level, \WARRIOR_SKILL_NAME)) {
                return $this->skillsService->logNotRequiredLevel(\WARRIOR_SKILL_NAME);
            }

            if (!$this->inventoryService->hasEnoughAmount($item, $amount)) {
                return $this->inventoryService->logNotEnoughAmount($item);
            }
        } else {
            $warrior_array = $warrior->toArray();
            if ($warrior_array[$part] === null) {
                return Response::addMessage("You don't have item in this part")->setStatus(400);
            }
        }

        $warrior = $this->armoryService->changeWarriorPart(
            $is_removing,
            $item,
            $amount,
            $hand,
            $item_data,
            $warrior
        );
        if (!$warrior) {
            return Response::addMessage("Unvalid type!")->setStatus(422);
        }

        $warrior->update();

        // Remove old items
        foreach ($this->armoryService->getInventoryItemsToUpdate() as $key => $value) {
            $this->inventoryService->edit($value['item'], $value['amount']);
        }

        // Remove item from inventory when adding
        if ($is_removing === false) {
            $this->inventoryService->edit($item, -$amount);
        }

        $resource = new WarriorArmoryResource($warrior);
        return Response::addTemplate(
            'warrior_armory',
            $this->templateFetcherService->loadTemplate('armory', [$resource->toArray()])
        )->setStatus(200);
    }
}
