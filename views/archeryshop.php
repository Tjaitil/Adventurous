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
                        
                        foreach($this->data as $key):?>
                            <tr>
                            <td><figure>
                                <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $key['item']. '.png';?>" />
                                <figcaption><?php echo ucwords($key['item']);?></figcaption>
                            </figure></td>
                            <td>
                                <?php
                                    switch($key['item']) {
                                        case 'oak bow':
                                            echo '3 x Oak log';
                                            break;
                                        case 'birch bow':
                                            echo '3 x Birch log';
                                            break;
                                        case 'yew bow':
                                            echo '3 x Yew log';
                                            break;
                                        case 'arrow shaft':
                                            echo '1 x Oak log';
                                            break;
                                        case 'unfinished arrows':
                                            echo '1 x Arrow shaft';
                                            echo '</br>';
                                            echo '1 x Feather';
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