<?php foreach($data as $key):?>
    <tr>
        <td><?php echo ucfirst($key['role']);?></td>
        <td><?php echo ucwords($key['required']);?>
            <img class="item_img" src="<?php echo
            (strpos($key['required'], 'warrior'))? constant('ROUTE_IMG') . str_replace('warrior', 'icon',$key['required']) . '.png'
                                                    : constant('ROUTE_IMG') . $key['required'] . '.png';?>"/></td>
        <td><?php echo $key['provided'], '/', $key['amount'];?></td>
    </tr>
<?php endforeach; ?>
