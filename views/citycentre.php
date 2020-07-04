            citycentre.css|citycentre.js|
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
                    <p> Current artefact: <?php echo ucfirst($data['artefact_data']['artefact']);?>
                    <?php if($data['artefact_data']['artefact'] === 'none'): ?>
                        <img style="background-color:white;" />
                    <?php else: ?>
                        <img src="<?php echo constant('ROUTE_IMG') . $data['artefact_data']['artefact'] . '.png';?>" />
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
                            <td></td>
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
                        <td><?php echo $data['effiency']['farmer'];?></td>
                        <td><?php echo $data['effiency']['farmer'] * 150;?></td>
                        <td><button> Upgrade </button></td>
                    </tr>
                    <tr>
                        <td> Miner </td>
                        <td><?php echo $data['effiency']['miner'];?></td>
                        <td><?php echo $data['effiency']['miner'] * 150;?></td>
                        <td><button> Upgrade </button></td>
                    </tr>
                </table>
            </div>