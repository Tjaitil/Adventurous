<?php
    if(!function_exists('calculateHealth')) {
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
    }
    if(!function_exists('warriorStatus')) {
        function warriorStatus($warrior) {
            // Check the data for if the what the current status for the warrior is
            // If mission is 0 it is not active, it will be 1 if it is active
            if($warrior['mission'] !== '0') {
                return "On Mission " . $warrior['mission'];
            }
            else if($warrior['mission'] == 2) {
                return "On Adventure";
            }
            // If fetch_report is 1 training is active, depending on the training countdown it may be done
            if($warrior['fetch_report'] == 1) {
                return "Training";
            }
            // If rest is 1 the warrior is resting
            if($warrior['rest'] == 1) {
                return "Resting";
            }
            else {
                return "Idle";
            }
        }
    }
    $key = $data[0];
    ?>
    <div id="warrior_<?php echo $key['warrior_id'];?>" class="warrior">
        <div class="info">
            <div class="warrior-info-container">
                <img src="<?php echo constant('ROUTE_IMG') . $key['type'] . ' icon.png';?>" alt="<?php echo $key['warrior_id'];?>"/>
                <div>
                    <p><?php echo '#',$key['warrior_id'];?></p>
                    <p>Location: <?php echo ucwords($key['location']);?></p>
                </div>
            </div>
            <div class="div_content pb-05">
                <div class="warrior-info-container">
                    <p>Health
                    <p class="warrior-health">
                        <?php echo $key['health'];
                            echo ($key['rest'] == '1') ? " (" . calculateHealth($key['rest_start'], $key['health']) . ")" : "";?>
                    </p>
                </div>
                <div class="warrior-info-container">
                    <p>Status </p>
                    <div>
                        <?php $status = warriorStatus($key);?>
                        <p class="warrior-status <?php echo ($status === "Idle") ? 'green-color' : "";?>">
                            <?php echo $status; ?>
                        </p>
                        <p class="countdown"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="warrior_level_up mt-1 mb-1">
            
        </div>
        <div class="levels">
            <div class="warrior-skill-container">
                <figure>
                    <img class="warrior_skill" src="<?php echo constant('ROUTE_IMG') . 'stamina icon.png';?>" />
                    <figcaption><?php echo $key['stamina_level'];?></figcaption>
                </figure>
                <div class="progressBarContainer skill_bar">
                    <div class="progressBarOverlayShadow">
                    </div>
                    <div class="progressBarOverlay">
                    </div>
                    <div class="progressBar skill_bar_progress">
                        <span class="progressBar_currentValue"><?php echo $key['stamina_xp'];?></span>
                        &nbsp/&nbsp
                        <span class="progressBar_maxValue"><?php
                        // Get the next level xp by accessing array with the current skill level
                        echo $data[1][$key['stamina_level']];?></span>
                    </div>
                </div>   
            </div>
            <div class="warrior-skill-container">
                <figure>
                    <img class="warrior_skill" src="<?php echo constant('ROUTE_IMG') . 'technique icon.png';?>" />
                    <figcaption><?php echo $key['technique_level'];?></figcaption>
                </figure>
                <div class="progressBarContainer skill_bar">
                    <div class="progressBarOverlayShadow">
                    </div>
                    <div class="progressBarOverlay">
                
                    </div>
                    <div class="progressBar skill_bar_progress">
                        <span class="progressBar_currentValue"><?php echo $key['technique_xp'];?></span>
                        &nbsp/&nbsp
                        <span class="progressBar_maxValue"><?php echo $data[1][$key['technique_level']];?></span>
                    </div>
                </div>
            </div>
            <div class="warrior-skill-container">
                <figure>
                    <img class="warrior_skill" src="<?php echo constant('ROUTE_IMG') . 'precision icon.png';?>" />
                    <figcaption><?php echo $key['precision_level'];?></figcaption>
                </figure>
                <div class="progressBarContainer skill_bar">
                    <div class="progressBarOverlayShadow">
                    </div>
                    <div class="progressBarOverlay">
                
                    </div>
                    <div class="progressBar skill_bar_progress">
                        <span class="progressBar_currentValue"><?php echo $key['precision_xp'];?></span>
                        &nbsp/&nbsp
                        <span class="progressBar_maxValue"><?php echo $data[1][$key['precision_level']];?></span>
                    </div>
                </div>
            </div>
            <div class="warrior-skill-container">
                <figure>
                    <img class="warrior_skill" src="<?php echo constant('ROUTE_IMG') . 'strength icon.png';?>" />
                    <figcaption><?php echo $key['strength_level'];?></figcaption>
                </figure>
                <div class="progressBarContainer skill_bar">
                    <div class="progressBarOverlayShadow">
                    </div>
                    <div class="progressBarOverlay">
                
                    </div>
                    <div class="progressBar skill_bar_progress">
                        <span class="progressBar_currentValue"><?php echo $key['strength_xp'];?></span>
                        &nbsp/&nbsp
                        <span class="progressBar_maxValue"><?php echo $data[1][$key['strength_level']];?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>