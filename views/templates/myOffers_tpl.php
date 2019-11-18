<?php
    for($i = 0; $i < 6; $i ++): ?>
    <?php if(isset($data[$i])): ?>
        <tr>
                
                <td><?php echo $data[$i]['type'];?><input type="hidden" value="<?php echo $data[$i]['id'];?>" /></td>
                <td><?php echo ucwords($data[$i]['item']);?></td>
                <td><?php echo $data[$i]['price_ea'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.jpg';?>" /></td>
                <td><?php echo $data[$i]['progress'] , '/' , $data[$i]['amount'];?> </td>
                <td><button> Cancel offer</button></td>
                <td><?php if($data[$i]['box_amount'] > 0): ?>
                     <div class="inventory_item">
                        <figure>
                            <img src="<?php echo constant('ROUTE_IMG') . $data[$i]['box_item'] . '.png';?>" />
                            <figcaption class="tooltip"><?php echo ucwords($data[$i]['box_item']); ?></figcaption>
                        </figure>
                        <span class="item_amount"><? echo $data[$i]['box_amount'];?></span>
                    </div>
                    <?php else:?>
                    <?php endif;?>
                </td>
        </tr>
    <?php else:?>
        <tr>
            <td colspan="6"><button> Buy </button><button> Sell </button></td>
        </tr>
    
    <?php endif;
        endfor;?>