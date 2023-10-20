<?php

/**
 * @var array $data warriors[]
 * @property int warrior[].$warrior_id
 * @property string warrior[].$type
 * @property string warrior[].$helm
 * @property string warrior[].$ammunition
 * @property string warrior[].$ammunition_amount
 * @property string warrior[].$left_hand
 * @property string warrior[].$body
 * @property string warrior[].$right_hand
 * @property string warrior[].$legs
 * @property string warrior[].$boots
 * @property int warrior[].$attack
 * @property int warrior[].$defence
 * @property int warrior[].$attack_speed
 */

foreach ($data as $key) : ?>
     <div class="armory_view darkTextColor">
          <div class="armory_view_info">
               <img class="type_icon" src="<?php echo constant('ROUTE_IMG') . $key['type'] . ' icon.png'; ?>" />
               <div class="armory_view_info_text_wrapper">
                    <p> Warrior #<span class="armory_view_warrior_id"><?php echo $key['warrior_id']; ?></span></p>
                    <p> Attack: <?php echo $key['attack'] + 10; ?></p>
                    <p> Defence: <?php echo $key['defence'] + 15; ?></p>
               </div>
          </div>
          <div class="armory_view_part_grid">
               <img class="armory_view_part helm" title="<?php echo $key['helm']; ?>" class="helm" src="<?php echo constant("ROUTE_IMG") . $key['helm'] . '.png' ?>" />
               <div class="armory_view_div">
                    <span class="ammunition_amount"><?php echo ($key['ammunition_amount'] > 0) ? $key['ammunition_amount'] : ""; ?></span>
                    <img class="armory_view_part ammunition" title="<?php echo $key['ammunition']; ?>" src="<?php echo constant("ROUTE_IMG") . $key['ammunition'] . '.png' ?>">
               </div>
               <img class="armory_view_part right_hand" title="<?php echo $key['right_hand']; ?>" src="<?php echo constant("ROUTE_IMG") . $key['right_hand'] . '.png' ?>" />
               <img class="armory_view_part body" title="<?php echo $key['body']; ?>" src="<?php echo constant("ROUTE_IMG") . $key['body'] . '.png' ?>" />
               <img class="armory_view_part left_hand" title="<?php echo $key['left_hand']; ?>" src="<?php echo constant("ROUTE_IMG") . $key['left_hand'] . '.png' ?>" />
               <img class="armory_view_part legs" title="<?php echo $key['legs']; ?>" src="<?php echo constant("ROUTE_IMG") . $key['legs'] . '.png' ?>" />
               <img class="armory_view_part boots" title="<?php echo $key['boots']; ?>" src="<?php echo constant("ROUTE_IMG") . $key['boots'] . '.png' ?>" />
          </div>
     </div>
<?php endforeach; ?>