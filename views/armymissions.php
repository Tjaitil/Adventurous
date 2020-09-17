            armymissions.css|armymissions.js|
            <h3 class="page_title"> Army Missions </h3>
            <div id="current_mission">
                <p> Mission:</p><p id="time"></p>
                <button> Cancel mission </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <td> Location </td>
                        <td> Warriors required </td>
                        <td> Mission </td>
                        <td> Reward </td>
                        <td> Minutes </td>
                        <td> </td>
                    </tr>
                    <?php foreach($this->data['armyMissions'] as $key):?>
                    <tr>
                        <td><?php echo ucfirst($key['location']);?></td>
                        <td><?php echo $key['required_warriors'];?></td>
                        <td><?php echo $key['mission'];?></td>
                        <td>
                            <?php echo $key['reward'];?>
                            <img width="15" height="15" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>"/>
                        </td>
                        <td><?php echo $key['time'];?></td>
                        <td><button onclick="prepareMission(this, <?php echo $key['mission_id']; ?>);"> Do mission</button></td>
                    </tr>
                    <?php endforeach;?>
                </thead>
            </table>
            <div id="mission_enabled">
                <button onclick="exit();">Exit</button>
                <table id="mission_table">
                                            
                </table>
                <p>Select Warriors:</p>
                <button id="mission_button" onclick="doMission();"> Do Mission </button>
            </div>