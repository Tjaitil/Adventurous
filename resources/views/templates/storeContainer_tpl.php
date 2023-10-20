<?php

/**
 * @param StoreItemResource[] $data 
 * @param array 
 * $options = [
 *  'item_requirements' => (boolean) Add requirements container. Optional.
 *  'item_information  => (boolean) Add information contiainer about item. Optional.
 *  'item_amount' => (boolean) Add amount container. Optional.
 * ]
 * @return html
 */
function createStoreContainer($data, $options = [])
{  ?>
    <div id="store-container-item-wrapper" class="div_content div_content_dark">
        <div id="store-container-item-list" class="pb-05">
            <?php foreach ($data['container_items'] as $key) : ?>
                <div class="store-container-item">
                    <figure>
                        <img src="<?php echo constant('ROUTE_IMG') . $key['name'] . '.png'; ?>" />
                        <figcaption class="tooltip"><?php echo ucwords($key['name']); ?></figcaption>
                    </figure>
                    <?php if (isset($options['item_amount']) && $options['item_amount'] === true) : ?>
                        <span class="item_amount"></span>
                    <?php endif; ?>
                    <p>
                        <span>
                            <span class="store-container-item-price 
                                <?php echo $key['adjusted_store_value'] > 0 ? 'line-through' : ''; ?>
                            ">
                                <?php
                                echo $key['store_value']; ?>
                            </span>
                            <?php if ($key['adjusted_store_value'] > 0) : ?>
                                <span class="able-color">
                                    <?php echo $key['adjusted_store_value']; ?>
                                </span>
                            <?php endif; ?>
                            <span class="
                                    <?php /*echo ($key['adjusted_store_value'] >  0) ? "able-color" : "not-able-color" ?>
                                ">
                                <?php echo (isset($key['adjusted_store_value']) && $key['adjusted_store_value'] >  0) ?
                                    "- " . $key['adjusted_store_value']
                                    : ""*/ ?>">
                            </span>
                    </p>
                    <img class=" gold" src="<?php echo constant("ROUTE_IMG") . 'gold.png'; ?>" />
                </div>
            <?php endforeach; ?>
        </div>
        <div id="store-container-item-selected" class="div_content_dark">
            <div id="store-container-do-trade" class="div_content_dark">
                <div id="store-container-selected-trade" class="item mt-1">

                </div>
                <p id="store-contaniner-trade-price"><span></span>
                    <img class="gold" src="<?php echo constant("ROUTE_IMG") . 'gold.png'; ?>" />
                </p>
                <div class="mt-2 dark-bg py-1">
                    <span class="mb-1">Required</span>
                    <?php if (isset($options['item_requirements']) && $options['item_requirements'] === true) : ?>
                        <div id="store-container-item-requirements" class="d-flex"></div>
                    <?php endif; ?>
                    <div class="skill-requirements"></div>
                </div>

                <?php if (isset($options['item_information']) && $options['item_information'] === true) : ?>
                    <p id="store-container-item-information"></p>
                <?php endif; ?>
                <label for="amount">Select your Amount</label></br>
                <input class="mb-1" type="number" id="store-container-selected-trade-amount" name="amount" min="1" />
                </br>
                <button class="mb-1" id="store-container-item-event-button"> Trade </button>
            </div>
            <div id="store-container-no-trade-selected">
                <p> Select an item to trade </p>
            </div>
        </div>
    </div>
<?php } ?>