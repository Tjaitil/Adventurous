<?php

namespace App\Http\Controllers;

use App\Exceptions\JsonException;
use App\Models\HealingItem;
use App\Models\Hunger;
use App\Services\GameLogService;
use App\Services\HungerService;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HungerController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService,
        private HungerService $hungerService,
    ) {}

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHunger(Request $request)
    {
        $Hunger = Hunger::where('user_id', Auth::user()->id)->first();

        if (! $Hunger instanceof Hunger) {
            throw new JsonException('Unvalid user', 400);
        }

        return new JsonResponse(['current_hunger' => $Hunger->current]);
    }

    /**
     * Restore hunger
     *
     * @return \Illuminate\Http\JsonResponse|\App\Http\Responses\AdvResponse
     */
    public function restoreHealth(Request $request)
    {
        $item = $request->input('item');
        $amount = $request->integer('amount');
        $User = Auth::user();

        $validator = Validator::make($request->all(), [
            'item' => 'required|string',
            'amount' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return new JsonResponse('Invalid input', 422);
        }

        if ($this->inventoryService->hasEnoughAmount($User->inventory,$item, $amount) === false) {
            return advResponse([], 422)->addMessage(
                GameLogService::addErrorLog('You do not have enough of that item'));
        }

        $Hunger = Hunger::where('user_id', Auth::user()->id)->first();
        if (! $Hunger instanceof Hunger) {
            return advResponse([], 422)->addMessage(
                GameLogService::addErrorLog('Unvalid user'));
        }
        if ($Hunger->current >= 100) {
            return advResponse([], 422)->addMessage(
                GameLogService::addErrorLog('You are not hungry'));
        }

        $HealingItem = HealingItem::where('item', $item)->first();

        if (! $HealingItem instanceof HealingItem) {
            return advResponse([], 422)->addMessage(
                GameLogService::addErrorLog('That item does not affect your hunger status'));
        }

        $Hunger = $this->hungerService->decreaseHunger($Hunger, $amount);

        $this->inventoryService->edit($User->inventory, $item, -$amount, $User->id);

        return advResponse(['hunger' => $Hunger->current], 200)
            ->addMessage(GameLogService::addSuccessLog('You eat and you relive hunger'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHealData(Request $request)
    {
        $item = $request->input('item');

        $validator = Validator::make($request->all(), [
            'item' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json('Invalid input', 422);
        }

        $HealingItem = HealingItem::where('item', $item)->first();

        $heal = $HealingItem->heal ?? 0;

        return new JsonResponse(['heal' => $heal]);
    }
}
