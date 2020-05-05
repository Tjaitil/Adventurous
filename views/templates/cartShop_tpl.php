<?php foreach($data['cart_shop'] as $key): ?>
<tr>
    <td><?php echo ucfirst($key['wheel']);?></td>
    <td><?php echo ucfirst($key['wood']);?></td>
    <td><?php echo $key['value'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
    <td><?php echo $key['capasity'];?></td>
    <td><?php echo $key[$data['city']];?></td>
    <td><?php echo $key['mineral_amount'];?></td>
    <td><?php echo $key['wood_amount'];?></td>
    <td><button onclick="buyItem('<?php echo $key['wheel']?>', 'cart');"> Buy</button></td>
</tr>
<?php endforeach;?>