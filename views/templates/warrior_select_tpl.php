<div id="warriors-select-wrapper">
    <p class="mb-t">Available warriors - <span id="selected_warrior_amount">0</span> selected</p>
    <div id="warriors_container">
        <?php if(!count($data) > 0):?>
            <p> No warriors available!</p>
        <?php endif;?>
        <?php foreach($data as $key):?>
            <div class="warrior-select-card" id="warrior_<?php echo $key['warrior_id'];?>">
                <div class="warrior_front warrior-side">
                        <img src="<?php echo constant('ROUTE_IMG') . $key['type'];?> icon.png" />
                        <p> Warrior #<?php echo $key['warrior_id'];?></p>
                        <p> Attack: <?php echo ($key['attack'] == null) ? 10 : $key['attack'] + 10;?></p>
                        <p class="mb-1"> Defence: <?php echo ($key['defence'] == null) ? 12 : $key['defence'] + 12;?></p>
                        <div class="warrior-card-skill-container" class="mt-1">
                            <figure class="warrior-card-skill">
                                <img src="<?php echo constant('ROUTE_IMG') . 'stamina icon.png';?>" />
                                <span><?php echo $key['stamina_level'];?></span>
                            </figure>
                            <figure class="warrior-card-skill">
                                <img src="<?php echo constant('ROUTE_IMG') . 'technique icon.png';?>" />
                                <span><?php echo $key['technique_level'];?></span>
                            </figure>
                            <figure class="warrior-card-skill">
                                <img src="<?php echo constant('ROUTE_IMG') . 'strength icon.png';?>" />
                                <span><?php echo $key['strength_level'];?></span>
                            </figure>
                            <figure class="warrior-card-skill">
                                <img src="<?php echo constant('ROUTE_IMG') . 'precision icon.png';?>" />
                                <span><?php echo $key['precision_level'];?></span>
                            </figure>
                        </div>
                </div>
                <div class="warrior_back warrior-side">
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
                <input type="checkbox" />
                <button> >> </button>
            </div>
        <?php endforeach;?>
    </div>
    <p class="mt-1 mb-1"><span class="warrior_warning"> Remember to review armour and ammuntition!</span>
        Only warriors which have status "idle" are displayed. Check armycmap for status.
    </p>
</div>