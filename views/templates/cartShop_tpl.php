<?php foreach($data['cart_shop'] as $key): ?>
<tr>
    <td><?php echo $key['wheel'];?></td>
    <td><?php echo $key['wood'];?></td>
    <td><?php echo $key['value'];?></td>
    <td><?php echo $key['capasity'];?></td>
    <td><?php echo $key[$data['city']];?></td>
    <td><?php echo $key['mineral_amount'];?></td>
    <td><?php echo $key['wood_amount'];?></td>
    <td><button onclick="buyItem('<?php echo $key['wheel']?>', 'cart');"> Buy</button></td>
</tr>
<?php endforeach;?>