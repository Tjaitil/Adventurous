<?php if(!count($data) > 0):?>
    <tr>
        <td colspan="5"> None </td>
    </tr>
<?php endif;
    foreach($data as $key): ?>
        <tr>
            <td><?php echo $key['item'];?></td>
            <td><?php echo $key['amount_left'];?></td>
            <td><?php echo $key['price_ea'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.jpg';?>" /></td>
            <td><?php echo $key['offeror'];?></td>
            <td><span>Amount</span>
            <input type="number" name="amount" id="amount" min="0"/>
            <button onclick="trade(<?php echo $key['id'];?>, this)">
            <?php echo ($key['type'] == "Buy")? "Sell" : "Buy";?></button></td>
        </tr>
<?php endforeach;?>