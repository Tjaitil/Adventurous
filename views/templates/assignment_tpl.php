<?php $i = 0; foreach($data['trader_assignments'] as $key): ?>
    <td class="city"><?php echo ucfirst($key['base']);?></td>
    <td class="destination"><?php echo ucfirst($key['destination']);?></td>
    <td class="cargo"><?php echo ucwords($key['cargo']);?></td>
    <td class="cargo_amount"><?php echo $key['cargo_amount'];?></td>
    <td class="time"><?php echo $key['time'];?></td>
    <td><img id="gold" src="<?php echo constant('ROUTE_IMG') . $key['reward'][0] . '.jpg'?>" style="width:20px;height:20px;"/>
    <?php echo $key['reward'][1];?></td>
    <td class="assignment_type"><?php echo ucfirst($key['assignment_type']);?></td>
    <td><button onclick="newAssignment(<?php echo $key['assignment_id'];?>);"> Do task </button></td>
<?php $i++; endforeach;?>