           <div id="workers">
                <div id="tavern-workers-grid-container">
                    <?php if(!count($data) > 0): ?>
                        <p> No workers available </p>
                    <?php endif; ?>
                    <?php foreach($data as $key):?>
                        <div>
                            <figure>
                                <img src="
                                <?php echo (in_array($key['type'], array("ranged", "melee"))) ?
                                        constant("ROUTE_IMG") . $key['type'] . ' icon.png' :
                                        constant("ROUTE_IMG") . 'worker icon.png'?>" style="length:50px; width:50px" />
                                <figcaption><?php echo ucfirst($key['type']); ?>
                                    <?php echo ($key['level'] > 0) ? 'level ' . $key['level'] : "";?>
                                    <img src="<?php echo constant("ROUTE_IMG") . 'gold.png';?>" class="gold" />350</figcaption>
                            </figure>
                            <button onclick="recruitWorker('<?php echo $key['type'];?>', '<?php echo $key['level'];?>');"> Recrute </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>