<?php if(!count($data) > 0):?>
    <tr>
        <td colspan="5"> None </td>
    </tr>
<?php endif;
    foreach($data as $key): ?>
            <tr>
                
                <td><?php echo $key['type'];?><input type="hidden" value="<?php echo $key['id'];?>" /></td>
                <td><?php echo $key['item'];?></td>
                <td><?php echo $key['price_ea'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.jpg';?>" /></td>
                <td><?php echo $key['progress'] , '/' , $key['amount'];?> </td>
                <td><button onclick="cancelOffer(<?php echo $key['id'];?>, this);"> Cancel offer</button></td>
                <td><?php if($key['box_amount'] > 0): ?>
                     <div class="inventory_item">
                        <figure>
                            <img src="<?php echo constant('ROUTE_IMG') . $key['box_item'] . '.png';?>" />
                            <figcaption class="tooltip"><?php echo ucwords($key['box_item']); ?></figcaption>
                        </figure>
                        <span class="item_amount"><? echo $key['box_amount'];?></span>
                    </div>
                    <?php else:?>
                    <div>
                        
                    </div>
                    <?php endif;?>
                </td>
            </tr>
<?php endforeach;?>