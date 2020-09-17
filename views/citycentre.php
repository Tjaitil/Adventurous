            citycentre.css|citycentre.js|
            <h3 class="page_title"> City Centre </h3>
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
                    <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></p>
                    <button> Change profiency</button> 
            </div>
            <div id="keep">
                <div id="artefact">
                    <p> Current artefact: <?php echo ucfirst($this->data['artefact_data']['artefact']);?>
                    <?php if($this->data['artefact_data']['artefact'] === 'none'): ?>
                        <img style="background-color:white;" />
                    <?php else: ?>
                        <img src="<?php echo constant('ROUTE_IMG') . trim(explode("(", $this->data['artefact_data']['artefact'])[0])
                                  . '.png';?>" />
                    <?php endif;?>
                    </p>
                </div>
                <div id="selected">
                    
                </div>
                <button> Change artefact </button>
                <button> Create new artefact </button>
            </div>
            <div id="permits">
                <p>Current permits: <?php echo $this->data['permits'];?></p>
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
                        <td> 50 <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>"/></td>
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
                        <td><?php echo $this->data['effiency']['farmer'] * 150;?>
                        <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>"/></td>
                        <td><button> Upgrade </button></td>
                    </tr>
                    <tr>
                        <td> Miner </td>
                        <td><?php echo $this->data['effiency']['miner'];?></td>
                        <td><?php echo $this->data['effiency']['miner'] * 150;?>
                        <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>"/></td>
                        <td><button> Upgrade </button></td>
                    </tr>
                </table>
            </div>