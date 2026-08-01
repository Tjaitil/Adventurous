<?php

namespace App\Http\Controllers;

use App\Enums\GameMaps;
use App\Enums\WorldChangeType;
use App\Services\SessionService;
use App\Services\WorldLoaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use UnhandledMatchError;

class WorldLoaderController extends Controller
{
    public function __construct(
        private SessionService $sessionService,
        private WorldLoaderService $worldLoaderService,
    ) {}

    public function changeMap(Request $request): JsonResponse
    {
        $UserData = $this->sessionService->getUserData();
        $method = $request->enum('method', WorldChangeType::class);
        $new_destination = $request->input('new_destination');

        if ($method === WorldChangeType::TRAVEL) {
            $match = false;
            foreach (GameMaps::locationMapping() as $map => $destination) {
                if ($new_destination === $destination) {
                    $match = true;
                    $this->worldLoaderService->setMap($map);
                    break;
                }
            }

            if ($match !== true) {
                Log::error('Map not found: '.$match, ['user_id' => Auth::user()->id]);

                return response()->json([
                    'message' => 'Could not load map',
                ], 422);
            }
        } elseif ($method === WorldChangeType::NEXT_MAP) {
            $newMap = $request->input('new_map');

            if ((abs($newMap['newX']) > 1 || abs($newMap['newY']) > 1)) {
                return response()->json([
                    'message' => 'You are trying to travel to far',
                ], 400);
            } elseif ((abs($newMap['newX']) === 0 && abs($newMap['newY']) === 0)) {
                return response()->json([
                    'message' => 'You are trying to travel to the same place',
                ], 400);
            }

            $split_array = explode('.', $UserData->map_location);
            $split_array[0] = intval($split_array[0]);
            $split_array[1] = intval($split_array[1]);

            if ($newMap['newX'] != 0) {
                $split_array[0] += $newMap['newX'];
            } elseif ($newMap['newY'] != 0) {
                $split_array[1] += $newMap['newY'];
            }

            $newMapKey = \implode('.', $split_array);

            if (! in_array($newMapKey, GameMaps::getMaps())) {
                Log::error('Map not found: '.$newMapKey, ['user_id' => Auth::user()->id]);

                return response()->json([
                    'message' => 'Failed to load map',
                ], 422);
            }

            $new_destination = GameMaps::locationMapping()[$newMapKey] ?? null;
            $this->worldLoaderService->setMap($newMapKey);
        } else {
            $respawnMap = match ($UserData->map_location) {
                '4.2' => '4.3',
                '6.2' => '5.2',
                '8.3' => '8.2',
                '3.10' => '4.9',
                default => throw new UnhandledMatchError,
            };
            $this->worldLoaderService->setMap($respawnMap);
        }

        $UserData->map_location = $this->worldLoaderService->getMap();
        $UserData->save();

        $result = $this->worldLoaderService->getWorldData();
        if ($result === false) {
            Log::error('File not found: '.$this->worldLoaderService->getMap().'.json', ['user_id' => Auth::user()->id]);

            return response()->json([], 422);
        }

        return response()->json($result, 200);
    }

    public function loadWorld(): JsonResponse
    {
        $this->worldLoaderService->setMap($this->sessionService->getCurrentMap());

        $result = $this->worldLoaderService->getWorldData();
        if ($result === false) {
            Log::error('File not found for map', ['user_id' => Auth::user()->id]);

            return response()->json($result, 422);
        }

        return response()->json($result, 200);
    }
}
