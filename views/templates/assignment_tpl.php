<?php
    if(!count($data) > 0):?>
        <p> No assignments available here </p>
    <?php endif;
    foreach($data as $key): 
    $not_current_location = $_SESSION['gamedata']['location'] !== $key['base'];
    ?>
    <div class="trader_assignment div_content div_content_dark lightTextColor <?php echo ($not_current_location) ? 'trader_assignment_locked': '';?>">
        <p>
            <img class="mb-1" src="<?php echo constant('ROUTE_IMG') . $key['cargo'] . '.png';?>"/><br>
            <?php echo $key['assignment_amount'];?> X <?php echo ucwords($key['cargo']);?>
        </p>
        <p><span class="<?php echo ($not_current_location) ? 'not-able-color' : ''?>"><?php echo ucfirst($key['base']);?></span> -> <?php echo ucfirst($key['destination']);?></p>
        <p><?php echo $key['time'];?></p>
        <p class="<?php echo checkTraderLevel($key['assignment_type']);?>">
            <?php echo ucfirst($key['assignment_type']);?>
        </p>
        <p>
            #
            <span class="trader_assignment_id">
                <?php echo $key['assignment_id'];?>
            </span>
        </p>
    </div>
<?php endforeach;
function checkTraderLevel($assignment_type) {
    $assignment_type = strtolower($assignment_type);
    $assignment_levels = array(
        "small" => 1,
        "favor" => 1,
        "medium" => 15,
        "large" => 31,
    );
    if(!isset($assignment_levels[$assignment_type])) return '';
    if($assignment_levels[$assignment_type] > $_SESSION['gamedata']['trader']['level']) {
        return "not-able-color";
    }
}

?>