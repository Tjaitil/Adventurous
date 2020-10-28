<div id="warriors_container">
    <?php if(!count($data) > 0):?>
        <p> No warriors available!</p>
    <?php endif;?>
    <?php foreach($data as $key):?>
        <div class="warriors" id="warrior_<?php echo $key['warrior_id'];?>">
            <div class="warrior_front">
                <figure>
                    <img src="<?php echo constant('ROUTE_IMG') . $key['type'];?> icon.png" />
                    <p> Warrior #<?php echo $key['warrior_id'];?></p>
                    <p> Attack: <?php echo ($key['attack'] == null) ? 10 : $key['attack'] + 10;?></p>
                    <p> Defence: <?php echo ($key['defence'] == null) ? 12 : $key['defence'] + 12;?></p>
                    <figure>
                        <img src="<?php echo constant('ROUTE_IMG') . 'stamina icon.png';?>" />
                        <span class="skill_level"><?php echo $key['stamina_level'];?></span>
                    </figure>
                    <figure>
                        <img src="<?php echo constant('ROUTE_IMG') . 'technique icon.png';?>" />
                        <span class="skill_level"><?php echo $key['technique_level'];?></span>
                    </figure>
                    <figure>
                        <img src="<?php echo constant('ROUTE_IMG') . 'strength icon.png';?>" />
                        <span class="skill_level"><?php echo $key['strength_level'];?></span>
                    </figure>
                    <figure>
                        <img src="<?php echo constant('ROUTE_IMG') . 'precision icon.png';?>" />
                        <span class="skill_level"><?php echo $key['precision_level'];?></span>
                    </figure>
                </figure>
                </br>
                <input type="checkbox" />
                <button> >> </button>
            </div>
            <div class="warrior_back">
                <ul>
                    <li>Equipment:</li>
                    <?php
                    $armory = array($key['helm'], $key['ammunition'], $key['body'], $key['right_hand'],
                                                  $key['left_hand'], $key['legs'], $key['boots']);
                    if(count(array_unique($armory)) > 1):
                    for($i = 0; $i < count($armory); $i++):?>
                        <?php if($i == 1 && $key['ammunition'] != 'none'): ?>
                            <li><?php echo ucwords($armory[$i]) . ' x ' . $key['ammunition_amount'];?></li>
                        <?php elseif($armory[$i] != 'none'): ?>
                            <li><?php echo ucwords($armory[$i]);?></li>
                        <?php endif; endfor;
                    else: ?>
                        <li> None </li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    <?php endforeach;?>
</div>