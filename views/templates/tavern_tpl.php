           <div id="farmers">
                <ul>
                    <?php if(!isset($data['tavern']['tavern_workers']['farmer_amount'])): ?>
                        <li> No farmers available </li>
                    <?php endif; ?>
                    <?php if(isset($data['tavern']['tavern_workers']['miner_amount'])):
                    for($i = 0; $i < $data['tavern']['tavern_workers']['farmer_amount']; $i++): ?>
                    <li><img src="<?php echo constant("ROUTE_IMG"). 'farmer.jpg'?>" style="length:50px; width:50px" />
                    <p><?php echo 'Farmer'?></p>
                    <p>Value: 400</p>
                    <button onclick="buyWorker('farmer')"> Recrute </button></li>
                    <?php endfor;
                          endif;?>
                </ul>
            </div>
            <div id="miners">
                <ul>
                    <?php if(!isset($data['tavern']['tavern_workers']['miner_amount'])): ?>
                        <li> No miners available </li>
                    <?php endif; ?>
                    <?php if(isset($data['tavern']['tavern_workers']['miner_amount'])):
                    for($i = 0; $i < $data['tavern']['tavern_workers']['miner_amount']; $i++):?>
                    <li><img src="<?php echo constant("ROUTE_IMG"). 'miner.jpg'?>" style="length:50px; width:50px" />
                    <p><?php echo 'miner'?></p>
                    <p>Value: 300</p>
                    <button onclick="buyWorker('miner')"> Recrute </button></li>
                    <?php endfor;
                          endif;?>
                </ul>
            </div>
            <div id="warriors">
                <ul>
                    <?php if(!isset($data['tavern']['tavern_warriors'])): ?>
                        <li> No warriors available </li>
                    <?php endif; ?>
                    <?php if(isset($data['tavern']['tavern_warriors'])):
                    foreach($data['tavern']['tavern_warriors'] as $key):?>
                    <li><img src="<?php echo constant("ROUTE_IMG"). 'warrior.jpg'?>" style="length:50px; width:50px" />
                    <p><?php echo $key['type']; ?> level: <?php echo $key['level'];?></p>
                    <p>Value 350</p>
                    <button onclick="buyWorker('<?php echo $key['type'];?>', '<?php echo $key['level'];?>');"> Recrute </button></li>
                    <?php endforeach;
                          endif;?>
                </ul>
            </div>