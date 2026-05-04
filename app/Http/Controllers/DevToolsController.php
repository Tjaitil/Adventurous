<?php

namespace App\Http\Controllers;

use App\Enums\GameMaps;
use App\Exceptions\InventoryFullException;
use App\Http\Responses\AdvResponse;
use App\Models\Hunger;
use App\Models\Item;
use App\Services\InventoryService;
use App\Services\SessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DevToolsController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService,
        private SessionService $sessionService,
        private WorldLoaderController $worldLoaderController,
    ) {}

    public function getLocations(): JsonResponse
    {
        $locationMapping = GameMaps::locationMapping();

        $locations = collect(GameMaps::getMaps())
            ->map(function (string $map) use ($locationMapping) {
                $location = $locationMapping[$map] ?? null;

                return [
                    'label' => $location ? "{$map} ({$location})" : $map,
                    'value' => $map,
                ];
            });

        return response()->json(['data' => $locations]);
    }

    public function getItems(): JsonResponse
    {
        $items = Item::where('in_game', true)->select('name', 'item_id')->orderBy('name')->get();

        return response()->json(['data' => $items]);
    }

    public function giveItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item'   => ['required', 'string'],
            'amount' => ['required', 'integer', 'min:1'],
        ]);

        $user      = Auth::user();
        $inventory = $user->inventory;

        try {
            $this->inventoryService->edit($inventory, $validated['item'], $validated['amount'], $user->id);
        } catch (InventoryFullException) {
            return $this->inventoryService->handleInventoryFull()->toResponse($request);
        }

        return (new AdvResponse(['message' => 'Item given']))->toResponse($request);
    }

    public function getFreezeState(): JsonResponse
    {
        return response()->json([
            'frozen' => (bool) Cache::get('devtools.frozen', false),
        ]);
    }

    public function toggleFreeze(): JsonResponse
    {
        $frozen = ! Cache::get('devtools.frozen', false);
        Cache::put('devtools.frozen', $frozen, now()->addMinutes(30));

        return response()->json(['frozen' => $frozen]);
    }

    public function setUserData(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hunger'               => ['sometimes', 'integer', 'min:0', 'max:100'],
            'stockpile_max_amount' => ['sometimes', 'integer', 'min:1'],
            'frajrite_items'       => ['sometimes', 'boolean'],
            'wujkin_items'         => ['sometimes', 'boolean'],
        ]);

        $userData = $this->sessionService->getUserData();
        Hunger::where('user_id', $userData->id)
            ->update(['current' => $validated['hunger']]);

        foreach ($validated as $field => $value) {
            $userData->$field = $value;
        }

        $userData->save();

        return (new AdvResponse(['message' => 'UserData updated']))->toResponse($request);
    }

    public function teleportToLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'map' => ['required', 'string', 'in:' . implode(',', GameMaps::getMaps())],
        ]);

        $targetMap    = $validated['map'];
        $locationName = GameMaps::locationMapping()[$targetMap] ?? null;

        $userData               = $this->sessionService->getUserData();
        $userData->map_location = $targetMap;
        if ($locationName) {
            $userData->location = $locationName;
        }
        $userData->save();

        $this->worldLoaderController->setMap($targetMap);
        $result = $this->worldLoaderController->getWorldData();

        if ($result === false) {
            return response()->json(['message' => 'Could not load map data'], 422);
        }

        return response()->json($result);
    }
}
