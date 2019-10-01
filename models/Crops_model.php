<?php 
    class Crops_model extends model  {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
            
        public function getData() {
                $data = array();
                $sql = "SELECT fields_avail FROM farmer WHERE location=:location AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_location = $this->session['location'];
                $param_username = $this->username;
                $stmt->execute();
                $data['fields'] = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $sql2 = "SELECT crop_type FROM crops_data WHERE farmer_level <=:farmlevel AND location=:location";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":farmlevel", $param_farm_level, PDO::PARAM_STR);
                $stmt2->bindParam(":location", $param_location, PDO::PARAM_STR);
                $param_farm_level = $this->session['farmer']['level'];
                $param_location = $this->session['location'];
                $stmt2->execute();
                $data['crop_types'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                
                $sql3 = "SELECT avail_workforce, efficiency_level FROM farmer_workforce WHERE username=:username";
                $stmt3 = $this->conn->prepare($sql3);
                $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt3->execute();
                $data['workforce_data'] = $stmt3->fetch(PDO::FETCH_ASSOC);
                
                return $data;
            }
            
        public function checkCountdown($check = false) {
            $sql = "SELECT grow_countdown, plot1_harvest FROM farmer WHERE username=:username AND location=:location";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_username = $this->username;
            $param_location = $this->session['location'];
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $date = date_timestamp_get(new DateTime($row['grow_countdown']));
            
            if($check == true) {
                $now = date_timestamp_get(new DateTime(date("Y-m-d H:i:s")));
                if($now > $date) {
                    return true;
                }
                else if($now < $date) {
                    return false;
                }
            }
            $this->closeConn();
            js_echo(array($date, $row['plot1_harvest']));
        }
        
        public function destroyCrops() {
            if(in_array($this->session['location'], array('towhar', 'krasnur')) != true) {
                return false;   
            }
            $workforce = $this->session['location'] . '_workforce';
            
            $sql = "SELECT f.grow_countdown, f.grow_quant, f.fields_avail, f.plot1_harvest, fw.avail_workforce, fw.$workforce
                    FROM farmer as f INNER JOIN farmer_workforce as fw ON f.username = fw.username
                    WHERE f.location=:location AND f.username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $date = date("Y-m-d H:i:s");
            $date_timestamp = date_timestamp_get(new DateTime($date));
            $countdown_timestamp = date_timestamp_get(new DateTime($row['grow_countdown']));
            if($date_timestamp > $countdown_timestamp  && $row['plot1_harvest'] === 'false') {
                $this->gameMessage("ERROR: You currently have no crops growing", true);
                return false;
            }
            else if($date_timestamp > $countdown_timestamp && $row['plot1_harvest'] === 'true') {
                $this->gameMessage("ERROR: Why would you destroy full grown crops?", true);
                return false;
            }
            try {
                $this->conn->beginTransaction();
                $sql = "UPDATE farmer as f INNER JOIN farmer_workforce as fw ON f.username = fw.username
                        SET f.grow_type='none', f.grow_quant=0, f.fields_avail=:fields_avail, f.grow_countdown=:grow_countdown
                        f.plot1_harvest='false', fw.avail_workforce=:avail_workforce, fw.$workforce=0
                        WHERE f.location=:location AND f.username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":fields_avail", $param_fields_avail, PDO::PARAM_STR);
                $stmt->bindParam(":grow_countdown", $param_grow_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_INT);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_fields_avail = $row['fields_avail'] + $row['grow_quant'];
                $param_grow_countdown = date("Y-m-d H:i:s");
                $param_avail_workforce = $row['avail_workforce'] + $row[$workforce];
                $param_location = $this->session['location'];
                $param_username = $this->username;
                $stmt->execute();
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->gameMessage("You have destroyed your crops", true);
        }
        public function getSeeds($type, $amount) {
            $item_amount = get_item($this->session['inventory'], $type)['amount'];
            if(!$item_amount  > 0) {
                $this->gameMessage("ERROR: You don't have that item", true);
                return false;
            }
            
            $seed_amount = rand(0,2);
            
            $seed_amount *= $amount;
            
            try {
                $this->conn->beginTransaction();
                
                update_inventory($this->conn, $this->username, $type, - $amount);
                
                if($seed_amount > 0) {
                    update_inventory($this->conn, $this->username, $type . ' seed', $seed_amount);
                }
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->gameMessage("You got {$seed_amount} {$type} seeds", true);
        }
    }
    
?>