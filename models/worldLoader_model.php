<?php
    class worldLoader_model extends model {
        public $username;
        public $session;
        private $file;
        private $object_file = "../gamedata/objects2.json";
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            
        }
        public function JSONfiles() {
            $string = file_get_contents('../gamedata/pixela.json');
            // $this->loadObjects();
            /*echo $string;*/
            /*print_r(json_decode($file, true));*/
            $this->loadObjects();
        }
        public function loadObjects() {
            $string = json_decode(file_get_contents('../gamedata/pixela.json'), true);
            
            $objects = array();
            var_dump(strpos($string['layers'][3]['name'], "Objekt") !== false);
            var_dump($string['layers'][3]['name']);
            for($i = 0; $i < count($string['layers']); $i++) {
                if(strpos($string['layers'][$i]['name'], 'Objekt') !== false) {
                    $object_array = $string['layers'][$i]['objects'];
                    for($x = 0; $x < count($object_array); $x++) {
                        $objects[] = $object_array[$x];
                    }
                }
            }
            // open and edit objects;
            /*$handle = fopen("../gamedata/objects2.json", "w");*/
            file_put_contents($this->object_file, "");
            $handle = file_put_contents($this->object_file, json_encode($objects, JSON_PRETTY_PRINT));
            var_dump($handle);
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
            $string = json_decode(file_get_contents('../gamedata/objects.json'), true);
            $objects = array();
            for($i = 0; $i < count($string); $i++) {
                    /*echo $string[$i]['id'];
                    var_dump($POST['xbase'] <= $string[$i]['x']);
                    var_dump($string[$i]['x'] <= $POST['xMapMin']);
                    var_dump($POST['ybase'] <= $string[$i]['y']);
                    var_dump($string[$i]['y'] >= $POST['yMapMin']);*/
                /*if(($POST['xbase'] <= $string[$i]['x']) &&
                    ($string[$i]['x'] <= $POST['xMapMin'] ) &&
                    ($POST['ybase'] <= $string[$i]['y']) &&
                    ($string[$i]['y'] >= $POST['yMapMin']))  {
                    $objects[] = $string[$i];
                }*/
                echo $string[$i]['id'];


                /*var_dump(($string[$i]['x'] > $POST['xMapMax'] && $x_difference > 0)
                         || ($string[$i]['x'] <= $POST['xMapMin'] && $x_difference < 0));
                var_dump($string[$i]['x'] < $x_difference + $POST['xbase'] + 320);
                var_dump($string[$i]['y'] < $POST['yMapMax'] + $y_difference && $string[$i]['y'] < $POST['yMapMin'] + $y_difference);
                echo "x_difference";
                var_dump($POST['xbase']);
                var_dump($string[$i]['x']);
                var_dump($x_difference);*/
                /*if(((($string[$i]['x'] > $POST['xMapMax'] && $x_difference > 0)
                        || ($string[$i]['x'] <= $POST['xMapMin'] && $x_difference < 0))  &&
                   $string[$i]['x'] < $x_difference + $POST['xbase'] + 320 &&
                   ($string[$i]['y'] < $POST['yMapMax'] + $y_difference && $string[$i]['y'] > $POST['yMapMin'] + $y_difference))
                   ) {
                    $objects[] = $string[$i];
                }*/
                /*var_dump($POST['xbase']);
                var_dump($string[$i]['x'] <= $POST['xMapMin']);
                var_dump($string[$i]['x'] >= $POST['xbase'] - 320);
                var_dump($x_difference < 0);
                var_dump($string[$i]['y'] < $POST['yMapMax'] && $string[$i]['y'] > $POST['yMapMin']);*/
                
                var_dump($string[$i]['y'] > $POST['yMapMax']);
                var_dump($string[$i]['y'] < $POST['ybase'] + 320);
                var_dump($y_difference > 0);
                var_dump($string[$i]['x'] <= $POST['xMapMax'] && $string[$i]['x'] > $POST['xMapMin']);
                
                // Arguments
                // #1 checks if string x is greater than xMapMax and less than the new xMapMax and if direction is correct
                // #2 check if string y is inside the loaded map area
                if(

                        ((($string[$i]['x'] > $POST['xMapMax'] && $string[$i]['x'] < $POST['xbase'] + 320 && $x_difference > 0)
                             ||
                             ($string[$i]['x'] <= $POST['xMapMin'] && $string[$i]['x'] >= $POST['xbase'] - 320 && $x_difference < 0))
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
                
                /*if((($chunkX > $POST['xMapMax'] && $x_difference > 0) ||
                   ($chunkX <= $POST['yMapMin'] && $x_difference < 0) &&
                   ($chunkY > $POST['yMapMin'] && $chunkY < $POST['yMapMax']) &&
                   ($POST['xbase'] + 320 >= $chunkXmax || $POST['xbase'] - 320 <= $chunkYMin))
                   ||
                   (($chunkY > $POST['yMapMax'] && $y_difference > 0) ||
                   ($chunkY <=  $POST['yMapMin'] && $y_difference < 0) &&
                   ($cunnkX > $POST['xMapMin'] && $chunkx < $POST['xMapMax']))) {
                    
                }*/
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