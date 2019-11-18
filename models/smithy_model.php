<?php
    class smithy_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getData() {
            $data = array();
            $sql = "SELECT item, amount_required, cost FROM smithy_data ORDER BY amount_required";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $minerals = array("iron", "steel", "clay", "gargonite", "adron", "yedqon", "frajrite");
            foreach($minerals as $key) {
                $mineral = $key;
                $data[$key] = array_filter($row, function($key) use ($mineral) {
                    return stripos($key['item'], $mineral) !== false;
                });
            }
            return $data;
        }
        public function smith($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from smithy.js
            // Function to smith items from minerals
            $item = strtolower($POST['item']);
            $mineral = strtolower($POST['mineral']);
            $amount = $POST['amount'];
            $ore = $mineral . ' ' . 'ore';
            $minerals = array("iron", "steel", "gargonite", "adron", "yeqdon", "frajrite");
            if(in_array($mineral, $minerals) == false) {
                $this->gameMessage("ERROR: You are not allowed to smith from that mineral", true);
                return false;
            }
            
            $sql = "SELECT item, amount FROM inventory WHERE (item=:item OR item='gold') AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $ore;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 1) {
                $this->gameMessage("ERROR: You missing one or more items in your inventory", true);
                return false;
            }
            
            $sql = "SELECT amount_required, level, cost FROM smithy_data WHERE item=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = $mineral . ' ' . $item;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: That item does not exist!", true);
                return false;
            }
            if($row2['level'] > $this->session['miner']['miner_level']) {
                $this->gameMessage("ERROR: Your level is too low", true);
                return false;
            }
            
            $minerals_needed = $row2['amount_required'] * $amount;
            if($row2['amount_required'] * $amount > $row[0]['amount']) {
                $this->gameMessage("ERROR! You dont have enough ores", true);
                return false;
            }
            $cost = $row2['cost'] * $amount;
            if($row[1]['amount'] < $cost) {
                $this->gameMessage("ERROR! You don't have enough gold", true);
                return false;
            }
            $new_item = $mineral . ' ' . $item;
    
            try {
                $this->db->conn->beginTransaction();   
                update_inventory($this->db->conn, $this->username, $new_item, $amount);
                if($this->session['profiency'] !== 'miner') {
                    update_inventory($this->db->conn, $this->username, 'gold', -$cost);    
                }
                update_inventory($this->db->conn, $this->username, $ore , -$minerals_needed, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
            get_inventory($this->db->conn, $this->username);
        }
    }
?>