<?php foreach($data['cart_shop'] as $key): ?>
<tr>
    <td><?php echo ucfirst($key['wheel']);?><img src="<?php echo constant('ROUTE_IMG') . $key['wheel'] . ' bar.png';?>"/></td>
    <td><?php echo ucfirst($key['wood']);?><img src="<?php echo constant('ROUTE_IMG') . $key['wood'] . ' logs.png';?>"/></td>
    <td><?php echo 300;?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
    <td><?php echo $key['capasity'];?></td>
    <td><?php echo $key['mineral_amount'];?></td>
    <td><?php echo $key['wood_amount'];?></td>
    <td><button onclick="buyItem('<?php echo $key['wheel']?>', 'cart');"> Make </button></td>
</tr>
<?php endforeach;?>