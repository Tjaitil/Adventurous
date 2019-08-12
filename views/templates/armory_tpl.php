<?php
foreach($data['warrior_armory'] as $key):
if(!count($data) < 0): ?>
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
<?php break; endif;?>
    <div class="armory_view">
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
    </div>
<?php endforeach;?>