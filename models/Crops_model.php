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
                $param_location = $this->session['location'];
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->execute();
                $data['crop_types'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
               
                
                $param_username = $this->username;
                $sql3 = "SELECT avail_workforce, efficiency_level FROM farmer_workforce WHERE username=:username";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt3->execute();
                $data['workforce_data'] = $stmt3->fetch(PDO::FETCH_ASSOC);
                if($js === true) {
                    $this->response->addTo("data", $data, array("index" => "data"));
                }
                else {
                    return $data;
                }
        }
        public function checkCountdown($check = false) {
            $param_username = $this->username;
            $param_location = $this->session['location'];
            $sql = "SELECT crop_countdown, plot1_harvest, crop_type FROM farmer WHERE username=:username AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $date = date_timestamp_get(new DateTime($row['crop_countdown']));
            $this->db->closeConn();
            $this->response->addTo("data", $date, array("index" => "date"));
            $this->response->addTo("data", $row['plot1_harvest'], array("index" => "harvest"));
        }
        public function destroyCrops() {
            if(in_array($this->session['location'], array('towhar', 'krasnur')) != true) {
                $this->response->addTo("errorGameMessage", "You are in the wrong location to do this action");
                return false;   
            }
            $workforce = $this->session['location'] . '_workforce';
            
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $sql = "SELECT f.crop_countdown, f.crop_quant, f.plot1_harvest, fw.avail_workforce, fw.$workforce
                    FROM farmer as f INNER JOIN farmer_workforce as fw ON f.username = fw.username
                    WHERE f.location=:location AND f.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $date = date("Y-m-d H:i:s");
            $date_timestamp = date_timestamp_get(new DateTime($date));
            $countdown_timestamp = date_timestamp_get(new DateTime($row['crop_countdown']));
            if($date_timestamp > $countdown_timestamp  && $row['plot1_harvest'] === 'false') {
                $this->response->addTo("errorGameMessage", "You currently have no crops growing");
                return false;
            }
            else if($date_timestamp > $countdown_timestamp && $row['plot1_harvest'] === 'true') {
                $this->response->addTo("errorGameMessage", "Why would you destroy full grown crops?");
                return false;
            }

            try {
                $this->db->conn->beginTransaction();
                $param_crop_countdown = date("Y-m-d H:i:s");
                $param_avail_workforce = $row['avail_workforce'] + $row[$workforce];
                $param_location = $this->session['location'];
                $param_username = $this->username;

                $sql = "UPDATE farmer as f INNER JOIN farmer_workforce as fw ON f.username = fw.username
                        SET f.crop_type='none', f.crop_quant=0,
                        f.crop_countdown=:crop_countdown,
                        f.plot1_harvest=0, fw.avail_workforce=:avail_workforce, fw.$workforce=0
                        WHERE f.location=:location AND f.username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":crop_countdown", $param_crop_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_INT);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->response->addTo("gameMessage", "You have destroyed your crops");
            $this->response->addTo("data", $param_avail_workforce, array("index" => "availWorkforce"));
        }
        public function getSeeds($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to get seeds from crop item
            $type = $POST['type'];
            $amount = $POST['amount'];
            $item_amount = get_item($this->session['inventory'], $type)['amount'];
            if(!$item_amount  > 0) {
                $this->response->addTo("errorGameMessage", "You don't have that item");
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
            $this->response->addTo("gameMessage", "You got {$seed_amount} {$type} seeds");
        }
    }
    
?>