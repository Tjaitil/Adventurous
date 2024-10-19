<?php

namespace App\Http\Controllers;

use App\Enums\GameMaps;
use App\Services\SessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WorldLoaderController extends Controller
{
    /**
     * @var array<mixed>
     */
    private array $object_array = [];

    private string $map;

    /**
     * @var array<mixed>
     */
    private array $objectCollisionData = [];

    public function __construct(private SessionService $sessionService)
    {
        $buildingsArray = json_decode(Storage::disk('gamedata')->get('buildings.json') ?? '', true);
        $buildingsArray = array_filter($buildingsArray['tiles'], function ($object) {
            return isset($object['properties']);
        });
        $landscapeArray = json_decode(Storage::disk('gamedata')->get('landscape.json') ?? '', true);
        $landscapeArray = array_filter($landscapeArray['tiles'], function ($object) {
            return isset($object['properties']);
        });
        $this->objectCollisionData = array_merge($buildingsArray, $landscapeArray);
    }

    /**
     * Change location
     */
    public function changeMap(Request $request): JsonResponse
    {
        $UserData = $this->sessionService->getUserData();
        $is_new_map_string = $request->boolean('is_new_map_string');
        $new_destination = null;
        if ($is_new_map_string === true) {
            $new_destination = $request->input('new_destination');

            // Find the map in the index
            $match = false;
            foreach (GameMaps::locationMapping() as $map => $destination) {
                if ($new_destination === $destination) {
                    $match = true;
                    $this->map = $map;
                    break;
                }
            }

            // If match isn't true, it means that the destination does not exists
            if ($match !== true) {
                Log::error('Map not found: '.$match, ['user_id' => Auth::user()->id]);

                return response()->json([
                    'message' => 'Could not load map',
                ], 422);
            }
        } else {
            $newMap = $request->input('new_map');
            // Check wether or not the difference is greater than 1. There should not be possible in normal game state to travel more..
            // ... than 1 difference without interference
            if ((abs($newMap['newX']) > 1 || abs($newMap['newY']) > 1)) {
                // Throw error
                return response()->json([
                    'message' => 'You are trying to travel to far',
                ], 400);
            }
            // If both newX and newY is 0 then the player is trying to access the same place they are.
            elseif ((abs($newMap['newX']) === 0 && abs($newMap['newY']) === 0)) {
                // Throw error

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

            $this->map = $newMap = \implode('.', $split_array);

            if (! in_array($newMap, GameMaps::getMaps())) {
                Log::error('Map not found: '.$newMap, ['user_id' => Auth::user()->id]);

                return response()->json([
                    'message' => 'Failed to load map',
                ], 422);
            }

            $new_destination = GameMaps::locationMapping()[$newMap] ?? null;

            $this->map = $newMap;
        }

        $UserData->map_location = $this->map;
        if ($new_destination) {
            $UserData->location = $new_destination;
        }
        $UserData->save();

        $result = $this->getWorldData();
        if ($result === false) {
            Log::error('File not found: '.$this->map.'.json', ['user_id' => Auth::user()->id]);

            return response()->json([], 422);
        }

        return response()->json($result, 200);
    }

    public function getWorldData()
    {
        $result = $this->loadObjects();
        if ($result === false) {
            return false;
        }

        return [
            'data' => [
                'current_map' => strval($this->map),
                'changed_location' => '',
                'map_data' => $this->object_array,
                'events' => [],
            ],
        ];
    }

    public function loadWorld(): JsonResponse
    {
        $result = $this->getWorldData();
        if ($result === false) {
            return response()->json($result, 422);
        }

        return response()->json([
            'data' => [
                'current_map' => strval($this->map),
                'changed_location' => '',
                'map_data' => $this->object_array,
                'events' => [],
            ],
        ], 200);
    }

    /**
     * @return bool
     */
    public function loadObjects()
    {
        $this->map = $this->sessionService->getCurrentMap();

        $file = Storage::disk('gamedata')->get($this->map.'.json');
        if (is_null($file)) {
            Log::error('File not found: '.$this->map.'.json', ['user_id' => Auth::user()->id]);

            return false;
        }

        $string = json_decode($file, true);

        $objects = [];
        // $objects['title'] = getMapTitle();
        $objects['objects'] = [];

        $objects['buildings'] = [];
        for ($i = 0; $i < count($string['layers']); $i++) {
            if (in_array($string['layers'][$i]['name'], ['Objects', 'Buildings', 'Characters', 'Figures']) === true) {
                $object_array = $string['layers'][$i]['objects'];
                for ($x = 0; $x < count($string['layers'][$i]['objects']); $x++) {
                    unset($object_array[$x]['gid']);
                    unset($object_array[$x]['name']);
                    // If the object has any objects, move the data up in the array
                    if (isset($object_array[$x]['properties'])) {
                        $property_array = $object_array[$x]['properties'];
                        for ($y = 0; $y < count($property_array); $y++) {
                            $object_array[$x][$property_array[$y]['name']] = $property_array[$y]['value'];
                            unset($object_array[$x]['properties']);
                        }
                    }
                    if ($string['layers'][$i]['name'] === 'Buildings') {
                        $object_array[$x] = $this->setupBuilding($object_array[$x]);
                    }
                    // If the diameter variables is not set, set them.
                    // Y value in images in json files are the value at the bottom and not up. To get the same base subtract
                    $object_array[$x] = $this->checkRotation($object_array[$x]);
                    if (in_array($object_array[$x]['type'], ['figure', 'object'])) {
                        // if($object_array[$x]['rotation'] === 90) {
                        //     $width = $object_array[$x]['width'];
                        //     $object_array[$x]['x'] = $object_array[$x]['x'] - $object_array[$x]['height'];
                        //     $object_array[$x]['width'] = $object_array[$x]['height'];
                        //     $object_array[$x]['height'] = $width;
                        // }
                        $object_array[$x]['y'] = round($object_array[$x]['y'], 2);
                    } elseif ($object_array[$x]['type'] === 'daqloon_fighting_area') {
                        $object_array[$x]['y'] = round($object_array[$x]['y']);
                        $object_array[$x]['x'] = round($object_array[$x]['x']);
                        $object_array[$x]['diameterUp'] = $object_array[$x]['y'];
                        $object_array[$x]['diameterDown'] = $object_array[$x]['y'] + $object_array[$x]['height'];
                        $object_array[$x]['diameterLeft'] = $object_array[$x]['x'];
                        $object_array[$x]['diameterRight'] = $object_array[$x]['x'] + $object_array[$x]['width'];
                        $objects['daqloon_fighting_areas'][] = $object_array[$x];
                        unset($object_array[$x]);

                        continue;
                    } else {
                        $object_array[$x]['y'] = round($object_array[$x]['y'], 2) - $object_array[$x]['height'];
                    }
                    if (isset($object_array[$x]['src'])) {
                        $objectSrc = $object_array[$x]['src'];
                        $objectCollData = array_values(array_filter($this->objectCollisionData, function ($object) use ($objectSrc) {
                            return $object['image'] == $objectSrc;
                        }));
                    }
                    if (! isset($object_array[$x]['diameterUp'])) {
                        $object_array[$x]['diameterUp'] = 0;
                        $object_array[$x]['diameterRight'] = 0;
                        $object_array[$x]['diameterDown'] = 0;
                        $object_array[$x]['diameterLeft'] = 0;
                    }
                    $object_array[$x]['x'] = round($object_array[$x]['x'], 2);
                    // Check wether or not there is a diameter preset objects
                    if (isset($objectCollData[0]['objectgroup']['objects'])) {
                        $objectCollData = $objectCollData[0]['objectgroup']['objects'][0];
                        $object_array[$x]['diameterUp'] = $object_array[$x]['y'] + $objectCollData['y'];
                        $object_array[$x]['diameterDown'] = $object_array[$x]['diameterUp']
                            + $objectCollData['height'];
                        $object_array[$x]['diameterLeft'] = $object_array[$x]['x'] + $objectCollData['x'];
                        $object_array[$x]['diameterRight'] = $object_array[$x]['diameterLeft']
                            + $objectCollData['width'];
                    } elseif (($string['layers'][$i]['name'] === 'Buildings')) {
                        $object_array[$x]['diameterUp'] = $object_array[$x]['y'] + 40;
                        $object_array[$x]['diameterDown'] = $object_array[$x]['y'] + $object_array[$x]['height'];
                        $object_array[$x]['diameterLeft'] = $object_array[$x]['x'];
                        $object_array[$x]['diameterRight'] = $object_array[$x]['x'] + $object_array[$x]['width'];
                    } else {
                        if (! in_array($object_array[$x]['type'], [
                            'figure', 'start_point', 'daqloon_fighting_area',
                            'desert_dune', 'nc_object', '',
                        ])) {
                            switch ($object_array[$x]['height']) {
                                case 32:
                                    $object_array[$x]['diameterUp'] = $object_array[$x]['y'] + 10;
                                    break;
                                default:
                                    $object_array[$x]['diameterUp'] = $object_array[$x]['y'];
                                    break;
                            }
                            // if(isset($object_array[$x]['src'])) var_dump($object_array[$x]);
                            if (in_array($object_array[$x]['src'], ['crate_3.png', 'crate_5.png'])) {
                                $object_array[$x]['diameterUp'] = $object_array[$x]['y'] + 10;
                            }
                        } else {
                            $object_array[$x]['diameterUp'] = $object_array[$x]['y'];
                        }
                        $object_array[$x]['diameterDown'] = $object_array[$x]['y'] + $object_array[$x]['height'];
                        $object_array[$x]['diameterLeft'] = $object_array[$x]['x'];
                        $object_array[$x]['diameterRight'] = $object_array[$x]['x'] + $object_array[$x]['width'];
                    }
                    if ($object_array[$x]['type'] == 'character') {
                        $object_array[$x] = $this->setupCharacter($object_array[$x]);
                    }
                    $objects['objects'][] = $object_array[$x];
                }
            }
            if ($string['layers'][$i]['name'] === 'Sides/Corners') {
                $object_array = $string['layers'][$i]['objects'];
                $objects['daqloon_fighting_areas'] = array_filter($object_array, function ($object) {
                    return $object['type'] === 'daqloon_fighting_area';
                });
            }
        }
        $this->object_array = $objects;

        return true;
    }

    /**
     * @param  array<mixed>  $object
     * @return array<mixed>
     */
    private function setupBuilding(array $object): array
    {
        switch ($object['src']) {
            case 'city centre.png':
                $object['src'] = 'citycentre.png';
                break;
            case 'army camp.png':
                $object['src'] = 'armycamp.png';
                break;
            default:

                break;
        }
        if (! isset($object['displayName'])) {
            $object['displayName'] = trim(substr($object['src'], 0, -4));
        }
        if ($this->map === '9.9') {
            $object['visible'] = false;
        }

        return $object;
    }

    /**
     * @param  array<mixed>  $object
     * @return array<mixed>
     */
    private function setupCharacter(array $object): array
    {
        // Check conversation
        if (in_array($object['src'], [
            'Woman character.png', 'Character13.png',
            'Citizen.png',
        ])) {
            $object['conversation'] = false;
        } else {
            $object['conversation'] = true;
        }

        // Set display name
        $display_name = '';
        switch ($object['type']) {
            case 'Woman character.png':
                $display_name = 'citizen';
                break;
            case 'Character13.png':
                $display_name = 'woman';
                break;
            case 'Citizen.png':
                $display_name = 'citizen';
                break;
            case 'wujkin soldier.png':
                $display_name = 'soldier';
                break;
            case 'Fansal male v2.png':
                $display_name = 'Fansal male';
                break;
            case 'tutorial_sailor.png':
                $display_name = 'tutorial_sailer';
                break;
            default:
                $display_name = substr($object['src'], 0, -4);
                break;
        }
        $object['displayName'] = $display_name;

        return $object;
    }

    /**
     * @param  array<mixed>  $object
     * @return array<mixed>
     */
    private function checkRotation(array $object): array
    {
        if ($object['rotation'] !== 0) {
            switch ($object['rotation']) {
                case 90:
                    $width = $object['width'];
                    $object['x'] = $object['x'] - $object['height'];
                    $object['width'] = $object['height'];
                    $object['height'] = $width;
                    break;
                case 180:
                    $width = $object['width'];
                    $object['x'] = $object['x'] - $object['width'];
                    $object['y'] = $object['y'] + $object['height'];
                    break;
                default:

                    break;
            }
        }

        return $object;
    }
}
