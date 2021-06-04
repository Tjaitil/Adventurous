            market.css|market.js|
            <h3 class="page_title"> Bakery </h3>
            <div id="bakery">
                <table>
                    <thead>
                        <tr>
                            <td> Type: </td>
                            <td> Ingredients: </td>
                            <td> Cost: </td>
                            <td> Food units: </td>
                            <td></td>
                        </tr>
                    </thead>
                    <?php
                    $list = array();
                    $list[] = array("item" => "cooked potato", "ingredients" => "potato", "cost" => 50, "food_units" => 1);
                    $list[] = array("item" => "flour", "ingredients" => "wheat", "require_amount" => 3, "cost" => 50, "food_units" => 1);
                    $list[] = array("item" => "chicken", "ingredients" => "raw chicken", "require_amount" => 1, "cost" => 50, "food_units" => 1);
                    $list[] = array("item" => "pork", "ingredients" => "raw pork", "require_amount" => 1, "cost" => 50, "food_units" => 1);
                    $list[] = array("item" => "beef", "ingredients" => "raw beef", "require_amount" => 1, "cost" => 50, "food_units" => 1);
                    
                    foreach($list as $key): ?>
                    <tr>
                        <td>
                            <div class="item">    
                                <figure onclick="show_title(this, false);">
                                    <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>" />
                                    <figcaption class="tooltip"><?php echo ucwords($key['item']);?></figcaption>
                                </figure>
                                <span class="item_amount"></span>
                            </div>
                            <?php echo $key['item'];?><img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>" />
                        </td>
                        <td><?php echo $key['ingredients'];?><img src="<?php echo constant('ROUTE_IMG') . $key['ingredients'] . '.png';?>" />
                        </td>
                        <td> 50 <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
                        <td> 1 </td>
                        <td><input type="number" min="0" /><button onclick="make('Cooked Potato', this);"> Make </button></td>
                    </tr>
                    <?php endforeach;?>
                </table>
            </div>
