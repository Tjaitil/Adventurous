            smithy.css|smithy.js|
            <h1 class="page_title">Smithy</h1>
            <div id="smith">
                <p class="help mb-1">
                    Select a mineral below to smith from. Players with miner profiency pay nothing
                </p>
                <h5>Choose your mineral</h5>
                <div class="mb-2">
                    <img src="<?php echo constant('ROUTE_IMG') . 'iron ore.png'; ?>" class="mineral-sort" title="iron">
                    <img src="<?php echo constant('ROUTE_IMG') . 'steel ore.png'; ?>" class="mineral-sort" title="steel">
                    <img src="<?php echo constant('ROUTE_IMG') . 'gargonite ore.png'; ?>" class="mineral-sort" title="gargonite">
                    <img src="<?php echo constant('ROUTE_IMG') . 'adron ore.png'; ?>" class="mineral-sort" title="adron">
                    <img src="<?php echo constant('ROUTE_IMG') . 'yeqdon ore.png'; ?>" class="mineral-sort" title="yedqon">
                    <img src="<?php echo constant('ROUTE_IMG') . 'frajrite ore.png'; ?>" class="mineral-sort" title="frajrite">
                </div>
                <button id="reset-mineral-sort">
                    Reset 
                </button>

                <?php 
                    get_template("storeContainer", false); 
                    createStoreContainer(array("container_items" => $this->data), 
                                        ['item_requirements' => true, 'item_information' => true]);
                ?>
            </div>