<?php if(!count($data) > 0):?>
    <tr>
        <td colspan="5"> None </td>
    </tr>
<?php endif;
    foreach($data as $key): ?>
            <tr>
                <td><?php echo $key['type'];?></td>
                <td><?php echo $key['item'];?></td>
                <td><?php echo $key['price_ea'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.jpg';?>" /></td>
                <td><?php echo $key['progress'] , '/' , $key['amount'];?> </td>
                <td><button onclick="cancelOffer(<?php echo $key['id'];?>, this);"> Cancel offer</button></td>
            </tr>
<?php endforeach;?>