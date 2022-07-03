smithy.css|smithy.js|
<h1 class="page_title">Smithy</h1>
<div id="smith">
    <p class="help mb-1">
        Select a mineral below to smith from. Players with miner profiency pay nothing
    </p>
    <h5>Choose your mineral</h5>

    <?php 
        get_template("storeContainer", false, true); 
        createStoreContainer(array("container_items" => $this->data), 
                            ['item_requirements' => true, 'item_information' => true]);
    ?>
</div>