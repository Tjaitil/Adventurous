<?php $i = 0;
    if(!count($data) > 0):?>
    <tr>
        <td colspan="8"> No assignments available </td>
    </tr>
    <?php endif;
    foreach($data as $key): ?>
    <tr>
        <td class="city"><?php echo ucfirst($key['base']);?></td>
        <td class="destination"><?php echo ucfirst($key['destination']);?></td>
        <td class="cargo"><img src="<?php echo constant('ROUTE_IMG') . $key['cargo'] . '.png';?>"/>
            <?php echo ucwords($key['cargo']);?>
        </td>
        <td class="assignment_amount"><?php echo $key['assignment_amount'];?></td>
        <td class="time"><?php echo $key['time'];?></td>
        <td class="assignment_type"><?php echo ucfirst($key['assignment_type']);?></td>
        <td><button onclick="newAssignment(<?php echo $key['assignment_id'];?>);"> Do task </button></td>
    </tr>
<?php $i++; endforeach;?>