<div>
    <?php foreach($data as $key):?>
        <caption>
            <figure>
                <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $key['skill'] . ' icon.png';?>" />
                <figcaption><h3> You have leveled up <?php echo $key['skill'];?> to level <?php echo $key['level'];?>!</h3></figcaption>
            </figure>
        </caption>
        <table>
            <thead>
                <tr>
                    <td>Unlocked</td>
                </tr>
            </thead>
            <?php if(!count($key['content']) > 0): ?>
                    <tr>
                        <td> Nothing new at this level </td>
                    </tr>
            <?php endif; ?>
            <?php foreach($key['content'] as $key): ?>
            <tr>
                <td>
                    <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $key['image'] . '.png';?>" />
                    <?php echo $key['unlocked'];?>
                </td>
            </tr>
            <?php endforeach;?>
        </table>
    <?php endforeach;?>
</div>