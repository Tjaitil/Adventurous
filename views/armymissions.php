            armymissions.css|armymissions.js|
            <h3 class="page_title"> Army Missions </h3>
            <div id="missions" class="cont_close">
                <button> Back to army camp</button>
                <div id="current_mission">
                    <p> Mission:</p><p id="time"></p>
                    <button> Cancel mission </button>
                </div>
                <div id="mission_enabled">
                <img class="cont_exit" src="<?php echo constant("ROUTE_IMG") . 'exit.png';?>" onclick="exit();" />
                    <div id="mission-info" class="mb-1 mt-1 div_content_light div_content">
                        <div class="mission-info-container">
                            <p>Warriors required</p>
                            <p></p>
                        </div>
                        <div class="mission-info-container">
                            <p>Mission</p>
                            <p></p>
                        </div>
                        <div class="mission-info-container">
                            <p>Difficulty</p>
                            <p></p>
                        </div>
                        <div class="mission-info-container">
                            <p>Reward</p>
                            <p></p>
                        </div>
                        <div class="mission-info-container">
                            <p>Minutes</p>
                            <p></p>
                        </div>
                        <div class="mission-info-container">
                            <p>Combat</p>
                            <p></p>
                        </div>
                        <div class="mission-info-container">
                            <p>Location</p>
                            <p></p>
                        </div>
                    </div>
                    <?php
                        // Template
                        echo $this->data['templateWarriorSelect'];
                    ?>
                    <button id="mission_button" onclick="doMission();"> Do Mission </button>
                    <div id="battle_result">
                        
                    </div>
                </div>
                <table>
                    <thead>
                            <td>Warriors required</td>
                            <td>Mission</td>
                            <td>Difficulty</td>
                            <td>Reward</td>
                            <td>Minutes</td>
                            <td>Combat</td>
                            <td>Location</td>
                            <td> </td>
                        </tr>
                        <?php
                        function checkLocation($location) {
                            if($location === $_SESSION['gamedata']['location']) {
                                return 'green-color';
                            } else {
                                return 'not-able-color';
                            }   
                        }
                        function checkWarriorLevel($difficulty) {
                            $level = array("easy" => 5, "medium" => 20, "hard" => 34);
                            if($level[$difficulty] > $_SESSION['gamedata']['warrior']['level']) {
                                return 'not-able-color';
                            } else {
                                return 'green-color';
                            }
                        }
                        foreach($this->data['armyMissions'] as $key):?>
                        <tr>
                            <td><?php echo $key['required_warriors'];?></td>
                            <td><?php echo $key['mission'];?></td>
                            <td class="<?php echo checkWarriorLevel($key['difficulty'])?>">
                                <?php echo ucfirst($key['difficulty']);?>
                            </td>
                            <td>
                                <?php echo $key['reward'];?>
                                <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>"/>
                            </td>
                            <td><?php echo $key['time'];?></td>
                            <td><?php echo $key['combat'];?></td>
                            <td class="<?php echo checkLocation($key['location']);?>">
                                <?php echo $key['location'];?>
                            </td>
                            <td><button onclick="prepareMission(this, <?php echo $key['mission_id']; ?>);"> Do mission</button></td>
                        </tr>
                        <?php endforeach;?>
                    </thead>
                </table>
            </div>