            adventures.css|adventures.js|
            <h3 class="page_title">Adventures</h3>
            <div id="current_adventure">
                <?php if(intval($this->data['adventure']['adventure_id']) === 0): ?>
                    <p> No current adventure! </p>
                    <p>Select "New Adventure" tab to create an new adventure or "join adventure" to join an existing one</p>
               <?php else: ?>
                    <div id="people">
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'farmer icon.png'; ?>" title="farmer"/>
                            <figcaption><?php echo ucfirst($this->data['adventure']['info']['farmer']);?></figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'miner icon.png'; ?>" title="miner"/>
                            <figcaption><?php echo ucfirst($this->data['adventure']['info']['miner']);?></figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'trader icon.png'; ?>" title="trader"/>
                            <figcaption><?php echo ucfirst($this->data['adventure']['info']['trader']);?></figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'warrior icon.png'; ?>" title="warrior"/>
                            <figcaption><?php echo ucfirst($this->data['adventure']['info']['warrior']);?></figcaption>
                        </figure>
                        <div id="status" class="mt-1">
                            <?php get_template('adventure_status', $this->data['adventure'], true);?>;
                        </div>
                        <div id="time"></div>
                        <?php if(in_array('none', array($this->data['adventure']['info']['farmer'],
                                 $this->data['adventure']['info']['miner'],
                                 $this->data['adventure']['info']['trader'],
                                 $this->data['adventure']['info']['warrior']))
                                 && $this->data['adventure']['info']['adventure_leader'] == $_SESSION['gamedata']['username']):?>
                            <div id="invite">
                                <?php if($this->data['adventure']['info']['other_invite'] == 1): ?>
                                <input type="text" min="0" onkeyup="chk_me();"/><span></span></br>
                 <button onclick="adventureRequest(<?php echo $this->data['adventure']['info']['adventure_id'];?>,'invite');">
                                Invite </button>
                                <?php elseif($this->data['adventure']['info']['adventure_leader'] == $_SESSION['gamedata']['username']): ?>
                                <div id="adventure-hire-citizen" class="mt-1 mb-1">
                                    <p>Hire citizen for role</p>
                                    <select name="adventure-citizen-role" id="adventure-citizen-role">
                                        <option selected disabled hidden></option>
                                        <option value="Farmer">Farmer</option>
                                        <option value="Miner">Miner</option>
                                        <option value="Trader">Trader</option>
                                        <option value="Warrior">Warrior</option>
                                    </select>
                                    <button id="hire-citizen"> Hire citizen</button>
                                </div>
                                <input type="text" min="0" onkeyup="chk_me();"/><span></span></br>
                 <button onclick="adventureRequest(<?php echo $this->data['adventure']['info']['adventure_id'];?>,'invite');">
                                Invite </button>
                                <?php endif;?>
                                <p> Leader invite only:
                                <?php echo ($this->data['adventure']['info']['other_invite'] == 0) ? 'on' : 'off';?>
                                </p>
                                <button> Toggle invite only</button>
                            </div>
                        <?php endif;?>
                        <button id="adventure-leave-event"> Leave adventure </button>
                    </div>
                    <div id="report">
                        <p id="time"></p>
                        <button id="adventure-get-report-event">
                            Get adventure <br>
                            report
                        </button>
                    </div>
                    <div id="requirements">
                        <table>
                            <thead>
                                <tr>
                                    <td> Role: </td>
                                    <td> Requirement: </td>
                                    <td> Provided: </td>
                                </tr>
                            </thead>
                            <?php get_template('requirements', $this->data['adventure']['requirements'], true); ?>
                        </table>
                    </div>
                    <div id="provide">
                        <?php if($this->data['adventure']['info']['adventure_status'] == 0):
                                switch($_SESSION['gamedata']['profiency']):
                                case 'warrior': ?>
                                <p>Selected warriors: <span id="selected_warrior_amount">0</span></p>
                                <?php get_template('warrior_select', $this->data['adventure']['warriors'], true);?>
                              <button class="adventure-provide-button">Provide</button>
                          <?php break; ?>
                          <?php
                              case 'miner':
                              case 'farmer':?>
                              <div id="item">
                                  <div id="selected"></div>
                              </div>
                              <label for="quantity"> Select how many </label>
                              <input id="selected_amount" name="quantity" type="number" min="0"/>
                              <button class="adventure-provide-button">Provide</button>
                          <?php break; ?>
                          <?php endswitch;?>
                        <?php endif;?>
                        <p>Once everyone has provided the adventure will start automatically</p>
                    </div>
                <?php endif; ?>
            </div>
            <div id="requests">
                <?php get_template('adventure_requests', 
                    array("requests" => $this->data['adventure']['requests'], "invites" => $this->data['adventure']['invites']), 
                    true, array("site" => "adventures"));?>
            </div>
            <div id="new_adventure">
                <p>Please note that the current profiency will be your role in the new adventure. 
                    Head to citycentre to change profiency</p>
                <form id="new_adventure_form" class="mt-1">
                    <label for="difficulty"> Select difficulty: </label>
                    <select name="difficulty" onchange="checkLevel();" id="diff_select" required>
                        <option selected disabled hidden>  </option>
                        <option value="easy"> Easy </option>
                        <option value="medium"> Medium </option>
                        <option value="hard"> Hard </option>
                    </select>
                    <label for="location"> Select location: </label>
                    <select name="location" required>
                        <option selected disabled hidden>  </option>
                        <option value="hirtam"> Hirtam </option>
                        <option value="pvitul"> Pvitul </option>
                        <option value="khanz"> Khanz </option>
                        <option value="ter"> Ter </option>
                        <option value="fansal plains"> Fansal plains </option>
                    </select>
                    <label for="other_invite"> Can other players invite? </label>
                    <input type="checkbox" name="invite_only"/></br>
                    <p> Note that certain adventures demand that the trader has a certain diplomacy relation! </p>
                    <button type="button" id="adventure-start-event"name="start_adventure"> Go on adventure </button>
                </form>
                <?php if(!empty($this->error['adventureErr'])):?>
                    <script>alert('Finish your adventure before taking a new one!');</script>
                <?php endif; ?>
            </div>      
            <div id="join_adventure">
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
                    <?php if(count($this->data['adventure']['join_adventures']) == 0): ?>
                        <tr>
                            <td colspan="7"> No adventures to join </td>
                        </tr>
                    <?php endif;?>
                    <?php foreach($this->data['adventure']['join_adventures'] as $key): ?>
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