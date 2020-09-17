           <div id="workers">
                <ul>
                    <?php if(!count($data) > 0): ?>
                        <li> No workers available </li>
                    <?php endif; ?>
                    <?php foreach($data as $key):?>
                    <li>
                        <figure>
                            <img src="
                            <?php echo (in_array($key['type'], array("ranged", "melee"))) ?
                                    constant("ROUTE_IMG") . $key['type'] . ' icon.png' :
                                    constant("ROUTE_IMG") . 'worker icon.png'?>" style="length:50px; width:50px" />
                            <figcaption><?php echo ucfirst($key['type']); ?>
                                <?php echo ($key['level'] > 0) ? 'level ' . $key['level'] . ',' : "";?>
                                <img src="<?php echo constant("ROUTE_IMG") . 'gold.png';?>" class="gold" />350</figcaption>
                        </figure>
                    <button onclick="recruitWorker('<?php echo $key['type'];?>', '<?php echo $key['level'];?>');"> Recrute </button>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>