#|smithy.js|
<?php

use App\libs\TemplateFetcher;

/**
 * @var array $data
 * @property StoreItemResource[] $data['store_items']
 * @property int $data['discount']
 */
?>
<h1 class="page_title">Smithy</h1>
<div id="smith">
    <p class="help mb-1">
        Select a mineral below to smith from.
    </p>
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
        ['item_requirements' => true, 'item_information' => true]
    );
    ?>
</div>