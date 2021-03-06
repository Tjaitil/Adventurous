            smithy.css|smithy.js|
            <h3 class="page_title"> Smithy </h3>
            <div id="smith">
                <h5>Choose your mineral</h5>
                <div id="minerals">
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
                                <td> Item: </td>
                                <td> Ores required: </td>
                                <td> Cost: </td>
                                <td></td>
                            </tr>
                        </thead>
                    <?php
                        foreach($array as $key):
                        if(strpos($key['item'], 'arrows') === false): ?>
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
                        <?php else:
                        $mineral = explode(" ", $key['item'])[0];
                        ?>
                        <tr>
                            <td><figure>
                                <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $key['item']. '.png';?>" />
                                <figcaption><?php echo ucwords($key['item']);?></figcaption>
                            </figure></td>
                            <td> 1 <img src="<?php echo constant('ROUTE_IMG') . $mineral . ' bar.png';?>" /> =
                                15 <img src="<?php echo constant('ROUTE_IMG') . $key['item']. '.png';?>" /></td>
                            <td><?php echo $key['cost'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
                            <td> <input type="number" min="0" />
                            <button> Smith </button></td>
                        </tr>
                     <?php endif;endforeach;
                     };?>
                <table id="iron">
                    <?php generateTable($this->data['iron']);?>
                </table>
                <table id="steel">
                    <?php generateTable($this->data['steel']);?>
                </table>
                <table id="gargonite">
                    <?php generateTable($this->data['gargonite']);?>
                </table>
                <table id="adron">
                    <?php generateTable($this->data['adron']);?>
                </table>
                <table id="yeqdon">
                    <?php generateTable($this->data['yeqdon']);?>
                </table>
                <table id="frajrite">
                    <?php generateTable($this->data['frajrite']);?>
                </table>
            </div>