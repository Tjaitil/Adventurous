    <?php foreach($data['horse_shop'] as $key): ?>
     <tr>
        <td><?php echo $key['type'];?></td>
        <td><?php echo $key['value']; ?></td>
        <td><?php echo $key[$data['city']];?></td>
        <td><button onclick="buyItem('<?php echo $key['type']; ?>','horse');"> Buy </button></td>
     </tr>              
    <?php endforeach;?>