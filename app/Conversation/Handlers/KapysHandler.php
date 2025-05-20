<?php

namespace App\Conversation\Handlers;

use App\Attributes\SelectedConversationOptionValue;
use App\Models\Miner;
use App\Models\MinerPermitCost;
use App\Models\User;
use App\Services\InventoryService;
use Exception;
use Illuminate\Container\Attributes\CurrentUser;

final class KapysHandler extends BaseHandler
{
    protected array $conditionals = [
        'kpsQr#0' => 'currentLocationConditional',
        'kpsQr#1' => 'currentLocationConditional',
    ];

    protected array $replacers = [
        'kpsQrr#0' => [
            ':price' => 'priceReplacer',
        ],
    ];

    protected array $serverEvent = [
        'kpsQrrS' => 'buyPermits',
    ];

    public function __construct(private InventoryService $inventoryService)
    {
        parent::__construct();
    }

    public function priceReplacer(#[CurrentUser] User $User): int
    {
        $location = $User->player->location;
        $MinerPermitCost = MinerPermitCost::where('location', $location)->firstOrFail();

        return $MinerPermitCost->permit_cost;
    }

    /**
     * @return 'r0'|'r1'
     */
    public function buyPermits(#[SelectedConversationOptionValue] string $location, #[CurrentUser] User $User): string
    {
        $Inventory = $User->inventory;
        $MinerPermitCost = MinerPermitCost::where('location', $location)->firstOrFail();

        $hasEnoughGold = $this->inventoryService->hasEnoughAmount($Inventory, config('adventurous.currency'), $MinerPermitCost->permit_cost);
        if (! $hasEnoughGold) {
            return 'r1';
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

        return 'r0';
    }
}
