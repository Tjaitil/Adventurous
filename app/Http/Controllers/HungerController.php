<?php

namespace App\Http\Controllers;

use App\libs\controller;
use Respect\Validation\Validator;
use App\libs\Request;
use App\libs\Response;
use App\Models\HealingItem;
use App\Models\Hunger;
use App\Services\HungerService;
use App\Services\InventoryService;
use App\Services\SessionService;

class HungerController extends controller
{
    public function __construct(
        private InventoryService $inventoryService,
        private SessionService $sessionService,
        private HungerService $hungerService,
    ) {
        parent::__construct();
    }



    /**
     * Get hunger
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getHunger(Request $request)
    {
        $Hunger = Hunger::where('user_id', $this->sessionService->user_id())->first();

        if (!$Hunger instanceof Hunger) {
            return Response::addMessage("Unvalid user")->setStatus(400);
        }

        return Response::addData('current_hunger', $Hunger->current);
    }



    /**
     * Restore hunger
     *
     * @param Request $request
     *
     * @return Response
     */
    public function restoreHealth(Request $request)
    {
        $item = $request->getInput('item');
        $amount = $request->getInput('amount');

        $request->validate([
            'item' => Validator::stringVal()->notEmpty(),
            'amount' => Validator::intVal(),
        ]);

        if ($this->inventoryService->hasEnoughAmount($item, $amount) === false) {
            return Response::addMessage("You do not have enough of that item")->setStatus(422);
        }

        if ($this->hungerService->getCurrentHunger() >= 100) {
            return Response::addMessage("You are already at max health")->setStatus(422);
        }

        $HealingItem = HealingItem::where('item', $item)->first();

        if (!$HealingItem instanceof HealingItem) {
            return Response::addMessage("That item does not heal you")->setStatus(422);
        }

        $this->hungerService->decreaseHunger($amount);

        $this->inventoryService->edit($item, -$amount);

        return Response::addMessage("You have been healed")
            ->addData('hunger', $this->hungerService->getCurrentHunger())
            ->setStatus(200);
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

        $HealingItem = HealingItem::where('item', $item)->first();

        return Response::addData('heal', $HealingItem->heal ?? 0);
    }
}
