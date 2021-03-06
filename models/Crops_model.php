<?php 
    class Crops_model extends model  {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function getData($js = false) {
                $data = array();
                if($js == true) {
                $sql2 = "SELECT farmer_level, crop_type, time, experience, seed_required, location
                         FROM crops_data ORDER BY farmer_level ASC";

                }
                else {
                    $sql2 = "SELECT crop_type FROM crops_data ORDER BY farmer_level ASC";
                }
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":location", $param_location, PDO::PARAM_STR);
                $param_location = $this->session['location'];
                $stmt2->execute();
                $data['crop_types'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
               
                
                $sql3 = "SELECT avail_workforce, efficiency_level FROM farmer_workforce WHERE username=:username";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt3->execute();
                $data['workforce_data'] = $stmt3->fetch(PDO::FETCH_ASSOC);
                if($js === true) {
                    echo json_encode($data);
                }
                else {
                    return $data;
                }
        }
        public function checkCountdown($check = false) {
            $sql = "SELECT crop_countdown, plot1_harvest, crop_type FROM farmer WHERE username=:username AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_username = $this->username;
            $param_location = $this->session['location'];
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $date = date_timestamp_get(new DateTime($row['crop_countdown']));
            $this->db->closeConn();
            js_echo(array($date, $row['plot1_harvest'], $row['crop_type']));
        }
        public function destroyCrops() {
            if(in_array($this->session['location'], array('towhar', 'krasnur')) != true) {
                $this->errorHandler->reportError(array($this->username, "User in wrong location but still called " . __METHOD__));
                $this->gameMessage("ERROR: Something unexpected happened! Please try again", true);
                return false;   
            }
            $workforce = $this->session['location'] . '_workforce';
            
            $sql = "SELECT f.crop_countdown, f.crop_quant, f.fields_avail, f.plot1_harvest, fw.avail_workforce, fw.$workforce
                    FROM farmer as f INNER JOIN farmer_workforce as fw ON f.username = fw.username
                    WHERE f.location=:location AND f.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $date = date("Y-m-d H:i:s");
            $date_timestamp = date_timestamp_get(new DateTime($date));
            $countdown_timestamp = date_timestamp_get(new DateTime($row['crop_countdown']));
            if($date_timestamp > $countdown_timestamp  && $row['plot1_harvest'] === 'false') {
                $this->gameMessage("ERROR: You currently have no crops growing", true);
                return false;
            }
            else if($date_timestamp > $countdown_timestamp && $row['plot1_harvest'] === 'true') {
                $this->gameMessage("ERROR: Why would you destroy full grown crops?", true);
                return false;
            }
            try {
                $this->db->conn->beginTransaction();
                $sql = "UPDATE farmer as f INNER JOIN farmer_workforce as fw ON f.username = fw.username
                        SET f.crop_type='none', f.crop_quant=0, f.fields_avail=:fields_avail, f.crop_countdown=:crop_countdown
                        f.plot1_harvest='false', fw.avail_workforce=:avail_workforce, fw.$workforce=0
                        WHERE f.location=:location AND f.username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":fields_avail", $param_fields_avail, PDO::PARAM_STR);
                $stmt->bindParam(":crop_countdown", $param_crop_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_INT);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_fields_avail = $row['fields_avail'] + $row['crop_quant'];
                $param_crop_countdown = date("Y-m-d H:i:s");
                $param_avail_workforce = $row['avail_workforce'] + $row[$workforce];
                $param_location = $this->session['location'];
                $param_username = $this->username;
                $stmt->execute();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            // $echo_data holds game data to update data in the client in form of JSON for easier accessibility
            $echo_data = array();
            $this->gameMessage("You have destroyed your crops", true);
            echo "|";
            $echo_data['avail_workforce'] = $param_avail_workforce;
            
            echo json_encode($echo_data);
        }
        public function getSeeds($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to get seeds from crop item
            $type = $POST['type'];
            $amount = $POST['amount'];
            $item_amount = get_item($this->session['inventory'], $type)['amount'];
            if(!$item_amount  > 0) {
                $this->gameMessage("ERROR: You don't have that item", true);
                return false;
            }
            
            $seed_amount = rand(0,2);
            
            $seed_amount *= $amount;
            
            try {
                $this->db->conn->beginTransaction();
                
                if($seed_amount > 0) {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($type . ' seed', $seed_amount);
                }
                // Update inventory
                $this->UpdateGamedata->updateInventory($type, - $amount, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->gameMessage("You got {$seed_amount} {$type} seeds", true);
        }
    }
    
?>