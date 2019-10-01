    <?php if(!count($data['shop']) > 0): ?>
        <div>
            <span>None trades available!</span>
        </div>                    
    <?php endif; ?>
    <?php foreach($data['shop'] as $key): ?>   
            <div class="store_trade">
                <p><?php echo $key['amount'];?></p>
                <div class="inventory_item">
                    <figure onclick="show_title(this);">
                        <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>"/>
                        <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
                    </figure>
                    <span>x1</span>
                </div>
                <div id="trade_sign"> <=> </br>
                </div>
                <div class="inventory_item">
                    <figure onclick="show_title(this);">
                        <img src="<?php echo constant('ROUTE_IMG') . $key['want'] . '.png';?>"/>
                        <figcaption class="tooltip"><?php echo ucwords($key['want']); ?></figcaption>
                    </figure>
                    <span><?php echo 'x',$key['want_amount'];?></span>
                </div>
            </div>
    <?php endforeach;?>