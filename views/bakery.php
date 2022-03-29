            bakery.css|bakery.js|
            <h3 class="page_title"> Bakery </h3>
            <div id="bakery">
                <div class="help">
                    <p>Here you can make food to decrease your hunger. Players with farmer profiency pay 75 % less
                        </br> For more information head to <a href="gameguide/bakery" target="_blank">gameguide/bakery</a></p>
                </div>
                <table>
                    <thead>
                        <tr>
                            <td> Type: </td>
                            <td> Ingredients: </td>
                            <td> Cost: </td>
                            <td> Heal: </td>
                            <td></td>
                        </tr>
                    </thead>
                    <?php
                    foreach($this->data as $key): ?>
                        <tr>
                            <td>
                                <div class="item">    
                                    <figure>
                                        <img src="<?php echo constant('ROUTE_IMG') . $key['type'] . '.png';?>" />
                                        <figcaption class="tooltip"><?php echo ucwords($key['type']);?></figcaption>
                                    </figure>
                                    <span class="item_amount"></span>
                                </div>
                            </td>
                            <td>
                                <div class="<?php echo (count($key['ingredients']) > 1) ? "table_required_multiple" 
                                                                                            : "bakery_required"?>">
                                    <?php for($i = 0; $i < count($key['ingredients']); $i++): ?>
                                            <p class="bakery_required_amount"><?php echo $key['ingredients'][$i]['amount'];?></p>
                                            <div class="item">    
                                                <figure>
                                                    <img src="<?php echo constant('ROUTE_IMG') . $key['ingredients'][$i]['ingredient'] . '.png';?>" />
                                                    <figcaption class="tooltip">
                                                        <?php echo ucwords($key['ingredients'][$i]['ingredient']);?>
                                                    </figcaption>
                                                </figure>
                                                <span class="item_amount"></span>
                                            </div>
                                    <?php endfor;?>
                                </div>
                            </td>
                            <td><?php if($_SESSION['gamedata']['profiency'] === 'farmer'):
                                echo $key['cost'] * 0.25 ;?>
                                <s><?php echo $key['cost'];?></s>
                                 <?php else: 
                                 echo $key['cost'];?>                                   
                                 <?php endif;?>
                                 <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
                            <td> 1 </td>
                            <td><input type="number" min="0" /><button onclick="make('Cooked Potato', this);"> Make </button></td>
                        </tr>
                    <?php endforeach;?>
                    <?php function() {

                    }
                    ?>
