<?php foreach($data as $key):?>
    <tr>
        <td><?php echo ucfirst($key['role']);?></td>
        <td><?php echo ucwords($key['required']);?>
            <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $key['required'];?>"/></td>
        <td><?php echo $key['provided'], '/', $key['amount'];?></td>
    </tr>
<?php endforeach; ?>
