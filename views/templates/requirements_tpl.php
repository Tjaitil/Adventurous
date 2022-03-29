<?php foreach($data as $key):?>
    <tr>
        <td><img src="<?php echo constant('ROUTE_IMG') . $key['role'] . ' icon.png';?>"></td>
        <td><?php echo ucwords($key['required']);?>
            <img class="item_img" src="<?php echo
            (strpos($key['required'], 'warrior'))? constant('ROUTE_IMG') . str_replace('warrior', 'icon',$key['required']) . '.png'
                                                    : constant('ROUTE_IMG') . $key['required'] . '.png';?>"/></td>
        <td <?php if($key['provided'] === $key['amount']) echo 'class="able-color"';?>>
            <?php echo $key['provided'], '/', $key['amount'];?>
        </td>
    </tr>
<?php endforeach; ?>
