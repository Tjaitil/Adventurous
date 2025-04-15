<?php

namespace App\Conversation\ServerEvents;

use App\Models\Miner;
use App\Models\MinerPermitCost;
use App\Models\User;
use App\Services\InventoryService;
use Exception;
use Illuminate\Container\Attributes\CurrentUser;

final class KapysHandler extends BaseHandler
{
    public function __construct(private InventoryService $inventoryService)
    {
        parent::__construct();
    }

    public function priceReplacer(string $location): int
    {
        $MinerPermitCost = MinerPermitCost::where('location', $location)->firstOrFail();

        return $MinerPermitCost->permit_cost;
    }

    public function buyPermits(string $location, #[CurrentUser] User $User): bool
    {
        $Inventory = $User->inventory;
        $MinerPermitCost = MinerPermitCost::where('location', $location)->firstOrFail();

        $hasEnoughGold = $this->inventoryService->hasEnoughAmount($Inventory, config('adventurous.currency'), $MinerPermitCost->permit_cost);
        if (! $hasEnoughGold) {
            return false;
        }

        $Inventory = $User->inventory;
        $Miner = Miner::where('user_id', $User->id)
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

        $this->inventoryService->edit($Inventory, config('adventurous.currency'), -$MinerPermitCost->permit_cost, $User->id);

        $Miner->permits += $MinerPermitCost->permit_amount;
        $Miner->save();

        return true;
    }
}
