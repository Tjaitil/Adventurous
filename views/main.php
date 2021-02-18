<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <?php include(constant("ROUTE_VIEW") . 'head.php');?>
        <link rel="stylesheet" type="text/css" href="<?php echo constant("ROUTE_CSS") . $name; ?>.css" />
    </head>
    <body>
        <header>
            <?php require(constant("ROUTE_VIEW") . 'header.php');?>
            <script src="public/js/time.js"></script>
        </header>
        <section>
            <?php require(constant("ROUTE_VIEW") . 'layout.php');?>
            <div id="public_chat">
            <div id="chat">
                <ul>
                    <?php get_template('chat', $this->data['chat']);?>            
                </ul>
            </div>
            <input type="text" id="text" placeholder="Write message" />
            <button type="button" onclick="chat();"> Chat </button>
            </div>
            <img src="map.png" id="world_map" height="300px" width="500px"/>
            
            <p id="demo"></p>
            <?php if($_SESSION['gamedata']['profiency'] === "none"): ?>
                <script src="public/js/tutorial.js"></script>
            <?php endif;?>
            <div id="profile">
                <span id="profile_header"> Player Card</span></br>
                <img id="profile_picture" src="" height="50%" width="74%" /></br>
                <span id="profile_profiency"><?php echo ucfirst($_SESSION['gamedata']['profiency']) . ' level '
                                                . $_SESSION['gamedata']['profiency_level'];?></span>
                <div id="skill_bar">
                    
                    <div id="skill_bar2">
                        
                    </div>
                    <div id="skill_bar_progress"><span id="progress_value1"><?php echo $_SESSION['gamedata']['profiency_xp'];?></span>
                    &nbsp/&nbsp<span id="progress_value2"><?php echo $_SESSION['gamedata']['profiency_xp_nextlevel']; ?></span></div>
                </div></br>
                <a href="#"> View more profile details >></a>
                <p id="demo"></p>
            </div>
            <div id="town_map">
                <p id="town">Currently staying in:</p><?php echo ucfirst($_SESSION['gamedata']['location']);?></br>
                <a id="town_map_a" href="#">
                    <img src="" width="300" height="200" style="border:1px solid black;"/>
                </a>
                <!--- <script src="cityhandler.js"> --->
                </script>
            </div>
            <div id="countdowns">
                <table>
                    <caption> Countdowns </caption>
                    <thead>
                        <tr>
                            <td> Profiency </td>
                            <td> What </td>
                        </tr>
                    </thead>
                    <tr>
                        <td> Farmer </td>
                        <td><p>Towhar: <?php echo $this->data['countdowns']['farmer'][0];?></p>
                            <p>Cruendo: <?php echo $this->data['countdowns']['farmer'][1];?></p>
                        </td>
                    </tr>
                    <tr>
                        <td> Miner </td>
                        <td><p>Golbak: <?php echo $this->data['countdowns']['miner'][0];?></p>
                            <p>Snerpiir: <?php echo $this->data['countdowns']['miner'][1];?></p>
                        </td>
                    </tr>
                    <tr>
                        <td> Trader </td>
                        <td><?php echo 'Assignment: ' , $this->data['countdowns']['trader'];?></td>
                    </tr>
                    <tr>
                        <td> Warrior </td>
                        <td><p><?php echo 'Armymission: ' , $this->data['countdowns']['warrior']['mission'];?></p>
                            <p><?php echo 'Warrior(s) finished: ' , $this->data['countdowns']['warrior']['finished'];?></p>
                            <p><?php echo 'Warrior(s) training: ' , $this->data['countdowns']['warrior']['training'];?></p>
                            <p><?php echo 'Warrior(s) idle: ' , $this->data['countdowns']['warrior']['idle'];?></p>
                    </tr>
                </table>
            </div>
            <div id="diplomacy">
                <table>
                    <caption> Diplomacy </caption>
                    <thead>
                        <tr>
                            <td> Location </td>
                            <td> Diplomacy </td>
                        </tr>
                    </thead>
                    <tr>
                        <td> Hirtam </td>
                        <td><?php echo $this->data['diplomacy']['hirtam'];?></td>
                    </tr>
                    <tr>
                        <td> Pvitul </td>
                        <td><?php echo $this->data['diplomacy']['pvitul'];?></td>
                    </tr>
                    <tr>
                        <td> Khanz </td>
                        <td><?php echo $this->data['diplomacy']['khanz'];?></td>
                    </tr>
                    <tr>
                        <td> Ter </td>
                        <td><?php echo $this->data['diplomacy']['ter'];?></td>
                    </tr>
                    <tr>
                        <td> Fansal Plains </td>
                        <td><?php echo $this->data['diplomacy']['fansalplains'];?></td>
                    </tr>
                    </tr>
                </table>
            </div>
            <div id="map_container">
                <div id="map">
                    <?php
                    $y = 1;
                    $x = 1;
                    
                    for($i = 0; $i < 0; $i++): ?>
                        <img class="map_img" src="<?php echo constant("ROUTE_IMG") . $x . '.' . $y . '.png';?>" />
                    <?php $x++;
                          if($x == 10) {
                            $x = 1;
                            $y++;
                          }
                    endfor;?>
                </div>
            </div>
            <?php
                $locations = array("cruendo", "golbak", "pvitul", "khanz", "ter", "hirtam", "fansal-plains", "tasnobil");
                
                
                
                $items = array();
                $items[] = array("item" => "potato seed", "price" => 500,
                "locations" => locationItemData(array(
                "cruendo", "1", "1/10",
                "golbak", "4", "1/3",
                "hirtam", "4", "1/2")));
                /*$items[] = array("item" => "potato", "value" => 20,
                                        "locations" => locationItemData(array()));
                    $items[] = array("item" => "watermelon seed", "value" => 20,
                                        "locations" => locationItemData(array()));
                    $items[] = array("item" => "wheat seed", "value" => 20,
                                        "locations" => locationItemData(array()));
                    $items[] = array("item" => "adron ore", "value" => 20,
                                        "locations" => locationItemData(array()));
                    $items[] = array("item" => "yeqdon bar", "value" => 20,
                                        "locations" => locationItemData(array()));*/
                
                // 
                function locationItemData($array) {
                /* $array indexes every third is new place, [0] => "location", [1] => "store_rate", [2] => "amount"
                         * store_rate is a rating from 1 - 4 how rare the item is on the location
                         *
                         */
                $new_array;
                $x = 0;
                for($i = 0; $i < floor(count($array) / 3); $i++) {
                $new_array[] = array("location" => $array[$x], "store_rate" => 
                                           $array[$x + 1], "amount" => $array[$x + 2]);
                $x+= 3;
                }
                return $new_array;
                }
                $location_stores = array();
                for($i = 0; $i < 2; $i++) {
                $location = $locations[$i];
                echo $location;
                
                
                /*$random_item = array_rand($items);*/
                $random_item = $items[0];
                for($x = 0; $x < count($random_item['locations']); $x++) {
                if($random_item['locations'][$x]['location'] == $location) {
                $random_amount_array = explode("/", $random_item['locations'][$x]['amount']);
                $random_item['amount'] = rand($random_amount_array[0], $random_amount_array[1]);
                $random_item['price'] = floor($random_item['price'] *
                                 (1 + ($random_item['locations'][$x]['store_rate'] / 60)));
                // If there are few items add extra to the price
                $random_item['price2'] = floor($random_item['price'] + 
                      ($random_item['price'] / 50 * (1 - ($random_item['amount'] * 0.10)))
                      + rand($random_item['locations'][$x]['store_rate'] 
                      / 125, $random_item['locations'][$x]['store_rate'] / 150));
                break;
                }
                }
                /*echo "</br>";*/
                unset($random_item['locations']);
                $location_stores[$location][] = $random_item;
                }
                print_r($location_stores);
            ?>
            <?php
                // arrayMaker returns and associative array which prevents manually inputing the indexes
                function ArrayMaker($array) {
                    return array("mission" => $array[0], "time" => $array[1],
                                 "reward" => $array[2], "warriors_required" => $array[3]);
                }
                
                $new_missions = array();
                
                // Determine how many missions of each difficulty by using random function
                $easy_missions_amount = 5;
                $medium_missions_amount = 3; 
                $hard_missions_amount = 2;
                $medium_rate = 1.3;
                $hard_rate = 2.1;
                
                // Determine how many trader assigments of each difficulty by using random function
                $easy_assignment_amount;
                $medium_assignment_amount;
                $hard_assigmnent_amount;
                $favor_assignment_amount;
                
               /* $missions = array();
                $missions[] = arrayMaker(array("Accompany merchants from fansal-plains to fagna", 1000, 250, 3));
                $missions[] = arrayMaker(array("Scout for daqloons and Wilsnas shore ", array("fansal-plains"), 1000, 250, 3));
                $missions[] = arrayMaker(array("Protect Duke Howling", 2000, 200, 4));
                $missions[] = arrayMaker(array("Patrol the kingdom of megles", 1000, 200, 3));
                $missions[] = arrayMaker(array("Patrol the kingdom of towheren", 1000, 250, 3));
                $missions[] = arrayMaker(array("Acconmpany Hildi on her tour north of Lenia Bridge", 1000, 250, 2));
                $missions[] = arrayMaker(array("Protect mines in Golbak", 1000, 400, 3));
                $missions[] = arrayMaker(array("Set up outpost at Byshli islands", 2500, 300, 5));
                $missions[] = arrayMaker(array("Protect trading routes form Ter", 2000, 1000, 3));
                $missions[] = arrayMaker(array("Guard mines in snerpiir", 3000, 600, 3));
                $missions[] = arrayMaker(array("Search Heskil mountains for bandits", 3000, 700, 5));
                $missions[] = arrayMaker(array("Guard the docks at fagna", 2598, 500, 3));
                $missions[] = arrayMaker(array("Guard the diplomat from fagna heading to hirtam", 4000, 600, 4));
                $missions[] = arrayMaker(array("Patrol around daqloon islands", 5000, 460, 3));
                $missions[] = arrayMaker(array("Patrol around fansal-plains", 6000, 600, 3));
                $missions[] = arrayMaker(array("Protect traders heading to fagna from north", 4000, 500, 3));
                $missions[] = arrayMaker(array("Guard Lenia bridge", 3000, 600, 3));
                $missions[] = arrayMaker(array("Protect fishermen of the coast of towhar", 2500, 600, 3));
                $missions[] = arrayMaker(array("Guard the docks at Towhar", 2500, 560, 2));
                $missions[] = arrayMaker(array("Stop smuggling routes of the coast of fansal-plains", 4000, 600, 3));
                $missions[] = arrayMaker(array("Locate bandit hideouts", 4500, 800, 3));
                $missions[] = arrayMaker(array("Retrieve stolen merchandise on the Byshli islands", 5000, 800, 3));
                $missions[] = arrayMaker(array("Patrol the streets of Towhar", 4000, 200, 2));
                $missions[] = arrayMaker(array("Patrol the streets of Cruendo", 4000, 250, 3));
                $missions[] = arrayMaker(array("Patrol around Khanz", 2000, 300, 3));
                $missions[] = arrayMaker(array("Protect the caravan with water supply to Khanz", 3000, 200, 3));
                $missions[] = arrayMaker(array("Protect Hirtam from pirates", 4500, 700, 3));
                $missions[] = arrayMaker(array("Protect Pvitul from pirates", 4500, 700, 3));
                $missions[] = arrayMaker(array("Guard Tibs pass", 4250, 800, 3));
                $missions[] = arrayMaker(array("Scout for Daqs activity on Wilsnas shore", 3500, 700, 3));
                
                // armyMissions
                for($i = 0; $i < ($easy_missions_amount + $medium_missions_amount + $hard_missions_amount); $i++) {
                    // Select random mission from $missions array
                    $mission = $missions[array_rand($missions)];
                    if($i < ($easy_missions_amount)) {
                        // Make missions with easy difficulty
                        $mission['difficulty'] = "easy";
                    }
                    else if($i < ($easy_missions_amount + $medium_missions_amount)) {
                        // Make missions with medium difficulty
                        $mission['difficulty'] = "medium";
                        $mission['reward'] =  intval($mission['reward']) * $medium_rate;
                        $mission['time'] = intval($mission['time']) * $medium_rate;
                        $mission['warriors_required'] = round(intval($mission['warriors_required']) * $medium_rate);
                    }
                    else {
                        // Make missions with hard difficulty
                        $mission['difficulty'] = "hard";
                        $mission['reward'] =  intval($mission['reward']) * $hard_rate;
                        $mission['time'] = intval($mission['time']) * $hard_rate;
                        $mission['warriors_required'] = round(intval($mission['warriors_required']) * $hard_rate) + 2;
                    }
                    $new_missions[] = $mission;
                }
                try {
                    $this->db->conn->beginTransaction();
                    $sql = "INSERT INTO armymissions (required_warriors, mission, reward, time, date) 
                            VALUES(:required_warriors, :mission, :reward, :time)";
                    
                    $stmt->bindParam(":required_warriors", $param_required_warriors, PDO::PARAM_INT);
                    $stmt->bindParam(":mission", $param_mission, PDO::PARAM_STR);
                    $stmt->bindParam(":reward", $param_reward, PDO::PARAM_INT);
                    $stmt->bindParam(":time", $param_time, PDO::PARAM_INT);
                    foreach($new_missions as $key => $value) {
                        // $value = mission array, loop through and bind parameters
                        var_dump($value);
                            $param_required_warriors = $value['required_warriors'];
                            $param_mission = $value['mission'];
                            $param_reward = $value['reward'];
                            $param_time = $value['time'];
                            $stmt->execute();
                    }
                    $this->db->conn->commit();
                }
                catch(Exception $e) {
                    $this->errorHandler->reportError($e);
                    return false;
                }*/

            ?>
            <input type="text" />
            <script src="<?php echo constant('ROUTE_JS');?>main.js"></script>
            <div>
                <img class="crystal" src="<?php echo constant("ROUTE_IMG") . 'hirtam crystal2.png';?>" />
                <img class="crystal" src="<?php echo constant("ROUTE_IMG") . 'pvitul crystal.png';?>" />
                <img class="crystal" src="<?php echo constant("ROUTE_IMG") . 'khanz crystal.png';?>" />
                <img class="crystal" src="<?php echo constant("ROUTE_IMG") . 'ter crystal.png';?>" />
                <img class="crystal" src="<?php echo constant("ROUTE_IMG") . 'fansal-plains crystal.png';?>" />
            </div>
        </section>
        <aside>
            <?php
                require(constant('ROUTE_VIEW') . 'aside.php');
            ?>
        </aside>
    </body>            
</html>