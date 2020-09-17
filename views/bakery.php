            market.css|market.js|
            <h3 class="page_title"> Bakery </h3>
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
                $list[0] = array("item" => "cooked potato", "ingredients", "cost" => 50, "food_units" => 1);
                $list[0] = array("item" => "flour", "required" => "wheat", "require_amount" => 3, "cost" => 50, "food_units" => 1);
                
                foreach($list as $key): ?>
                <tr>
                    <td><div class="item">
                            <figure onclick="show_title(this, false);">
                                <img src="<?php echo constant('ROUTE_IMG') . 'cooked potato' . '.png';?>" />
                                <figcaption class="tooltip"><?php echo ucwords('cooked potato'); ?></figcaption>
                            </figure>
                        </div>
                    </td>
                    <td> Potato <img src="<?php echo constant('ROUTE_IMG') . '';?>" /></td>
                    <td> 50 <img class="gold" src="<?php echo constant('ROUTE_IMG') . '';?>" /></td>
                    <td> 1 </td>
                    <td><input type="number" min="0" /><button onclick="make('Cooked Potato', this);"> Make </button></td>
                </tr>
                <?php endforeach;?>
                <tr>
                    <td><div class="item">
                            <figure onclick="show_title(this, false);">
                                <img src="<?php echo constant('ROUTE_IMG') . 'cooked potato' . '.png';?>" />
                                <figcaption class="tooltip"><?php echo ucwords('cooked potato'); ?></figcaption>
                            </figure>
                        </div>
                    </td>
                    <td> Potato <img src="<?php echo constant('ROUTE_IMG') . '';?>" /></td>
                    <td> 50 <img class="gold" src="<?php echo constant('ROUTE_IMG') . '';?>" /></td>
                    <td> 1 </td>
                    <td><input type="number" min="0" /><button onclick="make('Cooked Potato', this);"> Make </button></td>
                </tr>
                <tr>
                    <td> Flour </td>
                    <td> Wheat </td>
                    <td> 3 </td>
                    <td> - </td>
                    <td><input type="number" min="0" /><button onclick="make('Cooked Potato', this);"> Make </button></td>
                </tr>
            </table>
            <button onclick="lol();"> Click </button>
