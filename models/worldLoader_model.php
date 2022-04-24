<?php
class worldLoader_model extends model {
        public $username;
        public $session;
        private $object_array = array();
        private $map;
        protected $maps = array("tasnobil" => 2.6,
                                "golbak" => 3.5, "krasnur" => 3.6, "towhar" => 5.7, "fagna" => 7.5, "cruendo" => 6.6,
                                "ter" => 6.3, "snerpiir" => 5.5, "pvitul" => 2.9, "hirtam" => 4.9, "khanz" => 8.2,
                                "fansal-plains" => 4.3);
        private $changed_location;
        private $objectCollisionData = array();
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $buildingsArray = json_decode(file_get_contents('../gamedata/buildings.json'), true);
            $buildingsArray = array_filter($buildingsArray['tiles'], function($object) {
                return (isset($object['properties']));
            });
            $landscapeArray = json_decode(file_get_contents('../gamedata/landscape.json'), true);
            $landscapeArray = array_filter($landscapeArray['tiles'], function($object) {
                return(isset($object['properties']));
            });
            $this->objectCollisionData = array_merge($buildingsArray, $landscapeArray);
        }
        private function updateMap($new_location = false) {
            // Function to update map in the db and location if there is travel functions has been called ($new_location)
            // If the $new_location is not false change the sql query so that location in db will be updated
            if($new_location !== false) {
                $sql = "UPDATE user_data SET location=:location, map_location=:map_location WHERE username=:username";    
            }
            else {
                $sql = "UPDATE user_data SET map_location=:map_location WHERE username=:username";
            }
            $stmt = $this->db->conn->prepare($sql);
            // If the $new_location is not false bind the location parameter
            if($new_location !== false) {
                $param_location = $new_location;
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            }
            $param_map_location = $this->map;
            $param_username = $this->username;
            $stmt->bindParam(":map_location", $param_map_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $_SESSION['gamedata']['map_location'] = $this->map;
            // If the $new_location is not false update the session location to new location
            if($new_location !== false) {
                $_SESSION['gamedata']['location'] = $new_location;
                $this->changed_location = $new_location;
            }
            $this->session['map_location'] = $this->map;
        }
        private function getMap() {
            $maps = array();
            
            if($this->session['map_location'] == "null") {
                $map_location = "";
                switch($this->session['profiency']) {
                    case 'farmer':
                        $map_location = 'towhar';
                        break;
                    case 'miner':
                        // 3.5 = golbak
                        $map_location = '3.5';
                        break;
                    case 'trader':
                        $map_location = 'Parf';
                        break;
                    case 'warrior':
                        $map_location = 'tasnobil';
                        break;
                    case 'none':
                        $map_location = "6.4";
                        break;
                }
                $this->updateMap();
            }
            else {
                $this->map = $this->session['map_location'];
            }
        }
        public function changeMap($GET) {
            $newMap = json_decode($GET['newMap'], true);
            if(is_string($newMap)) {
                // Find the map in the index
                $match = false;
                foreach($this->maps as $key => $value) {
                    if($key === $newMap) {
                        $this->session['map_location'] = $this->map = $value;
                        $match = true;
                        break;
                    }
                }
                // If match isn't true, it means that the destination does not exists
                if($match !== true) {
                    $this->response->addTo("errorGameMessage" , "The place you are trying to reach doesn't exists");
                    return false;
                }
                $this->updateMap($newMap);
            }
            else {                
                // Check wether or not the difference is greater than 1. There should not be possible in normal game state to travel more..
                // ... than 1 difference without interference
                if((abs($newMap['new_x']) > 1 || abs($newMap['new_y']) > 1)) {
                    // Throw error
                    return false;
                }
                // If both new_x and new_y is 0 then the player is trying to access the same place they are.
                else if((abs($newMap['new_x']) === 0 && abs($newMap['new_y']) === 0)) {
                    // Throw error
                    return false;
                }
                $split_array = explode('.', $this->session['map_location']);
                $split_array[0] = intval($split_array[0]);
                $split_array[1] = intval($split_array[1]);
                if($newMap['new_x'] != 0) {
                    $split_array[0] += $newMap['new_x'];
                }
                elseif($newMap['new_y'] != 0) {
                    $split_array[1] += $newMap['new_y'];
                }
                $this->map = implode('.', $split_array);
                // If there is amatch in $this->maps it means the player has reached a new destination
                if(in_array($this->map, $this->maps)) {
                    $new_location = "";
                    foreach($this->maps as $key => $value) {
                        if($value == $this->map) {
                            $new_location = $key;
                          break;
                        }
                    }
                    $this->updateMap($new_location);
                }
                else {
                    $this->updateMap();    
                }
            }
            $this->loadWorld();
        }
        public function locationName() {
            switch($this->session['map_location']) {
                
            }
        }
        public function loadWorld() {
            $this->getMap();
            if(!file_exists('../gamedata/' . $this->map . '.json'))  {
                $this->session['map_location'] = '3.5';
                $this->getMap();
            }
            $this->loadObjects();
            $this->model = $this->loadModel('eventLoader', true);
            $events = $this->model->loadEventPositions($this->map);
            $data = array('currentMap' => strval($this->map),
                       'changedLocation' => $this->changed_location,
                       'mapData' => $this->object_array, 
                       'events' => $events);
            $this->response->addTo("data", $data, array("index" => "data"));
        }
        public function loadObjects() {
            $string = json_decode(file_get_contents('../gamedata/' . $this->map . '.json'), true);
            $objects = array();
            // $objects['title'] = getMapTitle();
            $objects['objects'] = array();
        
            $objects['buildings'] = array();
            for($i = 0; $i < count($string['layers']); $i++) {
                if(in_array($string['layers'][$i]['name'], array("Objects", "Buildings", "Characters", "Figures")) === true) {
                    $object_array = $string['layers'][$i]['objects'];
                    for($x = 0; $x < count($string['layers'][$i]['objects']); $x++) {
                        unset($object_array[$x]['gid']);
                        unset($object_array[$x]['name']);
                        // If the object has any objects, move the data up in the array
                        if(isset($object_array[$x]['properties'])) {
                            $property_array = $object_array[$x]['properties'];
                            for($y = 0; $y < count($property_array); $y++) {
                                $object_array[$x][$property_array[$y]['name']] = $property_array[$y]['value'];
                                unset($object_array[$x]['properties']);
                            }
                        }
                        if($string['layers'][$i]['name'] === "Buildings") {
                            $object_array[$x] = $this->setupBuilding($object_array[$x]);
                        }
                        // If the diameter variables is not set, set them. 
                        // Y value in images in json files are the value at the bottom and not up. To get the same base subtract
                        $object_array[$x] = $this->checkRotation($object_array[$x]);
                        if(in_array($object_array[$x]['type'], array('figure', 'object'))) {
                            // if($object_array[$x]['rotation'] === 90) {
                            //     $width = $object_array[$x]['width'];
                            //     $object_array[$x]['x'] = $object_array[$x]['x'] - $object_array[$x]['height'];
                            //     $object_array[$x]['width'] = $object_array[$x]['height'];
                            //     $object_array[$x]['height'] = $width;
                            // }
                            $object_array[$x]['y'] = round($object_array[$x]['y'], 2);
                        }
                        else if($object_array[$x]['type'] === "daqloon_fighting_area") {
                            $object_array[$x]['y'] = round($object_array[$x]['y']);
                            $object_array[$x]['x'] = round($object_array[$x]['x']);
                            $object_array[$x]['diameterUp'] = $object_array[$x]['y'];
                            $object_array[$x]['diameterDown'] = $object_array[$x]['y'] + $object_array[$x]['height'];
                            $object_array[$x]['diameterLeft'] = $object_array[$x]['x'];
                            $object_array[$x]['diameterRight'] = $object_array[$x]['x'] + $object_array[$x]['width'];
                            $objects['daqloon_fighting_areas'][] = $object_array[$x];
                            unset($object_array[$x]);
                            continue;
                        }
                        else {
                            $object_array[$x]['y'] = round($object_array[$x]['y'], 2) - $object_array[$x]['height'];
                        }
                        if(isset($object_array[$x]['src'])) {                            
                            $objectSrc = $object_array[$x]['src'];
                            $objectCollData = array_values(array_filter($this->objectCollisionData, function ($object) 
                                    use ($objectSrc) {
                                return ($object['image'] == $objectSrc);
                            }));
                        }
                        if(!isset($object_array[$x]['diameterUp'])) {
                            $object_array[$x]['diameterUp'] = 0;
                            $object_array[$x]['diameterRight'] = 0;
                            $object_array[$x]['diameterDown'] = 0;
                            $object_array[$x]['diameterLeft'] = 0;
                        }
                        $object_array[$x]['x'] = round($object_array[$x]['x'], 2);
                        // Check wether or not there is a diameter preset objects
                        if(isset($objectCollData[0]['objectgroup']['objects'])) {
                            $objectCollData = $objectCollData[0]['objectgroup']['objects'][0];
                            $object_array[$x]['diameterUp'] = $object_array[$x]['y'] + $objectCollData['y'];
                            $object_array[$x]['diameterDown'] = $object_array[$x]['diameterUp'] 
                            + $objectCollData['height'];
                            $object_array[$x]['diameterLeft'] = $object_array[$x]['x'] + $objectCollData['x'];
                            $object_array[$x]['diameterRight'] = $object_array[$x]['diameterLeft'] 
                            + $objectCollData['width'];
                        }
                        else if(($string['layers'][$i]['name'] === 'Buildings')) {
                            $object_array[$x]['diameterUp'] = $object_array[$x]['y'] + 40;
                            $object_array[$x]['diameterDown'] = $object_array[$x]['y'] + $object_array[$x]['height'];
                            $object_array[$x]['diameterLeft'] = $object_array[$x]['x'];
                            $object_array[$x]['diameterRight'] = $object_array[$x]['x'] + $object_array[$x]['width'];
                        }
                        else {
                            if(!in_array($object_array[$x]['type'], array('figure', 'start_point', 'daqloon_fighting_area', 
                                                                        'desert_dune', 'nc_object', ''))) {
                                switch($object_array[$x]['height']) {
                                    case 32:
                                        $object_array[$x]['diameterUp'] = $object_array[$x]['y'] + 10;
                                        break;
                                        default:
                                        $object_array[$x]['diameterUp'] = $object_array[$x]['y'];
                                        break;
                                }
                                // if(isset($object_array[$x]['src'])) var_dump($object_array[$x]);
                                if(in_array($object_array[$x]['src'], array("crate_3.png", "crate_5.png"))) {
                                    $object_array[$x]['diameterUp'] = $object_array[$x]['y'] + 10;
                                }
                            }
                            else {
                                $object_array[$x]['diameterUp'] = $object_array[$x]['y'];
                            }
                            $object_array[$x]['diameterDown'] = $object_array[$x]['y'] + $object_array[$x]['height'];
                            $object_array[$x]['diameterLeft'] = $object_array[$x]['x'];
                            $object_array[$x]['diameterRight'] = $object_array[$x]['x'] + $object_array[$x]['width'];
                        }
                        if($object_array[$x]['type'] == 'character') {
                            $object_array[$x] = $this->setupCharacter($object_array[$x]);
                        }
                        $objects['objects'][] = $object_array[$x];
                    }
                }
                if($string['layers'][$i]['name'] === 'Sides/Corners') {
                    $object_array = $string['layers'][$i]['objects'];
                    $objects['daqloon_fighting_areas'] = array_filter($object_array, function($object) {
                        return ($object['type'] === "daqloon_fighting_area");
                    });
                }
            }
            $this->object_array = $objects;
        }
        private function getMapTitle() {
            $search = array_search($this->map, $this->maps);
            if($search) {
                $this->mapTitle = $this->maps[$search];
            }
            else {
                switch ($this->map) {
                    case 5.7:
                        # code...
                        break;
                        default:
                        # code...
                        break;
                    }
            }
        }
        private function setupBuilding($object) {
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
            if(!isset($object['displayName'])) {
                $object['displayName'] = trim(substr($object['src'], 0, -4));
            }
            if($this->map === "9.9") {
                $object['visible'] = false;
            }
            return $object;
        }
        private function setupCharacter($object) {
            // Check conversation
            if(in_array($object['src'], array('Woman character.png', 'Character13.png', 
            'Citizen.png'))) {
                $object['conversation'] = false;
            }
            else {
                $object['conversation'] = true;
            }

            // Set display name
            $display_name = "";
            switch($object['type']) {
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
        private function checkRotation($object) {                       
            if($object['rotation'] !== 0) {
                switch($object['rotation']) {
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
?>