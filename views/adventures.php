<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="public/css/<?php echo $title ?>.css" />
        <?php include('views/head.php');?>
    </head>
    <body>
        <header>
            <?php require('views/header.php'); ?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
             <div id="requests">
                <table>
                    <thead>
                        <tr>
                            <td> From: </td>
                            <td> To: </td>
                            <td> Where: </td>
                            <td> Role: </td>
                        </tr>
                    </thead>
                    <?php foreach($this->data['requests'] as $key): ?>
                        <?php if(empty($this->data['requests']) || $this->data['requests'] == false):?>
                            <td colspan="5" align="center"> No invites at the moment!</td>
                        <?php endif;?>
                        <tr>
                            <td><?php echo $key['sender'];?></td>
                            <td><?php echo $key['receiver'];?></td>
                            <td><?php echo $key['role'];?></td>
                            <td><?php echo $key['method'];?></td>
                            <td><button onclick="showAdventure(<?php echo $key['adventure_id'];?>);"> Show Info </button>
                                <button onclick="joinAdventure(<?php echo $key['request_id'];?>);"> Accept </button>
                            </td>
                        </tr>
                    <?php endforeach;?>
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
                    <select name="difficulty">
                        <option>  </option>
                        <option value="easy"> Easy </option>
                        <option value="medium"> Medium </option>
                        <option value="hard"> Hard </option>
                    </select><span class="error"><?php echo $this->error['difficultyErr'];?></span></br>
                    <label for="location"> Select location: </label>
                    <select name="location">
                        <option value="">  </option>
                        <option value="hirtam"> Hirtam </option>
                        <option value="pvitul"> Pvitul </option>
                        <option value="khanz"> Khanz </option>
                        <option value="ter"> Ter </option>
                        <option value="fansal plains"> Fansal plains </option>
                    </select><span class="error"><?php echo $this->error['locationErr'];?></span>
                    <p> Note that certain adventures demand that the trader has a certain diplomacy relation! </p>
                    <button> Go on adventure </button>
                </form>
                <?php if(!empty($this->error['adventureErr'])):?>
                    <script>alert('Finish your adventure before taking a new one!');</script>
                <?php endif; ?>
            </div>
            
            <div id="current_adventure">
                <?php if($this->data['current_adventure']['current'] == 0) {
                    echo "None";
                }
                if($this->data['current_adventure']['current'] != 0):?>
                    <div id="people">
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'gold.jpg'; ?>" title="farmer"/>
                            <figcaption><?php echo $this->data['current_adventure']['info']['farmer'];?></figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'gold.jpg'; ?>" title="miner"/>
                            <figcaption><?php echo $this->data['current_adventure']['info']['miner'];?></figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'gold.jpg'; ?>" title="trader"/>
                            <figcaption><?php echo $this->data['current_adventure']['info']['trader'];?></figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'gold.jpg'; ?>" title="warrior"/>
                            <figcaption><?php echo $this->data['current_adventure']['info']['warrior'];?></figcaption>
                        </figure>
                        <?php if(in_array('none', array($this->data['current_adventure']['farmer'],
                                 $this->data['current_adventure']['miner'],
                                 $this->data['current_adventure']['trader'],
                                 $this->data['current_adventure']['warrior']))):?>
                            <div id="invite">
                                <input type="text" min="0" onkeyup="chk_me()";/><span></span></br>
                 <button onclick="adventureRequest(<?php echo $this->data['current_adventure']['info']['adventure_id'];?>,'invite');">
                                Invite </button>
                            </div>
                        
                        <?php endif; ?>
                        <?php if($this->data['current_adventure']['info']['adventure_leader'] ==
                                 $this->data['current_adventure']['username'] &&
                                 $this->data['current_adventure']['info']['adventure_status'] == 0): ?>
                            <button onclick="startAdventure()"> Start Adventure </button>
                        <?php endif;?>
                        
                    </div>
                    <div id="time"> None </div>
                    <div id="report"></div>
                    <div id="requirements">
                        <table>
                            <thead>
                                <tr>
                                    <td> Role: </td>
                                    <td> Status: </td>
                                    <td> Required: </td>
                                    <td> Provided: </td>
                                    <td> Contribution left: </td>
                                </tr>
                            </thead>
                            <tr>
                                <td> Farmer:</td>
                                <td><?php echo $this->data['current_adventure']['farmer']['status']; ?></td>
                                <td><?php echo $this->data['current_adventure']['requirements'][0]['required']; ?></td>
                                <td><?php echo $this->data['current_adventure']['farmer']['provided']; ?></td>
                                <td><?php echo $this->data['current_adventure']['farmer']['missing_contribution']; ?></td>
                            </tr>
                            <tr>
                                <td> Miner:</td>
                                <td><?php echo $this->data['current_adventure']['miner']['status']; ?></td>
                                <td><?php echo $this->data['current_adventure']['requirements'][1]['required']; ?></td>
                                <td><?php echo $this->data['current_adventure']['miner']['provided']; ?></td>
                                <td><?php echo $this->data['current_adventure']['miner']['missing_contribution'] ?></td>
                            </tr>
                            <tr>
                                <td> Trader:</td>
                                <td><?php echo $this->data['current_adventure']['trader']['status']; ?></td>
                                <td><?php echo $this->data['current_adventure']['requirements'][3]['required']; ?></td>
                                <td> - </td>
                                <td><?php echo $this->data['current_adventure']['trader']['missing_contribution'] ?></td>
                            </tr>
                            <tr>
                                <td> Warrior:</td>
                                <td><?php echo $this->data['current_adventure']['warrior']['status']; ?></td>
                                <td><?php echo $this->data['current_adventure']['requirements'][2]['required']; ?></td>
                                <td><?php echo $this->data['current_adventure']['warrior']['provided']; ?></td>
                                <td><?php echo $this->data['current_adventure']['warrior']['missing_contribution']; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div id="provide">
                        <div id="item">
                            <div id="selected"></div>
                        </div>
                    <?php if($this->data['current_adventure']['info']['adventure_status'] == 0):
                          switch($this->data['profiency']):
                          case 'warrior': ?>
               <button onclick="provide(<?php echo $this->data['current_adventure']
               ['info']['adventure_id'];?>, 'warrior')"> Provide </button>
                    <?php foreach($this->data['current_adventure']['warriors'] as $key): ?>
                        <div class="warriors" id="warrior_<?php echo $key['warrior_id'];?>">
                        <figure>
                            <img src="<?php echo constant('ROUTE_IMG') . $key['type'] . '.jpg'?>" />
                            <figcaption><?php
                            echo "Warrior: " , $key['warrior_id'] , '</br>';
                            $string = '%s level: %b </br>';
                            echo sprintf($string, 'Stamina', $key['stamina_level']);
                            echo sprintf($string, 'Technique', $key['technique_level']);
                            echo sprintf($string, 'Precision', $key['precision_level']);
                            echo sprintf($string, 'Strength', $key['strength_level']);
                            ?></figcaption>
                        </figure>
                        <input type="checkbox" onclick="check(this)" />
                        </div>
                    <?php endforeach;?>
                    <?php break; ?>
                    <?php
                        case 'miner':
                        case 'farmer':?>
                        <label for="quantity"> Select how many </label>
                        <input id="quantity" name="quantity" type="number" min="0"/>
                <button onclick="provide(<?php echo $this->data['current_adventure']
                ['info']['adventure_id'];?>, 'item')"> Provide </button>
                        <?php require(constant("ROUTE_VIEW") . "inventory.php"); url();?>
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
                        </tr>
                    </thead>
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
                </table>
            </div>
            <script src="<?php echo constant('ROUTE_JS') . 'selectitem.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require('views/aside.php'); ?>
        </aside>
    </body>
</html>
