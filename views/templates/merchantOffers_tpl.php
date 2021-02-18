<?php
    foreach($data as $key): ?>   
        <div class="item">
            <figure>
                <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>"/>
                <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
            </figure>
            <span class="item_amount"><?php echo $key['amount'];?></span>
            <p class="item_price"><?php echo $key['price'] . ' ( +25 )' . ' / ' . '</br>' . floor($key['price'] * 0.97) ;?>
                <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></p>
        </div>
<?php endforeach;?>