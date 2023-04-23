#|archeryshop.js|
<?php
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
    <p>
        <?php if ($data['discount'] > 0) : ?>
            <span class="text-success">Miner discount <?php echo $data['discount']; ?>% active</span>
        <?php endif; ?>
    </p>
    <?php

    get_template("storeContainer", false, true);

    createStoreContainer(
        array("container_items" => $data['store_items']),
        ["item_requirements" => true]
    );
    ?>
</div>