<?php
    class eventLoader_model extends model {
        public $session;
        public $maps = array();
        function __construct ($session) {
            parent::__construct();
            $this->session = $session;
        }
        public function loadEventPositions($location) {
            $filename = $filename = "../gamedata/events/" . $location . '_events.json';
            if(file_exists($filename)) {
                $array = json_decode(file_get_contents($filename), true);
                return json_encode($array['positions']);
            }
            else {
                return json_encode(array());
            }
        }
        public function loadEvent($event) {
            $filename = "../gamedata/events/" . $this->session['map_location'] . '_events.json';
            if(file_exists($filename)) {
                $array = json_decode(file_get_contents($filename), true);
                echo json_encode($array['events'][trim($event)]);
            }
            else {
                echo "ERROR";
            }
        }
    }
?>