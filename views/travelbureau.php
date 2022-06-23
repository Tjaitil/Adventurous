            travelbureau.css|travelbureau.js|
            <?php 
                // Get required templates
                get_template('createItem', null, true); 
            ?>
            <h1 class="page_title"><?php echo $title; ?></h1>
            <div id="cart_shop">
                <p> Your current cart: <?php echo $this->data['cart'];?></p>
                <div id="cart-container">
                    <?php foreach ($this->data['cart_shop'] as $key) : ?>
                        <div class="cart-container-item div_content div_content_dark p1">
                            <div class="dark-image-container">
                                <img src="<?php echo constant('ROUTE_IMG') . $key['wheel'] . ' cart.png'; ?>" />
                            </div>
                            <div>
                                <p class="cart-container-item-type"><?php echo ucfirst($key['type']);?></p>
                                <p><?php echo $key['capasity']; ?> capasity</p>
                                <h4>Required</h4>
                                <div class="d-flex justify-center">
                                <?php 
                                    $data = array(
                                        ["item" => $key['wheel'] . ' bar', "amount" => $key['mineral_amount']],
                                        ["item" => $key['wood'] . ' logs', "amount" => $key['wood_amount']]
                                    );
                                    createItem($data);
                                    ?>
                                </div>
                                <p><?php echo ucfirst($key['price']);?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png'; ?>" /></p>
                                <button class="travel_burea_buy_event">Make</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>