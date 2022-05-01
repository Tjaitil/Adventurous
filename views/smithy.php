            smithy.css|smithy.js|
            <h1 class="page_title">Smithy</h1>
            <div id="smith">
                <p class="help mb-1">
                    Select a mineral below to smith from. Players with miner profiency pay nothing
                </p>
                <h5>Choose your mineral</h5>
                <div id="minerals" class="mb-2">
                    <img src="<?php echo constant('ROUTE_IMG') . 'iron ore.png';?>"
                    class="minerals" title="iron" onclick="showMineral('iron', this);" />
                    <img src="<?php echo constant('ROUTE_IMG') . 'steel ore.png';?>"
                    class="minerals" title="steel" onclick="showMineral('steel', this);" />
                    <img src="<?php echo constant('ROUTE_IMG') . 'gargonite ore.png';?>"
                    class="minerals" title="gargonite" onclick="showMineral('gargonite', this);" />
                    <img src="<?php echo constant('ROUTE_IMG') . 'adron ore.png';?>"
                    class="minerals" title="adron" onclick="showMineral('adron', this);" />
                    <img src="<?php echo constant('ROUTE_IMG') . 'yeqdon ore.png';?>"
                    class="minerals" title="yedqon" onclick="showMineral('yeqdon', this);"/>
                    <img src="<?php echo constant('ROUTE_IMG') . 'frajrite ore.png';?>"
                    class="minerals" title="frajrite" onclick="showMineral('frajrite', this);"/>
                </div>
                <?php
                    function generateTable($array) {
                        
                        // Generate thead and tr tags ?>
                        <thead>
                            <tr>
                                <td>Item</td>
                                <td>Ores required</td>
                                <td>Cost</td>
                                <td></td>
                            </tr>
                        </thead>
                    <?php
                        foreach($array as $key):
                        $mineral = explode(" ", $key['item'])[0];
                        if(strpos($key['item'], 'arrows')):
                        ?>
                        <tr>
                            <td><figure>
                                <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $key['item']. '.png';?>" />
                                <figcaption><?php echo ucwords($key['item']);?></figcaption>
                            </figure></td>
                            <td> <img src="<?php echo constant('ROUTE_IMG') . $mineral . ' bar.png';?>" /> + 
                                 <img src="<?php echo constant('ROUTE_IMG') . 'unfinished arrows.png';?>" />
                                  = 
                                  5 <img src="<?php echo constant('ROUTE_IMG') . $key['item']. '.png';?>" /></td>
                            <td>
                                <?php if($_SESSION['gamedata']['profiency'] === 'miner'): 
                                    echo $key['cost'] * 0;?>
                                    <s><?php echo $key['cost'];?></s>
                                <?php else: 
                                    echo $key['cost'];
                                endif;?>                               
                                <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" />
                            </td>
                            <td> <input type="number" min="0" />
                            <button> Smith </button></td>
                        </tr>
                        <?php elseif(strpos($key['item'], 'knives')): ?>
                            <tr>
                                <td><figure>
                                    <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $key['item']. '.png';?>" />
                                    <figcaption><?php echo ucwords($key['item']);?></figcaption>
                                </figure></td>
                                <td> 1 <img src="<?php echo constant('ROUTE_IMG') . $mineral . ' bar.png';?>" />
                                    = 
                                    3 <img src="<?php echo constant('ROUTE_IMG') . $key['item']. '.png';?>" /></td>
                                <td>
                                    <?php if($_SESSION['gamedata']['profiency'] === 'miner'): 
                                        echo $key['cost'] * 0;?>
                                        <s><?php echo $key['cost'];?></s>
                                    <?php else: 
                                        echo $key['cost'];
                                    endif;?>                               
                                    <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" />
                                </td>
                                <td> <input type="number" min="0" />
                                <button> Smith </button></td>
                            </tr>
                        <?php else: ?>
                        <tr>
                            <td><figure>
                                <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>" />
                                <figcaption><?php echo ucwords($key['item']);?></figcaption>
                            </figure></td>
                            <td><?php echo $key['mineral_required'];?></td>
                            <td><?php echo $key['cost'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
                            <td><input type="number" min="0" />
                            <button> Smith </button></td>
                        </tr>
                     <?php endif;endforeach;
                     };?>
                <table id="iron" class="noDisplayBlock">
                    <?php generateTable($this->data['iron']);?>
                </table>
                <table id="steel" class="noDisplayBlock">
                    <?php generateTable($this->data['steel']);?>
                </table>
                <table id="gargonite" class="noDisplayBlock">
                    <?php generateTable($this->data['gargonite']);?>
                </table>
                <table id="adron" class="noDisplayBlock">
                    <?php generateTable($this->data['adron']);?>
                </table>
                <table id="yeqdon" class="noDisplayBlock">
                    <?php generateTable($this->data['yeqdon']);?>
                </table>
                <table id="frajrite" class="noDisplayBlock">
                    <?php generateTable($this->data['frajrite']);?>
                </table>
            </div>