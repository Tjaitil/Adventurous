<?php

namespace App\Http\Controllers;

use App\Exceptions\InventoryFullException;
use App\Http\Responses\AdvResponse;
use App\Models\User;
use App\Services\DaqloonFightingAreaService;
use App\Services\InventoryService;
use App\Services\LootService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * DELIBERATELY WEAK: the server does not verify that a fight happened.
 * The protection is the 'loot' rate limiter (see RouteServiceProvider), a
 * check that the user's current map has a daqloon fighting area, and a
 * capped queue of server-rolled pending drops. Rejections answer with the
 * same response as a success. This is a single player game, so cheating
 * only hurts the cheater. Revisit (signed single-use claim tokens,
 * server-side fight state) if loot ever gains real value.
 */
class DaqloonFightingController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService,
        private DaqloonFightingAreaService $daqloonFightingAreaService,
        private LootService $lootService,
    ) {}

    /**
     * Roll a drop for a killed daqloon. Returns the item name in
     * data.item so the client can spawn it, and queues it as pending.
     */
    public function rollLoot(Request $request, #[CurrentUser] User $User): JsonResponse
    {
        $response = new AdvResponse;

        if ($this->daqloonFightingAreaService->isInFightingArea($User->id)) {
            $item = $this->lootService->roll($User->id);
            if ($item !== null) {
                $response->addData('item', $item);
            }
        }

        return $response->toResponse($request);
    }

    /**
     * Pick up the oldest pending drop. The client does not choose the item.
     */
    public function claimLoot(Request $request, #[CurrentUser] User $User): JsonResponse
    {
        $item = $this->daqloonFightingAreaService->isInFightingArea($User->id)
            ? $this->lootService->peekPending($User->id)
            : null;

        if ($item !== null) {
            try {
                $this->inventoryService->edit($User->inventory()->get(), $item, 1, $User->id);
            } catch (InventoryFullException) {
                return $this->inventoryService->handleInventoryFull()->toResponse($request);
            }
            $this->lootService->consumePending($User->id);
        }

        return (new AdvResponse)->toResponse($request);
    }
}
