<?php if(!count($data) > 0):?>
    <tr>
        <td colspan="5"> None </td>
    </tr>
<?php endif;
    foreach($data as $key): ?>
        <tr>
            <td><div class="item" onclick="show_title(this, false);">
                <figure>
                <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>" />
                <figcaption class="tooltip"><?php echo ucwords($key['item']);?></figcaption>
            </figure>
            </div></td>
            <td><?php echo $key['amount_left'];?></td>
            <td><?php echo $key['price_ea'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
            <td><?php echo ucfirst($key['offeror']);?></td>
            <td><span>Amount</span>
            <input type="number" name="amount" id="amount" min="0"/>
            <input type="hidden" value="<?php echo $key['id'];?>" />
            <button>
            <?php echo ($key['type'] == "Buy")? "Sell" : "Buy";?></button></td>
        </tr>
<?php endforeach;?>