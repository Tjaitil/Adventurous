<?php
    function calculateHealth($rest_start, $health) {
        $rest_start = date_timestamp_get(new DateTime($rest_start));
        $date_now = date_timestamp_get(new DateTime(date("Y-m-d H:i:s")));
        $health_gained = (($date_now - $rest_start) / 60) * 3;
        if($health_gained + $health > 100) {
            return $health = 100;
        }
        else {
            return $health + $health_gained;
        }
    }

    foreach($data[0] as $key): ?>
        <div id="warrior_<?php echo $key['warrior_id'];?>" class="warrior">
            <div class="info">
                <img src="#" alt="<?php echo $key['warrior_id'];?>"/>
                <table>
                    <tr>
                        <td> Health: </td>
                        <td><?php echo $key['health'];
                        echo ($key['rest'] == '1') ? " (" . calculateHealth($key['rest_start'], $key['health']) . ")" : "";?></td>
                    </tr>
                    <tr>
                        <td> Status: </td>
                        <td><?php echo $key['status'];?></td>
                    </tr>
                </table>
            </div>
            <div class="levels">
                <p class="countdown"></p>
                <ul>
                    <li>
                        <figure>
                            <img class="warrior_skill" src="<?php echo constant('ROUTE_IMG') . 'stamina icon.png';?>" />
                            <figcaption><?php echo $key['technique_level'];?></figcaption>
                        </figure>
                        <div class="skill_bar">
                            <div class="skill_bar2">
                        
                            </div>
                            <div class="skill_bar_progress">
                                <span class="progress_value1"><?php echo $key['stamina_xp'];?></span>
                                &nbsp/&nbsp
                                <span class="progress_value2"><?php echo $data[1][$key['stamina_level']];?></span>
                            </div>
                        </div>
                    </li>
                    <li>
                        <figure>
                            <img class="warrior_skill" src="<?php echo constant('ROUTE_IMG') . 'technique icon.png';?>" />
                            <figcaption><?php echo $key['technique_level'];?></figcaption>
                        </figure>
                        <div class="skill_bar">
                            <div class="skill_bar2">
                        
                            </div>
                            <div class="skill_bar_progress">
                                <span class="progress_value1"><?php echo $key['technique_xp'];?></span>
                                &nbsp/&nbsp
                                <span class="progress_value2"><?php echo $data[1][$key['technique_level']];?></span>
                            </div>
                        </div>
                    </li>
                    <li><figure>
                            <img class="warrior_skill" src="<?php echo constant('ROUTE_IMG') . 'precision icon.png';?>" />
                            <figcaption><?php echo $key['precision_level'];?></figcaption>
                        </figure>
                        <div class="skill_bar">
                            <div class="skill_bar2">
                        
                            </div>
                            <div class="skill_bar_progress">
                                <span class="progress_value1"><?php echo $key['precision_xp'];?></span>
                                &nbsp/&nbsp
                                <span class="progress_value2"><?php echo $data[1][$key['precision_level']];?></span>
                            </div>
                        </div>
                    </li>
                    <li>
                        <figure>
                            <img class="warrior_skill" src="<?php echo constant('ROUTE_IMG') . 'strength icon.png';?>" />
                            <figcaption><?php echo $key['strength_level'];?></figcaption>
                        </figure>
                        <div class="skill_bar">
                            <div class="skill_bar2">
                        
                            </div>
                            <div class="skill_bar_progress">
                                <span class="progress_value1"><?php echo $key['strength_xp'];?></span>
                                &nbsp/&nbsp
                                <span class="progress_value2"><?php echo $data[1][$key['strength_level']];?></span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    <?php endforeach;?>