    <?php foreach($data['horse_shop'] as $key): ?>
     <tr>
        <td><?php echo ucfirst($key['type']);?></td>
        <td><?php echo $key['value']; ?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
        <td><?php echo $key[$data['city']];?></td>
        <td><button onclick="buyItem('<?php echo $key['type']; ?>','horse');"> Buy </button></td>
     </tr>              
    <?php endforeach;?>