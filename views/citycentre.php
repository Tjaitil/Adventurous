<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php');?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <h3 class="page_title"> City Centre </h3>
            <button onclick="show('profiency');"> Profiency </button>
            <button onclick="show('keep');"> Keep </button>
            <button onclick="show('permits');"> Permits</button>
            <div id="profiency">
                <p> Current profiency: <span id="profiency"><?php echo $_SESSION['gamedata']['profiency'];?></span></p>
                    <label for="profiency_select"> Change profiency</label></br>
                    <select name="profiency_select" id="profiency_select">
                        <option selected="selected"></option>
                        <?php
                        $profiences = array('farmer' => 'farmer', 'miner' => 'miner', 'trader' => 'trader',
                                            'warrior' => 'warrior');
                        unset($profiences[$_SESSION['gamedata']['profiency']]);
                        foreach($profiences as $values):?>
                        <option><?php echo ucfirst($values);?></option>
                        <?php endforeach;?>
                    </select>
                    <p> The cost of changing profiency is charged 500
                    <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.jpg';?>" /></p>
                    <button> Change profiency</button> 
            </div>
            <div id="keep">
                <div id="artefact">
                    <p> Current artefact: <?php echo ucfirst($this->data['artefact_data']['artefact']);?>
                    <?php if($this->data['artefact_data']['artefact'] === 'none'): ?>
                        <img style="background-color:white;" />
                    <?php else: ?>
                        <img src="<?php echo constant('ROUTE_IMG') . $this->data['artefact_data']['artefact'] . '.png';?>" />
                    <?php endif;?>
                    </p>
                </div>
                <div id="selected">
                    
                </div>
                <button> Change artefact </button>
                <button> Create new artefact </button>
            </div>
            <div id="permits">
                <table>
                    <caption> Buy Permits: </caption>
                    <thead>
                        <tr>
                            <td> Amount: </td>
                            <td> Cost: </td>
                        </tr>
                    </thead>
                    <tr>
                        <td> 50 </td>
                        <td> 100 <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.jpg';?>"/></td>
                        <td><button> Buy </button></td>
                    </tr>
                </table>
                <table>
                    <thead>
                        <tr>
                            <td> Item: </td>
                            <td> Cost: </td>
                            <td><button> Buy </button></td>
                        </tr>
                    </thead>
                    <tr>
                        <td> Frajrite items </td>
                        <td> </td>
                        <td><button> Buy </button></td>
                    </tr>
                    <tr>
                        <td> Wujkin items </td>
                        <td>  </td>
                        <td><button> Buy </button></td>
                    </tr>
                </table>
            </div>
            <div id="efficiency">
                <table>
                    <thead>
                        <tr>
                            <td> Profiency </td>
                            <td> Effiency level </td>
                            <td> Cost </td>
                            <td></td>
                        </tr>
                    </thead>
                    <tr>
                        <td> Farmer </td>
                        <td><?php echo $this->data['effiency']['farmer'];?></td>
                        <td><?php echo $this->data['effiency']['farmer'] * 150;?></td>
                        <td><button> Upgrade </button></td>
                    </tr>
                    <tr>
                        <td> Miner </td>
                        <td><?php echo $this->data['effiency']['miner'];?></td>
                        <td><?php echo $this->data['effiency']['miner'] * 150;?></td>
                        <td><button> Upgrade </button></td>
                    </tr>
                </table>
            </div>
            <div id="inventory">
                <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>
            </div>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') .'selectitem.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . 'aside.php');?>
        </aside>
    </body>
</html>
