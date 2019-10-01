<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
        <?php include(constant('ROUTE_VIEW') . 'head.php');?>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php'); ?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <h3 class="page_title"> Smithy </h3>
            <div id="smith">
                <div id="minerals">
                    <img src="#" class="minerals" title="iron" onclick="showMineral('iron', this);" />
                    <img src="#" class="minerals" title="steel" onclick="showMineral('steel', this);" />
                    <img src="#" class="minerals" title="gargonite" onclick="showMineral('gargonite', this);" />
                    <img src="#" class="minerals" title="adron" onclick="showMineral('adron', this);" />
                    <img src="#" class="minerals" title="yedqon" onclick="showMineral('yeqdon', this);"/>
                    <img src="#" class="minerals" title="frajrite" onclick="showMineral('frajrite', this);"/>
                </div>
                <table id="iron">
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Required: </td>
                            <td> Cost: </td>
                        </tr>
                    </thead>
                    <tr>
                        <td> Bar </td>
                        <td> 1 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Sword </td>
                        <td> 2 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Spear </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Longsword </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Helm </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Platebody </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Platelegs </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Boots </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Shield </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                </table>
                <table id="steel">
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Required: </td>
                            <td> Cost: </td>
                        </tr>
                    </thead>
                    <tr>
                        <td> Bar </td>
                        <td> 1 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Sword </td>
                        <td> 2 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button > Make</button></td>
                    </tr>
                    <tr>
                        <td> Spear </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Longsword </td>
                        <td> 2 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Helm </td>
                        <td> 3 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Platebody </td>
                        <td> 5 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Platelegs </td>
                        <td> 5 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Boots </td>
                        <td> 2 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Shield </td>
                        <td> 4 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                </table>
                <table id="gargonite">
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Required: </td>
                            <td> Cost: </td>
                        </tr>
                    </thead>
                    <tr>
                        <td> Bar </td>
                        <td> 1 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Sword </td>
                        <td> 2 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Spear </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Longsword </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Helm </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Platebody </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Platelegs </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Boots </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Shield </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Gargonite arrows  </td>
                        <td> 1 <img title="Gargonite" src="<?php echo constant('ROUTE_IMG') . 'gargonite.jpg';?>" /> =
                            15 <img title="Gargonite" src="<?php echo constant('ROUTE_IMG') . 'gargonite.jpg';?>" /></td>
                        <td> <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.jpg';?>" /></td>
                        <td> <input type="number" min="0" />
                        <button> Smith </button></td>
                    </tr>
                </table>
                <table id="adron">
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Required: </td>
                            <td> Cost: </td>
                        </tr>
                    </thead>
                    <tr>
                        <td> Bar </td>
                        <td> 1 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Sword </td>
                        <td> 2 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Spear </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Longsword </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Helm </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Platebody </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Platelegs </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Boots </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Shield </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Adron arrows </td>
                        <td>  <img src="<?php echo constant('ROUTE_IMG') . 'adron bar.png';?>" class="item" /> =
                        15 <img src="<?php echo constant('ROUTE_IMG') . 'adron.jpg';?>" class="item" /></td>
                        <td>  <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.jpg';?>" /></td>
                        <td> <input type="number" min="0" />
                        <button> Smith </button></td>
                    </tr>
                </table>
                <table id="yeqdon">
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Required: </td>
                            <td> Cost: </td>
                        </tr>
                    </thead>
                    <tr>
                        <td> Bar </td>
                        <td> 1 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button > Make</button></td>
                    </tr>
                    <tr>
                        <td> Sword </td>
                        <td> 2 </td>
                        <td> 10 </td>
                        <td><input type="number" min="0" />
                            <button > Make</button></td>
                    </tr>
                    <tr>
                        <td> Spear </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button > Make</button></td>
                    </tr>
                    <tr>
                        <td> Longsword </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button > Make</button></td>
                    </tr>
                    <tr>
                        <td> Helm </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button > Make</button></td>
                    </tr>
                    <tr>
                        <td> Platebody </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button > Make</button></td>
                    </tr>
                    <tr>
                        <td> Platelegs </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button > Make</button></td>
                    </tr>
                    <tr>
                        <td> Boots </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Shield </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button > Make</button></td>
                    </tr>
                    <tr>
                        <td> Yeqdon arrows </td>
                        <td>  <img src="<?php echo constant('ROUTE_IMG') . 'yeqdon.jpg';?>" /> =
                        15 <img src="<?php echo constant('ROUTE_IMG') . 'yeqdon.jpg';?>" /></td>
                        <td>  <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.jpg';?>" /></td>
                        <td> <input type="number" min="0" />
                        <button> Smith </button></td>
                    </tr>
                </table>
                <table id="frajrite">
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Required: </td>
                            <td> Cost: </td>
                        </tr>
                    </thead>
                    <tr>
                        <td> Bar </td>
                        <td> 1 </td>
                        <td> 100 </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Sword </td>
                        <td> 2 </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Spear </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Longsword </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Helm </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Platebody </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Platelegs </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Boots </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Shield </td>
                        <td>  </td>
                        <td>  </td>
                        <td><input type="number" min="0" />
                            <button> Make</button></td>
                    </tr>
                    <tr>
                        <td> Frajrite arrows </td>
                        <td>  <img title="Frajrite" src="<?php echo constant('ROUTE_IMG') . 'frajrite.jpg';?>" /> =
                        15 <img title="Frajrite" src="<?php echo constant('ROUTE_IMG') . 'frajrite.jpg';?>" /></td>
                        <td>  <img class="gold" title="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.jpg';?>" /></td>
                        <td> <input type="number" min="0" />
                        <button> Smith </button></td>
                    </tr>
                </table>
            </div>
            <div id="inventory">
                <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>    
            </div>
            <script src="<?php echo constant('ROUTE_JS') . 'smithy.js';?>"></script>
            <script></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>