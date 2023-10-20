<?php foreach($data as $key): ?>
    <div class="warriors" id="warrior_<?php echo $key['warrior_id'];?>">
        <figure>
            <img src="<?php echo constant('ROUTE_IMG') . $key['type'] . '.jpg'?>" />
            <figcaption><?php
            echo "Warrior: " , $key['warrior_id'] , "</br>";
            echo "Type: " , $key['type'], "</br>";
            $string = '%s level: %d </br>';
            echo sprintf($string, 'Stamina', $key['stamina_level']);
            echo sprintf($string, 'Technique', $key['technique_level']);
            echo sprintf($string, 'Precision', $key['precision_level']);
            echo sprintf($string, 'Strength', $key['strength_level']);
            ?></figcaption>
        </figure>
        <input type="checkbox" onclick="check(this);" />
    </div>
<?php endforeach;?>