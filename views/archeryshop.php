            archeryshop.css|archeryshop.js|
            <h3 class="page_title"><?php echo $title;?></h3>
            <div id="fletch">
                <table>
                    <thead>
                        <tr>
                            <td> Item </td>
                            <td> Wood Required </td>
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
                            <td><?php echo $key['wood_required'];?></td>
                            <td><?php echo $key['cost'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
                            <td><input type="number" min="0" />
                            <button> Make</button></td>
                        </tr>
                        <?php endforeach;?> 
                    </thead>
                </table>
            </div>