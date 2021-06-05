            armymissions.css|armymissions.js|
            <h3 class="page_title"> Army Missions </h3>
            <div id="missions" class="cont_close">
                <button> Back to army camp</button>
                <div id="current_mission">
                    <p> Mission:</p><p id="time"></p>
                    <button> Cancel mission </button>
                </div>
                <div id="mission_enabled">
                <img class="cont_exit" src="<?php echo constant("ROUTE_IMG") . 'exit.png';?>"  width="20px" height="20px" 
                    onclick="exit();" />
                    <table id="mission_table">
                                                
                    </table>
                    <p>Selected warriors: <span id="selected_warrior_amount">0</span></p>
                    <p>Select Warriors:</p>
                    <button id="mission_button" onclick="doMission();"> Do Mission </button>
                </div>
                <table>
                    <thead>
                            <td> Warriors required </td>
                            <td> Mission </td>
                            <td> Difficulty </td>
                            <td> Reward </td>
                            <td> Minutes </td>
                            <td> </td>
                        </tr>
                        <?php foreach($this->data['armyMissions'] as $key):?>
                        <tr>
                            <td><?php echo $key['required_warriors'];?></td>
                            <td><?php echo $key['mission'];?></td>
                            <td><?php echo ucfirst($key['difficulty']);?></td>
                            <td>
                                <?php echo $key['reward'];?>
                                <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>"/>
                            </td>
                            <td><?php echo $key['time'];?></td>
                            <td><button onclick="prepareMission(this, <?php echo $key['mission_id']; ?>);"> Do mission</button></td>
                        </tr>
                        <?php endforeach;?>
                    </thead>
                </table>
            </div>