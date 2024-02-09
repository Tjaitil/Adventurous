<?php

namespace App\Http\Controllers;

use App\Exceptions\JsonException;
use App\Models\HealingItem;
use App\Models\Hunger;
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
    ) {
    }

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

        $validator = Validator::make($request->all(), [
            'item' => 'required|string',
            'amount' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return new JsonResponse('Invalid input', 422);
        }

        if ($this->inventoryService->hasEnoughAmount($item, $amount) === false) {
            return advResponse([], 422)->addErrorMessage('You do not have enough of that item');
        }

        if ($this->hungerService->getCurrentHunger() >= 100) {
            return advResponse([], 422)->addErrorMessage('You are already at max health');
        }

        $HealingItem = HealingItem::where('item', $item)->first();

        if (! $HealingItem instanceof HealingItem) {
            return advResponse([], 422)->addErrorMessage('That item does not heal you');
        }

        $this->hungerService->decreaseHunger($amount);

        $this->inventoryService->edit($item, -$amount);

        return advResponse(['hunger' => $this->hungerService->getCurrentHunger()], 200)
            ->addSuccessMessage('You have been healed');
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

        $heal = $HealingItem?->heal ?? 0;

        return new JsonResponse(['heal' => $heal]);
    }
}
