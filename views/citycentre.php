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
            <h3 class="page_title"> City Centre </h3>
            <button onclick="show('profiency');"> Profiency </button>
            <button onclick="show('keep');"> Keep </button>
            <button onclick="show('permits');"> Permits</button>
            <div id="profiency">
                Profiency
                <p> Current profiency: <span id="profiency"><?php echo $_SESSION['gamedata']['profiency'];?></span></p>
                    <label for="profiency_select"> Change profiency</label></br>
                    <select name="profiency_select" id="profiency_select">
                        <option selected="selected"></option>
                        <?php
                        $profiences = array('farmer' => 'farmer', 'miner' => 'miner', 'trader' => 'trader',
                                            'warrior' => 'warrior');
                        unset($profiences[$_SESSION['gamedata']['profiency']]);
                        foreach($profiences as $values):
                        ?>
                        <option><?php echo ucfirst($values); ?></option>
                        <?php endforeach;?>
                    </select>
                    <p> The cost of changing profiency is charged 500
                    <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.jpg';?>" /></p>
                    <button onclick="changeProfiency();"> Change profiency</button> 
            </div>
            <div id="keep">
                <div id="artefact">
                    <img src="#" />
                    <p> Current artefact: <?php echo $this->data['artefact_data']['artefact'];?></p>
                    <p> Uses left: <?php echo $this->data['artefact_data']['uses']; ?></p>
                </div>
                <div id="selected">
                    
                </div>
                <button onclick="changeArtefact();"> Change artefact </button>
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
                        <td><button onclick="buyPermits(50);"> Buy </button></td>
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
            <div id="inventory">
                <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>
            </div>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . 'aside.php');?>
        </aside>
    </body>
    <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
</html>
