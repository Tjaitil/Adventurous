<?php

namespace App\controllers;

use App\libs\controller;
use App\models\Bakery_model;
use App\models\Hunger_model;
use Respect\Validation\Validator;
use App\libs\Request;
use App\libs\Response;
use App\services\InventoryService;
use \Exception;
use \GameConstants;

class HungerController extends controller
{
    public function __construct(
        private Bakery_model $bakery_model,
        private Hunger_model $hunger_model,
        private InventoryService $inventoryService,
    ) {
        parent::__construct();
    }

    /**
     * Restore hunger
     *
     * @param Request $request
     *
     * @return void
     */
    public function restoreHealth(Request $request)
    {
        $item = $request->getInput('item');
        $amount = $request->getInput('amount');

        $request->validate([
            'item' => Validator::stringVal()->notEmpty(),
            'amount' => Validator::intVal(),
        ]);


        try {
            $item_data = $this->bakery_model->find($item);

            if (count($item_data) === 0) {
                return Response::addMessage("That item does not heal you")->setStatus(422);
            }

            if (!$this->inventoryService->hasEnoughAmount(GameConstants::CURRENCY, $item_data['cost'] * $amount)) {
                return $this->inventoryService->logNotEnoughAmount(GameConstants::CURRENCY);
            } else if (!$this->inventoryService->hasEnoughAmount($item, $amount)) {
                return $this->inventoryService->logNotEnoughAmount($item);
            }

            $hunger = $this->hunger_model->get();

            $new_hunger = $hunger + ($item_data['heal'] * $amount);

            if ($new_hunger > 100) {
                $new_hunger = 100;
            }

            $this->hunger_model->update($new_hunger);
        } catch (Exception $e) {
            return Response::addMessage($e->getMessage());
        }
    }

    /**
     * Get heal data for item
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getHealData(Request $request)
    {
        $item = $request->getInput('item');

        $request->validate([
            'item' => Validator::stringVal()->notEmpty()
        ]);

        $item_data = $this->bakery_model->find($item);

        return Response::addData('heal', $item_data['health'] ?? 0);
    }
}
