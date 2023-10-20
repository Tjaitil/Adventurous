<div id="workers">
    <div id="tavern-workers-grid-container">
        <?php if (!count($data) > 0) : ?>
            <p> No workers available </p>
        <?php endif; ?>
        <?php foreach ($data as $key) : ?>
            <div class="tavern-worker">
                <figure>
                    <img src="<?php echo (in_array($key['type'], array("ranged", "melee"))) ?
                                    constant("ROUTE_IMG") . $key['type'] . ' icon.png' :
                                    constant("ROUTE_IMG") . 'worker icon.png' ?>">
                    <figcaption>
                        <p class="mt-0 mb-0">
                            <span class="tavern-worker-type">
                                <?php echo ucfirst($key['type']);?>
                            </span>
                            <?php if($key['level'] > 0): ?>
                            level
                            <span class="tavern-worker-level">
                                <?php echo $key['level'];?>
                            </span>
                            <?php endif;?>
                        </p>
                        <p class="mt-0">
                            <img src="<?php echo constant("ROUTE_IMG") . 'gold.png'; ?>" class="gold">350
                        </p>
                    </figcaption>
                </figure>
                <button class="tavern-worker-recrute"> Recrute </button>
            </div>
        <?php endforeach; ?>
    </div>
</div>