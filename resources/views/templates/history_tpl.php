<?php if(!count($data) > 0):?>
    <tr>
        <td colspan="5"> None </td>
    </tr>
<?php endif;?>
<?php
    krsort($data);
    foreach($data as $key): ?>
            <tr>
                <td><?php echo $key['type'];?></td>
                <td><?php echo ucwords($key['item']);?>
                    <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>"/></td>
                <td><?php echo $key['amount'];?></td>
                <td><?php echo $key['price_ea'];?>
                    <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>"/></td>
            </tr>
    <?php endforeach;?>