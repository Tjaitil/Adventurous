<?php foreach($data as $key):?>
    <tr>
        <td><input type="checkbox" value="<?php echo $key['warrior_id'];?>" /></td>
        <td><?php echo $key['warrior_id'];?></td>
        <td><?php echo $key['type'];?></td>
        <td><?php echo $key['health'];
        echo ($key['rest'] == '1') ? " (" . calculateHealth($key['rest_start'], $key['health']) . ")" : "";?></td>
        <td><?php echo $key['status'];?></td>
    </tr>
    
<?php endforeach;?>
<?php
    function calculateHealth($rest_start, $health) {
        $rest_start = date_timestamp_get(new DateTime($rest_start));
        $date_now = date_timestamp_get(new DateTime(date("Y-m-d H:i:s")));
        $health_gained = (($date_now - $rest_start) / 60) * 3;
        if($health_gained + $health > 100) {
            return $health = 100;
        }
        else {
            return $health + $health_gained;
        }
    }
?>