<div id="container-item-wrapper" class="div_content div_content_dark">
    <div id="container-item-list" class=".pb-05">
        <?php foreach($data['container_items'] as $key): ?>
            <div class="container-item">
                <figure>
                    <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png'; ?>" />
                    <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
                </figure>
                <span class="item_amount"></span>
                <p>
                    <span>
                        <span class="container-item-price"><?php echo $key['price']?></span>
                        <span class="<?php echo (isset($key['discount']) && $key['discount'] >  0) ? "able-color" : ""?>">
                            <?php echo (isset($key['discount']) && $key['discount'] >  0) ?  "- " . $key['discount'] : ""?>
                        </span>
                    </span>
                </p>
                <img class="gold" src="<?php echo constant("ROUTE_IMG") . 'gold.png'; ?>" />
            </div>
        <?php endforeach; ?>
    </div>
    <div id="container-item-selected" class="div_content_dark">
        <div id="do_trade" class="div_content_dark">
            <div id="selected_trade">

            </div>
            <p></p>
            <p id="trade_price"><span></span>
                <img class="gold" src="<?php echo constant("ROUTE_IMG") . 'gold.png'; ?>" />
            </p>
            <?php if(is_array($options) && $options['site'] === 'bakery'): ?>
                <div id="container-item-requirements"></div>
            <?php endif;?>
            <label for="amount"> Amount </label></br>
            <input class="mb-1" type="number" id="amount" name="amount" min="0" /></br>
            <button class="mb-1" id="container-item-selected-event-button"> Trade </button>
        </div>
    </div>
</div>