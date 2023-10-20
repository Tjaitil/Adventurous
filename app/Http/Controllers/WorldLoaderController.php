<?php

namespace App\controllers;

use App\enums\GameMaps;
use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\models\UserData;
use App\services\SessionService;

class WorldLoaderController extends controller
{
    public $username;
    public $session;
    private $object_array = array();
    private $map;
    private $changed_location;
    private $objectCollisionData = array();

    function __construct(private SessionService $sessionService)
    {
        parent::__construct();
        $buildingsArray = json_decode(file_get_contents(\ROUTE_ROOT . 'gamedata/buildings.json'), true);
        $buildingsArray = array_filter($buildingsArray['tiles'], function ($object) {
            return (isset($object['properties']));
        });
        $landscapeArray = json_decode(file_get_contents(\ROUTE_ROOT . 'gamedata/landscape.json'), true);
        $landscapeArray = array_filter($landscapeArray['tiles'], function ($object) {
            return (isset($object['properties']));
        });
        $this->objectCollisionData = array_merge($buildingsArray, $landscapeArray);
    }



    /**
     * Change location
     *
     * @param Request $request
     *
     * @return Response
     */
    public function changeMap(Request $request)
    {
        $is_new_map_string = $request->getInput('is_new_map_string');
        $new_destination = null;
        if ($is_new_map_string) {
            $new_destination = $request->getInput('new_destination');

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
                return Response::addMessage("The place you are trying to reach doesn't exists")->setStatus(422);
            }
        } else {
            $newMap = $request->getInput('new_map');

            // Check wether or not the difference is greater than 1. There should not be possible in normal game state to travel more..
            // ... than 1 difference without interference
            if ((abs($newMap['newX']) > 1 || abs($newMap['newY']) > 1)) {
                // Throw error
                return false;
            }
            // If both newX and newY is 0 then the player is trying to access the same place they are.
            else if ((abs($newMap['newX']) === 0 && abs($newMap['newY']) === 0)) {
                // Throw error
                return false;
            }
            $split_array = explode('.', $this->sessionService->getCurrentMap());

            $split_array[0] = intval($split_array[0]);
            $split_array[1] = intval($split_array[1]);
            if ($newMap['newX'] != 0) {
                $split_array[0] += $newMap['newX'];
            } elseif ($newMap['newY'] != 0) {
                $split_array[1] += $newMap['newY'];
            }

            $this->map = $newMap = \implode('.', $split_array);

            if (!in_array($newMap, GameMaps::getMaps())) {
                return Response::addMessage("The place you are trying to reach doesn't exists")->setStatus(422);
            }

            $new_destination = GameMaps::locationMapping()[$newMap] ?? null;

            $this->map = $newMap;
        }

        $UserData = UserData::where('username', [$this->sessionService->getCurrentUsername()])->first();
        $UserData->map_location = $this->map;
        if ($new_destination) {
            $UserData->location = $new_destination;
        }
        $UserData->save();

        $this->loadWorld();

        return Response::setStatus(200);
    }



    /**
     * Load world
     *
     * @return Response
     */
    public function loadWorld()
    {
        $UserData = UserData::where('username', $this->sessionService->getCurrentUsername())->first();
        if (is_null($UserData)) {
            return Response::addMessage("User not found")->setStatus(403);
        }

        $this->map = $UserData->map_location;
        if (!file_exists('../gamedata/' . $this->map . '.json')) {
            $this->session['map_location'] = '3.5';
        }
        $this->loadObjects();

        // $this->model = $this->loadModel('eventLoader', true);
        // $events = $this->model->loadEventPositions($this->map);

        return Response::setData([
            'current_map' => strval($this->map),
            'changed_location' => $this->changed_location,
            'map_data' => $this->object_array,
            'events' => []
        ]);
    }



    /**
     * 
     * @return void 
     */
    public function loadObjects()
    {
        $string = json_decode(file_get_contents(\ROUTE_ROOT . 'gamedata/' . $this->map . '.json'), true);
        $objects = array();
        // $objects['title'] = getMapTitle();
        $objects['objects'] = array();

        $objects['buildings'] = array();
        for ($i = 0; $i < count($string['layers']); $i++) {
            if (in_array($string['layers'][$i]['name'], array("Objects", "Buildings", "Characters", "Figures")) === true) {
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
                    if ($string['layers'][$i]['name'] === "Buildings") {
                        $object_array[$x] = $this->setupBuilding($object_array[$x]);
                    }
                    // If the diameter variables is not set, set them. 
                    // Y value in images in json files are the value at the bottom and not up. To get the same base subtract
                    $object_array[$x] = $this->checkRotation($object_array[$x]);
                    if (in_array($object_array[$x]['type'], array('figure', 'object'))) {
                        // if($object_array[$x]['rotation'] === 90) {
                        //     $width = $object_array[$x]['width'];
                        //     $object_array[$x]['x'] = $object_array[$x]['x'] - $object_array[$x]['height'];
                        //     $object_array[$x]['width'] = $object_array[$x]['height'];
                        //     $object_array[$x]['height'] = $width;
                        // }
                        $object_array[$x]['y'] = round($object_array[$x]['y'], 2);
                    } else if ($object_array[$x]['type'] === "daqloon_fighting_area") {
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
                        $objectCollData = array_values(array_filter($this->objectCollisionData, function ($object)
                        use ($objectSrc) {
                            return ($object['image'] == $objectSrc);
                        }));
                    }
                    if (!isset($object_array[$x]['diameterUp'])) {
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
                    } else if (($string['layers'][$i]['name'] === 'Buildings')) {
                        $object_array[$x]['diameterUp'] = $object_array[$x]['y'] + 40;
                        $object_array[$x]['diameterDown'] = $object_array[$x]['y'] + $object_array[$x]['height'];
                        $object_array[$x]['diameterLeft'] = $object_array[$x]['x'];
                        $object_array[$x]['diameterRight'] = $object_array[$x]['x'] + $object_array[$x]['width'];
                    } else {
                        if (!in_array($object_array[$x]['type'], array(
                            'figure', 'start_point', 'daqloon_fighting_area',
                            'desert_dune', 'nc_object', ''
                        ))) {
                            switch ($object_array[$x]['height']) {
                                case 32:
                                    $object_array[$x]['diameterUp'] = $object_array[$x]['y'] + 10;
                                    break;
                                default:
                                    $object_array[$x]['diameterUp'] = $object_array[$x]['y'];
                                    break;
                            }
                            // if(isset($object_array[$x]['src'])) var_dump($object_array[$x]);
                            if (in_array($object_array[$x]['src'], array("crate_3.png", "crate_5.png"))) {
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
                    return ($object['type'] === "daqloon_fighting_area");
                });
            }
        }
        $this->object_array = $objects;
    }



    /**
     * 
     * @param mixed $object 
     * @return mixed 
     */
    private function setupBuilding($object)
    {
        switch ($object['src']) {
            case 'city centre.png':
                $object['src'] = "citycentre.png";
                break;
            case 'army camp.png':
                $object['src'] = "armycamp.png";
                break;
            default:

                break;
        }
        if (!isset($object['displayName'])) {
            $object['displayName'] = trim(substr($object['src'], 0, -4));
        }
        if ($this->map === "9.9") {
            $object['visible'] = false;
        }
        return $object;
    }



    /**
     * 
     * @param mixed $object 
     * @return mixed 
     */
    private function setupCharacter($object)
    {
        // Check conversation
        if (in_array($object['src'], array(
            'Woman character.png', 'Character13.png',
            'Citizen.png'
        ))) {
            $object['conversation'] = false;
        } else {
            $object['conversation'] = true;
        }

        // Set display name
        $display_name = "";
        switch ($object['type']) {
            case 'Woman character.png';
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
     * 
     * @param mixed $object 
     * @return mixed 
     */
    private function checkRotation($object)
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
