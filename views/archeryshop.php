            archeryshop.css|archeryshop.js|
            <h3 class="page_title"><?php echo $title;?></h3>
            <div id="fletch">
                <p class="help">
                    Craft bows, unfinished arrows or arrow shafts from logs. Some bows will require a certain total 
                    level of warriors. check <a href="gameguide/warrior">gameguide</a>
                </p>
                <table>
                    <thead>
                        <tr>
                            <td> Item </td>
                            <td> Items Required </td>
                            <td> Cost </td>
                            <td></td>
                        </tr>
                        <?php
                        
                        foreach($this->data as $key):?>
                            <tr>
                            <td>
                                <div class="item">    
                                    <figure>
                                        <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>" />
                                        <figcaption class="tooltip"><?php echo ucwords($key['item']);?></figcaption>
                                    </figure>
                                    <span class="item_amount"></span>
                                </div>
                            </td>
                            <td <?php if(strpos($key['item'], 'unfinished')): ?>
                                class="archeryShop_required"
                                <?php endif;?>>
                                <?php
                                    $required;
                                    $material = explode(" ", $key['item'])[0];
                                    switch($key['item']) {
                                        case 'oak bow':
                                        case 'spruce bow':
                                        case 'birch bow':
                                        case 'yew bow': ?>
                                            <div class="item">    
                                                <figure>
                                                    <img src="<?php echo constant('ROUTE_IMG') . $material . ' logs.png';?>" />
                                                    <figcaption class="tooltip"><?php echo ucwords($material . ' logs');?></figcaption>
                                                </figure>
                                                <span class="item_amount">2</span>
                                            </div>
                                            <?php
                                            break;
                                        case 'arrow shaft':?>
                                            <div class="item">    
                                                <figure>
                                                    <img src="<?php echo constant('ROUTE_IMG') . 'oak log.png';?>" />
                                                    <figcaption class="tooltip"><?php echo ucwords('oak logs');?></figcaption>
                                                </figure>
                                                <span class="item_amount">1</span>
                                            </div>
                                            <?php
                                            break;
                                        case 'unfinished arrows': ?>
                                            <div class="item">    
                                                <figure>
                                                    <img src="<?php echo constant('ROUTE_IMG') . 'arrow shaft.png';?>" />
                                                    <figcaption class="tooltip"><?php echo ucwords('arrow shaft');?></figcaption>
                                                </figure>
                                                <span class="item_amount">1</span>
                                            </div>
                                            <div class="item">    
                                                <figure>
                                                    <img src="<?php echo constant('ROUTE_IMG') . 'feathers.png';?>" />
                                                    <figcaption class="tooltip"><?php echo ucwords('feathers');?></figcaption>
                                                </figure>
                                                <span class="item_amount">1</span>
                                            </div>
                                            <?php
                                            break;
                                        default:
                                            break;
                                    }
                                ?>
                            </td>
                            <td><?php echo $key['cost'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
                            <td><input type="number" min="0" />
                            <button> Make</button></td>
                        </tr>
                        <?php endforeach;?> 
                    </thead>
                </table>
            </div>