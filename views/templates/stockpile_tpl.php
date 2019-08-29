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
    }
    foreach($data['stockpile'] as $key): ?>
        <div class="stockpile_item">
            <figure onclick="show_title(this, true);">
                <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>"/>
                <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
            </figure>
            <span id="item_amount"><?php echo amounts($key['amount']);?></span>
        </div>
    <?php endforeach; ?>
    <p><?php echo count($data['stockpile']), " / 60"?></p>

