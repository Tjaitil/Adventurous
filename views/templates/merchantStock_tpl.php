    <?php if(!count($data['shop']) > 0): ?>
        <div>
            <span>No trades available!</span>
        </div>                    
    <?php endif; ?>
    <?php foreach($data['shop'] as $key): ?>   
            <div class="store_trade">
                <p><?php echo $key['amount'];?></p>
                <div class="item">
                    <figure onclick="show_title(this);">
                        <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>"/>
                        <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
                    </figure>
                    <span class="item_amount">x1</span>
                </div>
                <div id="trade_sign"> <=> </br>
                </div>
                <div class="item">
                    <figure onclick="show_title(this);">
                        <img src="<?php echo constant('ROUTE_IMG') . $key['want'] . '.png';?>"/>
                        <figcaption class="tooltip"><?php echo ucwords($key['want']); ?></figcaption>
                    </figure>
                    <span class="item_amount"><?php echo 'x',$key['want_amount'];?></span>
                </div>
            </div>
    <?php endforeach;?>