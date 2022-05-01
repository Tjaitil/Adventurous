<?php
    class gamedata_model extends model {
        public $username;
        public $session;
        public $row;
        
        function __construct ($username) {
            parent::__construct();
            $this->username = $username;
        }
        public function fetchData() {
            $param_username = $this->username;
            $sql = "SELECT username, location, map_location, destination, profiency, hunger, artefact FROM user_data
                    WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            //Workforce data for farmer
            $sql3 = "SELECT workforce_total, avail_workforce FROM farmer_workforce WHERE username=:username";
            $stmt3 = $this->db->conn->prepare($sql3);
            $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt3->execute();
            $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            
            $gold = "";
            $sql4 = "SELECT amount FROM inventory WHERE username=:username AND item='gold'";
            $stmt4 = $this->db->conn->prepare($sql4);
            $stmt4->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt4->execute();
            $stmt4->bindColumn('amount', $gold);
            $stmt4->fetch(PDO::FETCH_ASSOC);
            $row4['gold'] = $gold;
            
            //Select stats for player
            $sql5 = "SELECT adventurer_respect, farmer_level, farmer_xp, miner_level, miner_xp, warrior_level, warrior_xp,
                     trader_level, trader_xp
                     FROM user_levels WHERE username=:username";
            $stmt5 = $this->db->conn->prepare($sql5);
            $stmt5->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt5->execute();
            $row5 = $stmt5->fetch(PDO::FETCH_ASSOC);
            $row2 = array();
            $row2['farmer'] = array("level" => $row5['farmer_level'], "xp" => $row5['farmer_xp']);
            $row2['miner'] = array("level" => $row5['miner_level'], "xp" => $row5['miner_xp']);
            $row2['warrior'] = array("level" => $row5['warrior_level'], "xp" => $row5['warrior_xp']);
            $row2['trader'] = array("level" => $row5['trader_level'], "xp" => $row5['trader_xp']);
            $row2['adventurer'] = $row5['adventurer_respect']; 
            
            $sql6 = "SELECT item, amount FROM inventory WHERE username=:username";
            $stmt6 = $this->db->conn->prepare($sql6);
            $stmt6->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt6->execute();
            $row6['inventory'] = $stmt6->fetchAll(PDO::FETCH_ASSOC);
            
            $param_farmer_level = $row5['farmer_level'];
            $param_miner_level = $row5['miner_level'];
            $param_warrior_level = $row5['warrior_level'];
            $param_trader_level = $row5['trader_level'];
            //Get next level xp cap for each skill, OR istedenfor UNION?
            $sql2 = "SELECT next_level FROM level_data WHERE level=:farmer_level
                    UNION ALL
                    SELECT next_level FROM level_data WHERE level=:miner_level
                    UNION ALL
                    SELECT next_level FROM level_data WHERE level=:warrior_level
                    UNION ALL
                    SELECT next_level FROM level_data WHERE level=:trader_level";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":farmer_level", $param_farmer_level, PDO::PARAM_STR);
            $stmt2->bindParam(":miner_level", $param_miner_level, PDO::PARAM_STR);
            $stmt2->bindParam(":warrior_level", $param_warrior_level, PDO::PARAM_STR);
            $stmt2->bindParam(":trader_level", $param_trader_level, PDO::PARAM_STR);
            $stmt2->execute();
            $row2x = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            $row2['farmer']['next_level'] = $row2x[0]['next_level'];
            $row2['miner']['next_level'] = $row2x[1]['next_level'];
            $row2['warrior']['next_level'] = $row2x[2]['next_level'];
            $row2['trader']['next_level'] = $row2x[3]['next_level'];
            /*$row2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);*/
            
            $sql = "SELECT adventure_status FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row7 = $stmt->fetch(PDO::FETCH_ASSOC);
        
            
            $rows = array_merge($row, $row2, $row3, $row4, $row6, $row7);
            
            return $rows;            
        }
        public function getXP () {
            $row = array();
            $row[] = $_SESSION['gamedata']['profiency_xp'];
            $row[] = $_SESSION['gamedata']['profiency_xp_nextlevel'];
            js_echo($row);
        }
        public function checkMarket() {
            $param_username = $this->username;
            $sql = "SELECT box_item FROM offers WHERE offeror=:username AND box_amount > 0";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount() > 0) {
                $this->response->addTo("gameMessage", "You have items waiting at market");
            }
        }
    }
?>