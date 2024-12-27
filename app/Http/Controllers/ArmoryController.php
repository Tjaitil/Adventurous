<?php

namespace App\Http\Controllers;

use App\Enums\ArmoryParts;
use App\Enums\SkillNames;
use App\Exceptions\InventoryFullException;
use App\Models\ArmoryItemsData;
use App\Models\Soldier;
use App\Models\SoldierArmory;
use App\Models\User;
use App\Services\ArmoryService;
use App\Services\GameLogService;
use App\Services\InventoryService;
use App\Services\SkillsService;
use App\Services\WarriorService;
use Exception;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Log;

class ArmoryController extends Controller
{
    public function __construct(
        private WarriorService $warriorService,
        private InventoryService $inventoryService,
        private ArmoryService $armoryService,
        private ArmoryItemsData $armoryItemsData,
        private SkillsService $skillsService,
    ) {}

    public function index(): Factory|View
    {
        return view('armory');
    }

    public function getSoldiers(#[CurrentUser()] User $User): JsonResponse
    {
        $ArmoryWarriors = Soldier::select(['id', 'warrior_id', 'type'])
            ->with(['armory'])
            ->where('user_id', $User->id)
            ->get();

        return response()->json($ArmoryWarriors, 200);
    }

    /**
     * Remove armor item
     */
    public function remove(#[CurrentUser] User $User, Request $request): JsonResponse
    {
        $request->validate([
            'warrior_id' => 'required|numeric|min:1',
            'is_removing' => 'required|boolean',
            'part' => ['required', Rule::enum(ArmoryParts::class)],
        ]);

        $armoryPart = $request->enum('part', ArmoryParts::class);
        if ($armoryPart === null || $armoryPart == ArmoryParts::HAND) {
            return response()->jsonWithGameLogs([],
                [
                    GameLogService::addErrorLog('Invalid part.'),
                ], 400
            );
        }

        $Armory = SoldierArmory::where('warrior_id', $request->input('warrior_id'))->first();

        if (! $Armory instanceof SoldierArmory) {
            return response()->jsonWithGameLogs(
                [],
                GameLogService::addWarningLog('Something went wrong. Please try again later.'),
                400
            );
        }
        try {
            $item = $Armory->{$armoryPart->value};
            $ArmoryItem = ArmoryItemsData::where('item', $item)->first();
            if ($item === null || ! $ArmoryItem instanceof ArmoryItemsData) {
                return response()->jsonWithGameLogs(
                    [], GameLogService::addWarningLog('No item to remove.'), 400
                );
            }
            $amount = 1;

            if ($armoryPart === ArmoryParts::AMMUNITION) {
                $amount = $Armory->ammunition_amount;
            }

            $removedItems = $this->armoryService->changeSoldierArmory(
                true,
                $ArmoryItem->item,
                $amount,
                null,
                $armoryPart,
                $Armory,
            );

            foreach ($removedItems as $key => $removedItem) {
                $this->inventoryService->edit($User->inventory, $removedItem->name, $removedItem->amount, $User->id);
            }
            $Armory->save();

            $SoldierArmoryResponse = [
                'warrior_id' => $Armory->soldier->warrior_id,
                'type' => $Armory->soldier->type,
                'armory' => $Armory->toArray(),
            ];

            return response()->json($SoldierArmoryResponse, 200);
        } catch (InventoryFullException $e) {
            return response()->jsonWithGameLogs([], [GameLogService::addErrorLog('Inventory is full')], 400);
        } catch (Exception $e) {
            Log::error('Error while changing warrior part', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->jsonWithGameLogs([], [GameLogService::addErrorLog('Something went wrong. Please try again later.')], 400);
        }
    }

    public function add(#[CurrentUser] User $User, Request $request): JsonResponse
    {
        $request->validate([
            'warrior_id' => 'required|numeric|min:1',
            'item' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'hand' => ['nullable', Rule::in(['right_hand', 'left_hand'])],
        ]);

        $item = strtolower($request->input('item'));

        $amount = (int) $request->input('amount', 1);
        $hand = $request->input('hand', null);

        $ArmoryItem = $this->armoryService->isValidArmoryItem($item);
        if ($ArmoryItem === false) {

            return response()->jsonWithGameLogs(
                [],
                [
                    GameLogService::addErrorLog('Invalid item.'),
                ],
                400
            );
        }

        if (! $this->inventoryService->hasEnoughAmount($User->inventory, $ArmoryItem->item, $amount)) {

            return $this->inventoryService->logNotEnoughAmount($ArmoryItem->item);
        }

        $armoryPart = $ArmoryItem->type;

        $Armory = SoldierArmory::where('warrior_id', $request->input('warrior_id'))->first();

        if (! $Armory instanceof SoldierArmory) {
            return response()->jsonWithGameLogs(
                [],
                [
                    GameLogService::addErrorLog('Something went wrong. Please try again later.'),
                ],
                400
            );
        }

        try {
            if (! $this->warriorService->isSoldiersAvailable(collect([$Armory->soldier]))) {

                return $this->warriorService->logWarriorsNotAvailable();
            }

            $armoryPart = $ArmoryItem->type;

            if (! $this->armoryService->hasCorrectSoldierTypeForItem($Armory->soldier, $ArmoryItem)) {

                return response()->jsonWithGameLogs(
                    [],
                    [
                        GameLogService::addWarningLog('This warrior cannot wear the requested item'),
                    ],
                    400
                );
            }

            $unlockable_status = $this->armoryService->isItemUnlocked($ArmoryItem, $User->player);

            if (! $unlockable_status) {
                $mineral = ArmoryItemsData::getMineralFromItem($ArmoryItem);

                return response()->jsonWithGameLogs([], [GameLogService::addWarningLog("You need to unlock $mineral items first")], 400);
            }

            if (! $this->skillsService->hasRequiredLevel($User->userLevels, $ArmoryItem->level, SkillNames::WARRIOR)) {

                return $this->skillsService->logNotRequiredLevel(SkillNames::WARRIOR->value);
            }

            $removedItems = $this->armoryService->changeSoldierArmory(
                false,
                $ArmoryItem->item,
                $amount,
                $hand,
                $armoryPart,
                $Armory,
            );

            foreach ($removedItems as $key => $removedItem) {
                $this->inventoryService->edit($User->inventory, $removedItem->name, $removedItem->amount, $User->id);
            }

            // Remove item from inventory when adding
            $this->inventoryService->edit($User->inventory, $ArmoryItem->item, -$amount, $User->id);

            $Armory->save();

            $SoldierArmoryResponse = [
                'warrior_id' => $Armory->soldier->warrior_id,
                'type' => $Armory->soldier->type,
                'armory' => $Armory->toArray(),
            ];

            return response()->json($SoldierArmoryResponse, 200);
        } catch (InventoryFullException $e) {
            return response()->jsonWithGameLogs([], [GameLogService::addErrorLog('Inventory is full')], 400);
        } catch (Exception $e) {
            Log::error('Error while changing warrior part', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->jsonWithGameLogs([], [GameLogService::addErrorLog('Something went wrong. Please try again later.')], 400);

        }
    }
}
