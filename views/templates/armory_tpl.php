<?php
foreach($data as $key):
    if(!isset($key['check'])):?>
        <div class="armory_view">
    <?php endif;?>
        <img class="type_icon" src="<?php echo constant('ROUTE_IMG') . $key['type'] . ' icon.png';?>" />
        <p> Warrior #<?php echo $key['warrior_id'];?></p>
        <p> Attack: <?php echo $key['attack'] + 10;?></p>
        <p> Defence: <?php echo $key['defence'] + 15;?></p>
        <img onclick="removeArmor(this);"
             title="<?php echo $key['helm'];?>"
             class="helm" src="<?php echo constant("ROUTE_IMG") . $key['helm'] . '.png'?>"/>
        <img onclick="removeArmor(this);"
             title="<?php echo $key['ammunition'];?>"
             class="ammunition" src="<?php echo constant("ROUTE_IMG") . $key['ammunition'] . '.png'?>"/>
             <div class="inventory_item" id="armory_view_div">
                <figure>
                    
                </figure>
                <span id="armory_view_span" class="item_amount">
                <?php echo ($key['ammunition_amount'] > 0) ? $key['ammunition_amount'] : "";?></span>
             </div>
        <img onclick="removeArmor(this);"        
             title="<?php echo $key['right_hand'];?>"
             class="right_hand" src="<?php echo constant("ROUTE_IMG") . $key['right_hand'] . '.png'?>" />
        <img onclick="removeArmor(this);"
             title="<?php echo $key['body'];?>"
             class="body" src="<?php echo constant("ROUTE_IMG") . $key['body'] . '.png'?>" />
        <img onclick="removeArmor(this);"
             title="<?php echo $key['left_hand'];?>"
             class="left_hand" src="<?php echo constant("ROUTE_IMG") . $key['left_hand'] . '.png'?>" />
        <img onclick="removeArmor(this);"
             title="<?php echo $key['legs'];?>"
             class="legs" src="<?php echo constant("ROUTE_IMG") . $key['legs'] . '.png'?>" />
        <img onclick="removeArmor(this);"
             title="<?php echo $key['boots'];?>"
             class="boots" src="<?php echo constant("ROUTE_IMG") . $key['boots'] . '.png'?>" />
    <?php if(!isset($key['check'])):?>
        </div>
    <?php endif;?>
<?php endforeach;?>