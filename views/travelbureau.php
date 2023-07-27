            travelbureau.css|travelbureau.js|
            <h1 class="page_title"><?php echo $title; ?></h1>
            <div id="cart_shop">
                <div class="mb-2">
                    <p class="mb-0">Your current cart</p>
                    <?php
                    echo $this->bladeRender->run('components.item', ['name' => $current_cart->name, 'show_tooltip' => false, 'id' => 'current-cart', 'show_amount' => false]);
                    ?>
                </div>
                <?php
                echo $this->bladeRender->run('components.storeContainer', [
                    'store_items' => $store_items,
                    'options' => [
                        'item_requirements' => true,
                        'item_information' => true,
                        'input_amount' => false,
                    ]
                ]);
                ?>
            </div>