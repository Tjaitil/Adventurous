<?php
    function amounts($value) {
        switch(true) {
            case $value >= 1000:
                return round($value / 1000, 1) . 'k' ;
                break;
            default:
                return $value;
                break;
        }
    } ?>
    <p> Stockpile: </p>
    <?php
    foreach($data['stockpile'] as $key): ?>
        <div class="stockpile_item" onpointerdown="show_menu();">
            <figure>
                <img src="<?php echo constant('ROUTE_IMG') . trim(explode('(', $key['item'])[0]). '.png';?>"/>
                <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
            </figure>
            <span class="item_amount"><?php echo amounts($key['amount']);?></span>
        </div>
    <?php endforeach; ?>
    <p><?php echo count($data['stockpile']), " / 60"?></p>

