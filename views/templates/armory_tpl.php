<?php
foreach($data as $key):
    if(!isset($key['check'])):?>
        <div class="armory_view">
    <?php endif;?>
        <p><?php echo $key['warrior_id'];?></p>
        <img onclick="removeArmor(this);"
             title="<?php echo $key['helm'];?>"
             class="helm" src="<?php echo constant("ROUTE_IMG") . str_replace(" ", "_", $key['helm']) . '.jpg'?>"/>
        <img onclick="removeArmor(this);"
             title="<?php echo $key['left_hand'];?>"
             class="left_hand" src="<?php echo constant("ROUTE_IMG") . str_replace(" ", "_", $key['left_hand']) . '.jpg'?>" />
        <img onclick="removeArmor(this);"
             title="<?php echo $key['body'];?>"
             class="body" src="<?php echo constant("ROUTE_IMG") . str_replace(" ", "_", $key['body']) . '.jpg'?>" />
        <img onclick="removeArmor(this);"
             title="<?php echo $key['right_hand'];?>"
             class="right_hand" src="<?php echo constant("ROUTE_IMG") . str_replace(" ", "_", $key['right_hand']) . '.jpg'?>" />
        <img onclick="removeArmor(this);"
             title="<?php echo $key['legs'];?>"
             class="legs" src="<?php echo constant("ROUTE_IMG") . str_replace(" ", "_", $key['legs']) . '.jpg'?>" />
        <img onclick="removeArmor(this);"
             title="<?php echo $key['boots'];?>"
             class="boots" src="<?php echo constant("ROUTE_IMG") . str_replace(" ", "_", $key['boots']) . '.jpg'?>" />
        <p> Attack: <?php echo $key['attack'];?></p>
        <p> Defence: <?php echo $key['defence'];?></p>
    <?php if(!isset($key['check'])):?>
        </div>
    <?php endif;?>
<?php endforeach;?>