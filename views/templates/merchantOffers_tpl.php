<?php
    if(count($data) === 0): ?>
    <p>
        No offers available
    </p>
<?php endif;
    foreach($data as $key): ?>   
        <div class="item">
            <figure>
                <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>"/>
                <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
            </figure>
            <span class="item_amount"><?php echo $key['amount'];?></span>
            <p class="item_price"><?php echo $key['user_buy_price'] . ' ( +25 )' . ' / ' . '</br>'?>
            <span class="item_sell_price"><?php echo floor($key['user_sell_price'] * 0.97) ;?></span>
                <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></p>
        </div>
<?php endforeach;?>