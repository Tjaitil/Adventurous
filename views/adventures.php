
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <h3 class="page_title">Adventures</h3>
             <div id="requests">
                <table>
                    <thead>
                        <tr>
                            <td> From: </td>
                            <td> To: </td>
                            <td> Method: </td>
                            <td> Role: </td>
                            <td> Date: </td>
                            <td> </td>
                        </tr>
                    </thead>
                    <?php if(count($this->data['requests']) == 0 || $this->data['requests'] == false):?>
                        <tr>
                            <td colspan="6" align="center"> No invites at the moment!</td>
                        </tr>
                    <?php endif;?>
                    <?php foreach($this->data['requests'] as $key): ?>
                    <tr>
                        <td><?php echo ucfirst($key['sender']);?></td>
                        <td><?php echo ucfirst($key['receiver']);?></td>
                        <td><?php echo $key['method'];?></td>
                        <td><?php echo $key['role'];?></td>
                        <td><?php echo $key['request_date'];?></td>
                        <td>
                            <button onclick="showAdventure(<?php echo $key['adventure_id'];?>);"> Show Info </button>
                            <button onclick="joinAdventure(<?php echo $key['request_id'];?>);"> Accept </button>
                        </td>
                    </tr>
                    <?php endforeach;?>
                    <tfoot>
                        <tr>
                            <td colspan="6"><button class="previous"> < Prev </button><button class="next"> Next > </button></td>
                        </tr> 
                    </tfoot>
                </table>
            </div>
            <div id="show_adventure">
                <table>
                    <thead>
                        <tr>
                            <td> Difficulty: </td>
                            <td> Location: </td>
                            <td> Farmer </td>
                            <td> Miner: </td>
                            <td> Trader: </td>
                            <td> Warrior: </td>
                        </tr>
                    </thead>
                    <tr>
                        <td>  </td>
                        <td>  </td>
                        <td>  </td>
                        <td>  </td>
                        <td>  </td>
                        <td>  </td>
                    </tr>
                </table>
            </div>
            <div id="tabs">
                <button onclick="show('current_adventure');"> Current Adventure: </button>
                <button onclick="show('pending_adventure');"> Pending Adventure: </button>
                <button onclick="show('new_adventure');"> New Adventure: </button>
            </div>
            <div id="new_adventure">
                <form method="post" action="/adventures">
                    <label for="difficulty"> Select difficulty: </label>
                    <select name="difficulty" onchange="checkLevel();" id="diff_select">
                        <option>  </option>
                        <option value="easy"> Easy </option>
                        <option value="medium"> Medium </option>
                        <option value="hard"> Hard </option>
                    </select></br>
                    <label for="location"> Select location: </label>
                    <select name="location">
                        <option value="">  </option>
                        <option value="hirtam"> Hirtam </option>
                        <option value="pvitul"> Pvitul </option>
                        <option value="khanz"> Khanz </option>
                        <option value="ter"> Ter </option>
                        <option value="fansal plains"> Fansal plains </option>
                    </select></br>
                    <label for="other_invite"> Can other players invite? </label>
                    <input type="checkbox" name="invite_only"/></br>
                    <p> Note that certain adventures demand that the trader has a certain diplomacy relation! </p>
                    <button> Go on adventure </button>
                </form>
                <?php if(!empty($this->error['adventureErr'])):?>
                    <script>alert('Finish your adventure before taking a new one!');</script>
                <?php endif; ?>
            </div>      
            <div id="current_adventure">
                <?php if($this->data['current_adventure']['current'] == 0): ?>
                    <span> No current adventure! </span>
               <?php else: ?>
                    <div id="people">
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'farmer icon.png'; ?>" title="farmer"/>
                            <figcaption><?php echo ucfirst($this->data['current_adventure']['info']['farmer']);?></figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'miner icon.png'; ?>" title="miner"/>
                            <figcaption><?php echo ucfirst($this->data['current_adventure']['info']['miner']);?></figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'trader icon.png'; ?>" title="trader"/>
                            <figcaption><?php echo ucfirst($this->data['current_adventure']['info']['trader']);?></figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'warrior icon.png'; ?>" title="warrior"/>
                            <figcaption><?php echo ucfirst($this->data['current_adventure']['info']['warrior']);?></figcaption>
                        </figure>
                        <div id="status">
                        <?php
                            $test1 = in_array('none', array($this->data['current_adventure']['info']['farmer'],
                                 $this->data['current_adventure']['info']['miner'],
                                 $this->data['current_adventure']['info']['trader'],
                                 $this->data['current_adventure']['info']['warrior']));
                            $test2 = in_array(0, $this->data['current_adventure']['requirements']);
                            $test3 = $this->data['current_adventure']['info']['adventure_status'] == 0;
                            if($test1 == true && $test3 !== false): ?>
                            <p> Adventure status: more players needed </p>
                            <?php endif;?>
                            <?php if($test2 == true && $test3 == true): ?>
                            <p> Adventure status: awaiting providing </p>
                            <?php elseif($test1 == false && $test3 == true): ?>
                            <p> Adventure status: ready to start! </p>
                            <?php endif;?>
                            <?php if($test1 != true && $test2 != true && $test1 != true): ?>
                            <p> Adventure status: underway! </p>
                            <?php endif;?>
                        </div>
                        <div id="time"></div>
                        <?php if(in_array('none', array($this->data['current_adventure']['info']['farmer'],
                                 $this->data['current_adventure']['info']['miner'],
                                 $this->data['current_adventure']['info']['trader'],
                                 $this->data['current_adventure']['info']['warrior']))
                                 && $this->data['current_adventure']['info']['adventure_leader'] == $this->data['username']):?>
                            <div id="invite">
                                <?php if($this->data['current_adventure']['info']['other_invite'] == 1): ?>
                                <input type="text" min="0" onkeyup="chk_me();"/><span></span></br>
                 <button onclick="adventureRequest(<?php echo $this->data['current_adventure']['info']['adventure_id'];?>,'invite');">
                                Invite </button>
                                <?php elseif($this->data['current_adventure']['info']['adventure_leader'] == $this->data['username']): ?>
                                <input type="text" min="0" onkeyup="chk_me();"/><span></span></br>
                 <button onclick="adventureRequest(<?php echo $this->data['current_adventure']['info']['adventure_id'];?>,'invite');">
                                Invite </button>
                                <?php endif;?>
                                <p> Leader invite only:
                                <?php echo ($this->data['current_adventure']['info']['other_invite'] == 0) ? 'on' : 'off';?>
                                </p>
                                <button> Toggle invite only</button>
                            </div>
                        <?php endif;?>
                        <?php if($this->data['current_adventure']['info']['adventure_leader'] ==
                                 $this->data['username'] &&
                                 in_array(0, $this->data['current_adventure']['requirements']) == false): ?>
                            <button onclick="startAdventure();" id="adv_start"> Start Adventure </button>
                        <?php endif;?>
                        <button name="leave_adventure"> Leave adventure </button>
                    </div>
                    <div id="report"></div>
                    <div id="requirements">
                        <table>
                            <thead>
                                <tr>
                                    <td> Role: </td>
                                    <td> Requirement: </td>
                                    <td> Provided: </td>
                                </tr>
                            </thead>
                            <?php get_template('requirements', $this->data['current_adventure']['requirements']); ?>
                        </table>
                    </div>
                    <div id="provide">
                        <?php if($this->data['current_adventure']['info']['adventure_status'] == 0):
                              switch($this->data['profiency']):
                              case 'warrior': ?>
                                <?php get_template('warrior_select', $this->data['current_adventure']['warriors']);?>
                            <button> Provide </button>
                        <?php break; ?>
                        <?php
                            case 'miner':
                            case 'farmer':?>
                            <div id="item">
                                <div id="selected"></div>
                            </div>
                            <label for="quantity"> Select how many </label>
                            <input id="quantity" name="quantity" type="number" min="0"/>
                            <button> Provide </button>
                    </div>
                    <div id="inventory">
                        <?php require(constant("ROUTE_VIEW") . "inventory.php"); url();?>
                    </div>
                    <?php break; ?>
                    <?php endswitch;?>
                    <?php endif;?>
                    
                <?php endif; ?>
            </div>
            <div id="pending_adventure">
                <p> Join one of this adventures: </p>
                <table>
                    <thead>
                        <tr>
                            <td> Difficulty: </td>
                            <td> Location: </td>
                            <td> Farmer </td>
                            <td> Miner: </td>
                            <td> Trader: </td>
                            <td> Warrior: </td>
                            <td></td>
                        </tr>
                    </thead>
                    <?php if(count($this->data['pending_adventures']) == 0): ?>
                        <tr>
                            <td colspan="7"> No adventures to join </td>
                        </tr>
                    <?php endif;?>
                    <?php foreach($this->data['pending_adventures'] as $key): ?>
                    <tr>
                        <td><?php echo $key['difficulty']; ?></td>
                        <td><?php echo $key['location']; ?></td>
                        <td><?php echo $key['farmer']; ?></td>
                        <td><?php echo $key['miner']; ?></td>
                        <td><?php echo $key['trader']; ?></td>
                        <td><?php echo $key['warrior']; ?></td>
                        <td><button onclick="adventureRequest(<?php echo $key['adventure_id']; ?>, 'request');"> Ask to join </button></td>
                    </tr>
                    <?php endforeach; ?>
                    <tfoot>
                        <tr>
                            <td colspan="7"><button class="previous"> < Prev </button><button class="next"> Next > </button></td>
                        </tr> 
                    </tfoot>
                </table>
            </div>