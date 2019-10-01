<?php $i = 0;
    if(!count($data['trader_assignments']) > 0):?>
    <tr>
        <td colspan="8"> None </td>
    </tr>
    <?php endif;
    foreach($data['trader_assignments'] as $key): ?>
    <tr>
        <td class="city"><?php echo ucfirst($key['base']);?></td>
        <td class="destination"><?php echo ucfirst($key['destination']);?></td>
        <td class="cargo"><?php echo ucwords($key['cargo']);?></td>
        <td class="assignment_amount"><?php echo $key['assignment_amount'];?></td>
        <td class="time"><?php echo $key['time'];?></td>
        <td class="assignment_type"><?php echo ucfirst($key['assignment_type']);?></td>
        <td><button onclick="newAssignment(<?php echo $key['assignment_id'];?>);"> Do task </button></td>
    </tr>
<?php $i++; endforeach;?>