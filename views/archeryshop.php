            archeryshop.css|archeryshop.js|
            <h3 class="page_title"><?php echo $title;?></h3>
            <div id="fletch">
                <table>
                    <thead>
                        <tr>
                            <td> Item </td>
                            <td> Items Required </td>
                            <td> Cost </td>
                            <td></td>
                        </tr>
                        <?php
                        
                        foreach($this->data as $key => $element):?>
                            <tr>
                            <td><figure>
                                <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $element['item']. '.png';?>" />
                                <figcaption><?php echo ucwords($key['item']);?></figcaption>
                            </figure></td>
                            <td <?php if($key !== array_key_last($this->data)): ?>
                                class="archeryShop_required"
                                <?php endif;?>>
                                <?php
                                    $required;
                                    switch($element['item']) {
                                        case 'oak bow': ?>
                                            <p class="archeryShop_required_amount"><?php echo '3 x'?></p>
                                            <figure>
                                                <img src="<?php echo constant('ROUTE_IMG') . 'oak log.png'?>" />
                                                <figcaption> Oak Log</figcaption>
                                            </figure>
                                            <?php
                                            break;
                                        case 'birch bow': ?>
                                            <p class="archeryShop_required_amount"><?php echo '3 x'?></p>
                                            <figure>
                                                <img src="<?php echo constant('ROUTE_IMG') . 'birch log.png'?>" />
                                                <figcaption> Birch Log</figcaption>
                                            </figure>
                                            <?php
                                            break;
                                        case 'yew bow': ?>
                                            <p class="archeryShop_required_amount"><?php echo '3 x'?></p>
                                            <figure>
                                                <img src="<?php echo constant('ROUTE_IMG') . 'yew log.png'?>" />
                                                <figcaption> Yew Log</figcaption>
                                            </figure>
                                            <?php
                                            break;
                                        case 'arrow shaft':?>
                                            <p class="archeryShop_required_amount"><?php echo '1 x'?><p>
                                            <figure>
                                                <img src="<?php echo constant('ROUTE_IMG') . 'oak log.png'?>" />
                                                <figcaption> Oak Log</figcaption>
                                            </figure>
                                            <?php
                                            break;
                                        case 'unfinished arrows': ?>
                                            <div class="archeryShop_required">
                                                <p class="archeryShop_required_amount"><?php echo '1 x'?></p>
                                                <figure>
                                                    <img src="<?php echo constant('ROUTE_IMG') . 'arrow shaft.png'?>" />
                                                    <figcaption> Arrow shaft</figcaption>
                                                </figure>
                                            </div>
                                            <div class="archeryShop_required">
                                                <p class="archeryShop_required_amount"><?php echo '1 x'?></p>
                                                <figure>
                                                    <img src="<?php echo constant('ROUTE_IMG') . 'feather.png'?>" />
                                                    <figcaption> Feather </figcaption>
                                                </figure>
                                            </div>
                                            <?php
                                            break;
                                        default:
                                            break;
                                    }
                                ?>
                            </td>
                            <td><?php echo $element['cost'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
                            <td><input type="number" min="0" />
                            <button> Make</button></td>
                        </tr>
                        <?php endforeach;?> 
                    </thead>
                </table>
            </div>