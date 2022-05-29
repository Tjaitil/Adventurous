<?php
    if($data['assignment_id'] != 0): ?>
    
        <div class="traderAssignment_fullColumn">
                <figure class="item">
                    <img src="<?php echo constant('ROUTE_IMG') . strtolower($data['cargo']) . '.png';?>" />
                        <figcaption><?php echo ucwords($data['cargo']);?></figcaption>
                </figure>
        </div>
        <div class="traderAssignment_fullColumn">
            <span>
                Route:&nbsp;
                <?php echo ucwords($data['base']) . ' -> ' . ucwords($data['destination']);?>
            </span>
        </div>
        <div id="traderAssignment_progressBar" class="progressBarContainer traderAssignment_fullColumn mx-auto">
            <div class="progressBarOverlayShadow">
            </div>
            <div class="progressBarOverlay">
            </div>
            <div class="progressBar">
                <span class="progressBar_currentValue"><?php echo $data['delivered'];?></span>
                &nbsp/&nbsp
                <span class="progressBar_maxValue"><?php echo $data['assignment_amount'];?></span>
            </div>
        </div>
        <div>
            <span>
                Assignment type: &nbsp;
                <?php echo $data['assignment_type'];?>
            </span>
        </div>
        <div>
            Cart Capasity: <span id="traderAssignment_cart_amount"><?php echo $data['cart_amount'];?></span>  /
            <span id="traderAssignment_cart_"><?php echo $data['capasity'];?></span>
        </div>
        <div class="traderAssignment_fullColumn mb-2">
                <button id="traderAssignment-pick-up">Pick up items</button>
                <button id="traderAssignment-deliver">Deliver</button>
        </div>
    <?php else: ?>
        <div class="traderAssignment_fullColumn">
            No current assignment, to get new assignment see the list above
        </div>
    <?php endif;?>