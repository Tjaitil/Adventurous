#|archeryshop.js|
<?php

use App\libs\TemplateFetcher;

/**
 * @var array $data
 * @property StoreItemResource[] $data['store_items']
 * @property int $data['discount']
 */


?>
<h1 class="page_title">Archery Shop</h1>
<div id="fletch">
    <p class="help">
        Craft bows, unfinished arrows or arrow shafts from logs. Some bows will require a certain total
        level of warriors. check <a href="gameguide/warrior">gameguide</a>
    </p>
    <h5>Choose your log</h5>

    <p>
        <?php
        echo TemplateFetcher::loadTemplate('discount', [
            'discount' => $data['discount'],
            'discount_text' => 'Miner discount ' . $data['discount'] . '% active'
        ]); ?>
    </p>
    <?php
    get_template("storeContainer", false, true);

    createStoreContainer(
        array("container_items" => $data['store_items']),
        ["item_requirements" => true]
    );
    ?>
</div>