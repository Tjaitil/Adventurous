<?php

namespace App\Conversation\ServerEvents;

use App\Models\Miner;
use App\Models\MinerPermitCost;
use App\Services\InventoryService;
use Exception;
use Illuminate\Support\Facades\Auth;

class BuyPermit
{
    public function __construct(
        private InventoryService $inventoryService)
    {
    }

    public function locationConditional(string $location): bool
    {
        return Auth::user()->player->location === $location;
    }

    public function priceReplacer(string $location): string
    {
        $MinerPermitCost = MinerPermitCost::where('location', $location)->firstOrFail();

        return $MinerPermitCost->permit_cost;
    }

    public function hasEnoughGold(string $location): bool
    {
        $MinerPermitCost = MinerPermitCost::where('location', $location)->firstOrFail();

        return $this->inventoryService->hasEnoughAmount(config('adventurous.currency'), $MinerPermitCost->permit_cost);
    }

    public function buyPermits(string $location): void
    {
        $Miner = Miner::where('user_id', Auth::user()->id)
            ->where('location', $location)
            ->first();

        if (! $Miner instanceof Miner) {
            throw new Exception('Miner model not found');
        }

        $MinerPermitCost = MinerPermitCost::where('location', $location)
            ->first();

        if (! $MinerPermitCost instanceof MinerPermitCost) {
            throw new Exception('MinerPermitCost model not found');
        }

        $this->inventoryService->edit(config('adventurous.currency'), -$MinerPermitCost->permit_cost);

        $Miner->permits += $MinerPermitCost->permit_amount;
        $Miner->save();
    }
}
