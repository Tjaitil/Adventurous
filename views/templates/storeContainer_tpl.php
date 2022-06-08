<div id="store-container-item-wrapper" class="div_content div_content_dark">
    <div id="store-container-item-list" class=".pb-05">
        <?php foreach($data['container_items'] as $key): ?>
            <div class="store-container-item">
                <figure>
                    <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png'; ?>" />
                    <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
                </figure>
                <span class="item_amount"></span>
                <p>
                    <span>
                        <span class="store-container-item-price"><?php echo $key['price']?></span>
                        <span class="<?php echo (isset($key['discount']) && $key['discount'] >  0) ? "able-color" : ""?>">
                            <?php echo (isset($key['discount']) && $key['discount'] >  0) ?  "- " . $key['discount'] : ""?>
                        </span>
                    </span>
                </p>
                <img class="gold" src="<?php echo constant("ROUTE_IMG") . 'gold.png'; ?>" />
            </div>
        <?php endforeach; ?>
    </div>
    <div id="store-container-item-selected" class="div_content_dark">
        <div id="store-container-do-trade" class="div_content_dark">
            <div id="store-container-selected-trade">

            </div>
            <p></p>
            <p id="store-contaniner-trade-price"><span></span>
                <img class="gold" src="<?php echo constant("ROUTE_IMG") . 'gold.png'; ?>" />
            </p>
            <?php if(is_array($options) && $options['site'] === 'bakery'): ?>
                <div id="store-container-item-requirements"></div>
            <?php endif;?>
            <label for="amount"> Amount </label></br>
            <input class="mb-1" type="number" id="store-container-selected-trade-amount" name="amount" min="1" /></br>
            <button class="mb-1" id="store-container-item-event-button"> Trade </button>
        </div>
    </div>
</div>