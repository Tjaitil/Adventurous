              <div id="content_wrapper">
                <div id="adventurer">
                    <table class="highscores">
                        <caption> Adventurer <img src="<?php echo constant('ROUTE_IMG') . 'adventurer icon.png';?>"/></caption>
                        <thead>
                            <tr>
                                <td> Name </td>
                                <td> Respect </td>
                            </tr>
                        </thead>
                        <?php foreach($this->data['adventurer_highscores'] as $key): ?>
                        <tr>
                            <td><?php echo ucfirst($key['username']); ?></td>
                            <td><?php echo $key['adventurer_respect']; ?></td>
                        </tr>
                        <?php endforeach;?>
                        <tfoot>
                            <tr>
                                <td colspan="5"><button class="previous" disabled> < Prev </button>
                                <button class="next" disabled> Next > </button></td>
                            </tr> 
                        </tfoot>
                    </table>
                </div>
                <div id="farmer">
                    <table class="highscores">
                        <caption> Farmer <img src="<?php echo constant('ROUTE_IMG') . 'farmer icon.png';?>"/></caption>
                        <thead>
                            <tr>
                                <td> Name </td>
                                <td> Level </td>
                                <td> Experience </td>
                            </tr>
                        </thead>
                        <?php for($i = 0; $i < 10; $i++): ?>
                            <?php if(empty($this->data['farmer_highscores'][$i])): ?>
                                <tr>
                                    <td> - </td>
                                    <td> - </td>
                                    <td> - </td>
                                </tr>
                            <?php else: ?>  
                        <tr>
                            <td><?php echo ucfirst($key['username']); ?></td>
                            <td><?php echo $this->data['farmer_highscores'][$i]['farmer_level']; ?></td>
                            <td><?php echo $this->data['farmer_highscores'][$i]['farmer_xp']; ?></td>
                        </tr>
                        <?php endif; endfor;?>
                        <tfoot>
                            <tr>
                                <td colspan="5"><button class="previous"> < Prev </button><button class="next"> Next > </button></td>
                            </tr> 
                        </tfoot>
                    </table>
                </div>
                <div id="miner">
                    <table class="highscores">
                        <caption> Miner <img src="<?php echo constant('ROUTE_IMG') . 'miner icon.png';?>"></caption>
                        <thead>
                            <tr>
                                <td> Name </td>
                                <td> Level </td>
                                <td> Experience </td>
                            </tr>
                        </thead>
                        <?php for($i = 0; $i < 10; $i++): ?>
                            <?php if(empty($this->data['miner_highscores'][$i])): ?>
                                <tr>
                                    <td> - </td>
                                    <td> - </td>
                                    <td> - </td>
                                </tr>
                            <?php else: ?>  
                                <tr>
                                    <td><?php echo ucfirst($this->data['miner_highscores'][$i]['username']);?></td>
                                    <td><?php echo $this->data['miner_highscores'][$i]['miner_level'];?></td>
                                    <td><?php echo $this->data['miner_highscores'][$i]['miner_xp'];?></td>
                                </tr>
                            <?php endif;
                        endfor;?>
                        <tfoot>
                            <tr>
                                <td colspan="5"><button class="previous" disabled> < Prev </button>
                                <button class="next" disabled> Next > </button></td>
                            </tr> 
                        </tfoot>
                    </table>
                </div>
                <div id="trader">
                    <table class="highscores">
                        <caption> Trader <img src="<?php echo constant('ROUTE_IMG') . 'trader icon.png';?>"/></caption>
                        <thead>
                            <tr>
                                <td> Name </td>
                                <td> Level </td>
                                <td> Experience </td>
                            </tr>
                        </thead>
                        <?php for($i = 0; $i < 10; $i++): ?>
                            <?php if(empty($this->data['trader_highscores'][$i])): ?>
                                <tr>
                                    <td> - </td>
                                    <td> - </td>
                                    <td> - </td>
                                </tr>
                            <?php else: ?>  
                        <tr>
                            <td><?php echo ucfirst($key['username']); ?></td>
                            <td><?php echo $this->data['trader_highscores'][$i]['trader_level']; ?></td>
                            <td><?php echo $this->data['trader_highscores'][$i]['trader_xp']; ?></td>
                        </tr>
                        <?php endif; endfor;?>
                        <tfoot>
                            <tr>
                                <td colspan="5"><button class="previous" disabled> < Prev </button>
                                <button class="next" disabled> Next > </button></td>
                            </tr> 
                        </tfoot>
                    </table>
                </div>
                <div id="warrior">
                    <table class="highscores">
                        <caption> Warrior <img src="<?php echo constant('ROUTE_IMG') . 'warrior icon.png';?>"/></caption>
                        <thead>
                            <tr>
                                <td> Name </td>
                                <td> Level </td>
                                <td> Experience </td>
                            </tr>
                        </thead>
                        <?php for($i = 0; $i < 10; $i++): ?>
                            <?php if(empty($this->data['warrior_highscores'][$i])): ?>
                                <tr>
                                    <td> - </td>
                                    <td> - </td>
                                    <td> - </td>
                                </tr>
                            <?php else: ?>  
                        <tr>
                            <td><?php echo ucfirst($key['username']); ?></td>
                            <td><?php echo $this->data['warrior_highscores'][$i]['warrior_level']; ?></td>
                            <td><?php echo $this->data['warrior_highscores'][$i]['warrior_xp']; ?></td>
                        </tr>
                        <?php endif; endfor;?>
                        <tfoot>
                            <tr>
                                <td colspan="5"><button class="previous" disabled> < Prev </button>
                                <button class="next" disabled> Next > </button></td>
                            </tr> 
                        </tfoot>
                    </table>
                </div>
                <div id="total">
                    <table class="highscores">
                        <caption> Total skill levels </caption>
                        <thead>
                            <tr>
                                <td> Name </td>
                                <td> Level </td>
                                <td> Experience </td>
                            </tr>
                        </thead>
                        <?php for($i = 0; $i < 10; $i++): ?>
                            <?php if(empty($this->data['total_highscores'][$i])): ?>
                                <tr>
                                    <td> - </td>
                                    <td> - </td>
                                    <td> - </td>
                                </tr>
                            <?php else: ?>  
                        <tr>
                            <td><?php echo ucfirst($key['username']); ?></td>
                            <td><?php echo $this->data['total_highscores'][$i]['total_level']; ?></td>
                            <td><?php echo $this->data['total_highscores'][$i]['total_xp']; ?></td>
                        </tr>
                        <?php endif; endfor;?>
                        <tfoot>
                            <tr>
                                <td colspan="5"><button class="previous" disabled> < Prev </button>
                                <button class="next" disabled> Next > </button></td>
                            </tr> 
                        </tfoot>
                    </table>
                </div>
              </div>