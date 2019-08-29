<?php if(!count($data) > 0):?>
    <tr>
        <td colspan="5"> None </td>
    </tr>
<?php endif;?>
<?php
    krsort($data);
    foreach($data as $key): ?>
            <tr>
                <td><?php echo $key['type'];?></td>
                <td><?php echo $key['item'];?></td>
                <td><?php echo $key['amount'];?></td>
                <td><?php echo $key['price_ea'];?></td>
            </tr>
    <?php endforeach;?>