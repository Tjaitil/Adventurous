<?php foreach($data as $key):?>
    <tr>
        <td><img src="<?php echo constant('ROUTE_IMG') . $key['role'] . ' icon.png';?>"></td>
        <td><?php echo ($key['role'] === 'warrior') ? 'Warrior' : ucwords($key['required']);?>
            <?php 
                if($key['role'] === "trader") {
                    $src = "diplomacy icon";
                } else {
                    $src = $key['required'];
                }

                if($key['role'] !== "warrior"): ?>
                    <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $src .'.png';?>"/></td>
                <?php endif;?>
        <td class="provided-amount" <?php if($key['provided'] === $key['amount']) echo 'class="able-color"';?>>
            <?php echo $key['provided'], '/', $key['amount'];?>
        </td>
    </tr>
<?php endforeach; ?>