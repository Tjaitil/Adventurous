<?php
    class worldLoader_model extends model {
        public $username;
        public $session;
        private $file;
        private $object_file = "../gamedata/objects.json";
        private $map;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        private function updateMap() {
            $sql = "UPDATE user_data SET map_location=:map_location WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":map_location", $param_map_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_map_location = $this->map;
            $param_username = $this->username;
            $stmt->execute();
            $_SESSION['gamedata']['map_location'] = $this->map;
            $this->session['map_location'] = $this->map;
        }
        private function getMap() {
            $maps = array();
            
            if($this->session['map_location'] == "null") {
                $map_location;
                switch($this->session['profiency']) {
                    case 'farmer':
                        $map_location = 'towhar';
                        break;
                    case 'miner':
                        $map_location = 'golbak';
                        break;
                    case 'trader':
                        $map_location = 'Parf';
                        break;
                    case 'warrior':
                        $map_location = 'tasnobil';
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
            if((abs($newMap['new_y']) > 1 || abs($newMap['new_x']) > 1) /*|| ($newMap['new_y'] === 0 && $newMap['new_x'] === 0)*/) {
                // Throw error
                return false;
            }
            $split_array = explode('.', $this->session['map_location']);
            if($newMap['new_x'] != 0) {
                $split_array[0] += $newMap['new_x'];
            }
            elseif($newMap['new_y'] != 0) {
                $split_array[1] += $newMap['new_y'];
            }
            $this->session['map_location'] = $this->map = implode('.', $split_array);
            $this->updateMap();
            $this->loadWorld();
        }
        public function loadWorld() {
            $this->getMap();
            $string = file_get_contents('../gamedata/' . $this->map . '.json');
            $this->loadObjects();
            echo $this->map . '|' . file_get_contents($this->object_file);
            /*$_SESSION['gamedata']['map_location'];*/
        }
        public function loadObjects() {
            $string = json_decode(file_get_contents('../gamedata/' . $this->map . '.json'), true);
            $objects = array();
            $objects['objects'] = array();
            $objects['links'] = array();
            for($i = 0; $i < count($string['layers']); $i++) {
                if(strpos($string['layers'][$i]['name'], "Tile Layer") === false) {
                        $object_array = $string['layers'][$i]['objects'];
                        for($x = 0; $x < count($object_array); $x++) {
                            unset($object_array[$x]['gid']);
                            unset($object_array[$x]['name']);
                            unset($object_array[$x]['rotation']);
                            
                            $object_array[$x]['x'] = round($object_array[$x]['x'], 2);
                            $object_array[$x]['y'] = round($object_array[$x]['y'], 2) - $object_array[$x]['height'];
                            
                            // If the object has any objects, move the data up in the array
                            if(isset($object_array[$x]['properties'])) {
                                $property_array = $object_array[$x]['properties'];
                                for($y = 0; $y < count($property_array); $y++) {
                                    $object_array[$x][$property_array[$y]['name']] = $property_array[$y]['value'];
                                    unset($object_array[$x]['properties']);
                                }
                            }
                            // If the diameter variables is not set, set them. 
                            if(!isset($object_array[$x]['diameterTop'])) {
                                $object_array[$x]['diameterTop'] = $object_array[$x]['y'];
                                $object_array[$x]['diameterRight'] = $object_array[$x]['x'] + $object_array[$x]['width'];
                                $object_array[$x]['diameterDown'] = $object_array[$x]['y'] + $object_array[$x]['height'];
                                $object_array[$x]['diameterLeft'] = $object_array[$x]['x'];
                            }
                            if($object_array[$x]['type'] == "link") {
                                $objects['links'][] = $object_array[$x];
                            }
                            else {
                                $objects['objects'][] = $object_array[$x];
                            }
                            unset($object_array[$x]['type']);
                        }
                }
            }
            // open and edit objects;
            /*$handle = fopen("../gamedata/objects2.json", "w");*/
            $test = file_put_contents($this->object_file, "123");
            $handle = file_put_contents($this->object_file, json_encode($objects, JSON_PRETTY_PRINT));
        }
        public function loadChunks($POST) {
            // Load the next 2 tiles
            $y_difference = $POST['ybase'] - 320 - $POST['yMapMin'];
            $x_difference = $POST['xbase'] - 320 - $POST['xMapMin'];
            
            
            $POST['yMapMin'] = intval($POST['yMapMin']);
            $POST['xMapMin'] = intval($POST['xMapMin']);
            $POST['xMapMax'] = $POST['xMapMin'] + 640;
            $POST['yMapMax'] = $POST['yMapMin'] + 640;
            
            $file = restore_file('objects', true);
            $string = json_decode(file_get_contents('../gamedata/objects2.json'), true);
            $objects = array();
            $string = $string['objects'];
            for($i = 0; $i < count($string); $i++) {

               /*var_dump($string[$i]['y'] > $POST['yMapMax']);
                    var_dump($string[$i]['y'] < $POST['ybase'] + 320);
                    var_dump($y_difference > 0);
                    var_dump($string[$i]['x'] <= $POST['xMapMax'] && $string[$i]['x'] > $POST['xMapMin']);*/
                    
                    // Arguments
                    // #1 checks if string x is greater than xMapMax and less than the new xMapMax ($POST['xbase'] + 320)
                    // and if direction is correct
                    // #2 check if string y is inside the loaded map area
                    // $string[$i]['x'] > $POST['xMapMax'] && $string[$i]['x'] checks wether the string is outside of the set map parameter
                    // $string[$i]['x'] < $POST['xbase'] + 320 checks wether the string is less than the new xbase - 320;
                    // $x_difference > 0 checks wether the 
                    if(
    
                            ((($string[$i]['x'] > $POST['xMapMax'] && $string[$i]['x'] < $POST['xbase'] + 320 && $x_difference > 0)
                                 ||
                                 ($string[$i]['x'] <= $POST['xMapMin'] && $string[$i]['x'] >= $POST['xbase'] - 320 &&
                                  $x_difference < 0))
                             &&
                             $string[$i]['y'] < $POST['yMapMax'] && $string[$i]['y'] > $POST['yMapMin'])
                        ||
                       
                            (
                                (($string[$i]['y'] > $POST['yMapMax'] && $string[$i]['y'] < $POST['ybase'] + 320 && $y_difference > 0)
                                ||
                                 ($string[$i]['y'] < $POST['yMapMin'] && $string[$i]['y'] > $POST['ybase'] - 320 && $y_difference < 0)
                            )
                            &&
                            $string[$i]['x'] <= $POST['xMapMax'] && $string[$i]['x'] > $POST['xMapMin']))
                    {
                        $objects[] = $string[$i];
                    }
            }
            
            echo json_encode($objects);
        }
        /*private function loadObjects() {
            
            $string = json_decode(file_get_contents('../gamedata/pixela.json'), true);
            
            if($version != $string['version']) {
                
            }
        }*/
    }
?>