<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php');?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <h3 class="page_title"> Smithy </h3>
            <div id="smith">
                <div id="minerals">
                    <img src="<?php echo constant('ROUTE_IMG') . 'iron.png';?>"
                    class="minerals" title="iron" onclick="showMineral('iron', this);" />
                    <img src="<?php echo constant('ROUTE_IMG') . 'steel.png';?>"
                    class="minerals" title="steel" onclick="showMineral('steel', this);" />
                    <img src="<?php echo constant('ROUTE_IMG') . 'gargonite.png';?>"
                    class="minerals" title="gargonite" onclick="showMineral('gargonite', this);" />
                    <img src="<?php echo constant('ROUTE_IMG') . 'adron.png';?>"
                    class="minerals" title="adron" onclick="showMineral('adron', this);" />
                    <img src="<?php echo constant('ROUTE_IMG') . 'yeqdon.png';?>"
                    class="minerals" title="yedqon" onclick="showMineral('yeqdon', this);"/>
                    <img src="<?php echo constant('ROUTE_IMG') . 'frajrite.png';?>"
                    class="minerals" title="frajrite" onclick="showMineral('frajrite', this);"/>
                </div>
                <?php
                    function generateTable($array) {
                        foreach($array as $key):
                        if(strpos($key['item'], 'arrows') === false): ?>
                        <tr>
                            <td><figure>
                                <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $key['item']. '.png';?>" />
                                <figcaption><?php echo ucwords($key['item']);?></figcaption>
                            </figure></td>
                            <td><?php echo $key['amount_required'];?></td>
                            <td><?php echo $key['cost'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
                            <td><input type="number" min="0" />
                            <button> Make</button></td>
                        </tr>
                        <?php else:
                        $mineral = explode(" ", $key['item'])[0];
                        ?>
                        <tr>
                            <td><figure>
                                <img class="item_img" src="<?php echo constant('ROUTE_IMG') . $key['item']. '.png';?>" />
                                <figcaption><?php echo ucwords($key['item']);?></figcaption>
                            </figure></td>
                            <td> 1 <img src="<?php echo constant('ROUTE_IMG') . $mineral . 'png';?>" /> =
                                15 <img src="<?php echo constant('ROUTE_IMG') . $key['item']. '.png';?>" /></td>
                            <td><?php echo $key['cost'];?><img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></td>
                            <td> <input type="number" min="0" />
                            <button> Smith </button></td>
                        </tr>
                     <?php endif;endforeach;
                     };?>
                <table id="iron">
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Ores required: </td>
                            <td> Cost: </td>
                            <td></td>
                        </tr>
                    </thead>
                    <?php generateTable($this->data['iron']);?>
                </table>
                <table id="steel">
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Ores required: </td>
                            <td> Cost: </td>
                            <td></td>
                        </tr>
                    </thead>
                    <?php generateTable($this->data['steel']);?>
                </table>
                <table id="gargonite">
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Ores required: </td>
                            <td> Cost: </td>
                            <td></td>
                        </tr>
                    </thead>
                    <?php generateTable($this->data['gargonite']);?>
                </table>
                <table id="adron">
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Ores required: </td>
                            <td> Cost: </td>
                            <td></td>
                        </tr>
                    </thead>
                    <?php generateTable($this->data['adron']);?>
                </table>
                <table id="yeqdon">
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Ores required: </td>
                            <td> Cost: </td>
                            <td></td>
                        </tr>
                    </thead>
                    <?php generateTable($this->data['yeqdon']);?>
                </table>
                <table id="frajrite">
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Ores required: </td>
                            <td> Cost: </td>
                            <td></td>
                        </tr>
                    </thead>
                    <?php generateTable($this->data['frajrite']);?>
                </table>
            </div>
            <div id="inventory">
                <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>    
            </div>
            <script src="<?php echo constant('ROUTE_JS') . 'smithy.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>