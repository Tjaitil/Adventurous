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
            <div class="stockpile_buttons">
                <button onclick="withdraw(this, 1);"> 1 </button>
                <button onclick="withdraw(this, 5);"> 5 </button>
                <button onclick="withdraw(this, 'x');"> x </button>
                <button onclick="withdraw(this, 'all');"> All </button>
            </div>
            <figure onclick="show_title(this, true);">
                <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.jpg';?>"/>
                <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
            </figure>
            <span id="item_amount"><?php echo amounts($key['amount']);?></span>
        </div>
    <?php endforeach; ?>
    <p><?php echo count($data['stockpile']), " / 60"?></p>

