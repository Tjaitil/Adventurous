<?php if(!count($data) > 0):?>
    <p> No warriors available!</p>
<?php endif;?>
<?php foreach($data as $key):?>
    <div class="warriors" id="warrior_<?php echo $key['warrior_id'];?>">
        <figure>
            <img src="<?php echo constant('ROUTE_IMG') . $key['type'];?>.png" />
            <p> Attack: <?php echo ($key['attack'] == null) ? 10 : $key['attack'] + 10;?></p>
            <p> Defence: <?php echo ($key['defence'] == null) ? 12 : $key['defence'] + 12;?></p>
            <figcaption>
                <span></span>
            </figcaption>
            <figure>
                <img src="<?php echo constant('ROUTE_IMG') . 'stamina_icon.png';?>" />
                <span class="skill_level"><?php echo $key['stamina_level'];?></span>
            </figure>
            <figure>
                <img src="<?php echo constant('ROUTE_IMG') . 'technique_icon.png';?>" />
                <span class="skill_level"><?php echo $key['technique_level'];?></span>
            </figure>
            <figure>
                <img src="<?php echo constant('ROUTE_IMG') . 'strength_icon.png';?>" />
                <span class="skill_level"><?php echo $key['strength_level'];?></span>
            </figure>
            <figure>
                <img src="<?php echo constant('ROUTE_IMG') . 'precision.png';?>" />
                <span class="skill_level"><?php echo $key['precision_level'];?></span>
            </figure>
        </figure>
        <input type="checkbox" />
    </div>
<?php endforeach;?>