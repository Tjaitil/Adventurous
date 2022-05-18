<?php
    if(!count($data) > 0):?>
        <p> No assignments available here </p>
    <?php endif;
    foreach($data as $key): ?>
    <div class="trader_assignment div_content div_content_dark lightTextColor">
        <p>
            <img class="mb-1" src="<?php echo constant('ROUTE_IMG') . $key['cargo'] . '.png';?>"/><br>
            <?php echo $key['assignment_amount'];?> * <?php echo ucwords($key['cargo']);?>
        </p>
        <p><?php echo ucfirst($key['base']);?> -> <?php echo ucfirst($key['destination']);?></p>
        <p><?php echo $key['time'];?></p>
        <p><?php echo ucfirst($key['assignment_type']);?></p>
        <p class="trader_assignment_id">
            #
            <span class="trader_assignment_id">
                <?php echo $key['assignment_id'];?>
            </span>
        </p>
    </div>
<?php endforeach;?>