<?php
    for($i = 0; $i < 6; $i ++): ?>
    <?php if(isset($data[$i])): ?>
        <tr>
                
                <td><?php echo $data[$i]['type'];?><input type="hidden" value="<?php echo $data[$i]['id'];?>" /></td>
                <td>
                    <div class="item" class="escrow_item">
                        <figure>
                            <img src="<?php echo constant('ROUTE_IMG') . $data[$i]['item'] . '.png';?>" />
                            <figcaption class="tooltip"><?php echo ucwords($data[$i]['item']); ?></figcaption>
                        </figure>
                        <span class="item_amount"></span>
                    </div>
                </td>
                <td><?php echo $data[$i]['price_ea'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
                <td><?php echo $data[$i]['progress'] , ' / ' , $data[$i]['amount'];?> </td>
                <td><?php if($data[$i]['box_amount'] > 0): ?>
                     <div class="item box_item">
                        <figure>
                            <img src="<?php echo constant('ROUTE_IMG') . $data[$i]['box_item'] . '.png';?>" />
                            <figcaption class="tooltip"><?php echo ucwords($data[$i]['box_item']); ?></figcaption>
                        </figure>
                        <span class="item_amount"><? echo $data[$i]['box_amount'];?></span>
                    </div>
                    <?php else:?>
                    <?php endif;?>
                </td>
                <td><?php if(intval($data[$i]['progress']) < intval($data[$i]['amount'])): ?>
                    <button> Cancel offer</button>
                    <?php endif;?>
                </td>
        </tr>
    <?php else:?>
        <tr>
            <td colspan="6"><button> Buy </button><button> Sell </button></td>
        </tr>
    
    <?php endif;
        endfor;?>