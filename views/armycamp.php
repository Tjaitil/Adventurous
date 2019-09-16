<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
        <?php include(constant('ROUTE_VIEW') . 'head.php');?>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php');?>
        </header>
        <?php if(count($this->data['warrior_level_up']) > 0): ?>
            <?php $_SESSION['gamedata']['level_up_data'] = $this->data['warrior_level_up'];?>
                <script src="<?php echo constant("ROUTE_JS") . 'warriorLevelUp.js'?>"></script>
            <?php endif?>
        <div id="announcement"><button onclick="exit();">Exit</button></div>
        <section>
            <?php require(constant("ROUTE_VIEW") . 'layout.php');?>
            
            <a href="/army-missions"> Missions</a>
            <a href="/armory"> Armory </a>
            <button type="button" onclick="show('overview');"> Training Overview </button>
            <button type="button" onclick="show('calculator');"> Combat Calculator </button>
            </a>
            <?php print_r($this->error);?>
            <div id="calculator">
                <div id="calc_form">
                    <form>
                        <input type="radio" name="stats_type" value="individ" onclick="toggle(2, 'individ')" /> Specify individually stats </br>
                        <input type="radio" name="stats_type" value="group" onclick="toggle(2, 'group')" checked /> Specify groups stats </br>
                        <label for="melee_amount"> Select amount of melee warrior(s): </label>
                        <input name="melee_amount" onchange="statsInput();" type="number" min="0" value="1" /></br>
                        <label for="ranged_amount"> Select amount of ranged warrior(s): </label>
                        <input name="ranged_amount" type="number" onchange="statsInput();" min="0" value="1" /></br>
                        <div id="stats_individ">
                            
                        </div>
                        <div id="stats_group">
                            <label for="stamina_level"> Stamina level: </label>
                            <input name="stamina_level" type="number" min="0" value="1" /></br>
                            <label for="technique_level"> Technique level: </label>
                            <input name="technique_level" type="number" min="0" value="1" /></br>
                            <label for="precision_level"> Precision level: </label>
                            <input name="precision_level" type="number" min="0" value="1" /></br>
                            <label for="strength_level"> Strength level: </label>
                            <input name="strength_level" type="number" min="0" value="1" /></br>
                            <label for="melee_attack"> Melee attack: </label>
                            <input name="melee_attack" type="number" min="10" value="10" /></br>
                            <label for="melee_defence"> Melee defence: </label>
                            <input name="melee_defence" type="number" min="10" value="10" /></br>
                            <label for="ranged_attack"> Ranged attack: </label>
                            <input name="ranged_attack" type="number" min="10" value="10" /></br>
                            <label for="ranged_defence"> Ranged defence: </label>
                            <input name="ranged_defence" type="number" min="10" value="10" /></br>
                        </div>                        
                        <label for="daqloon_amount"> Select amount of daqloon(s): </label>
                        <input name="daqloon_amount" type="number" min="2" value="2" /></br>
                        <label for="daqloon_attack"> Daqloon defence: </label>
                        <input name="daqloon_attack" type="number" min="10" value="10" /></br>
                        <label for="daqloon_defence"> Daqloon defence: </label>
                        <input name="daqloon_defence" type="number" min="10" value="10" /></br>
                        <button type="button" onclick="calculate();"> Calculate </button>
                    </form>
                </div>
                <div id="calc_result">
                    <button type="button" onclick="toggle(1);"> New calculation </button>
                </div>
            </div>
            <div id="overview">
                <?php get_template('warrior_levels', $this->data['warrior_data']);?>
            </div>
            <div id="actions">
                <label for="action"> Select action </label>
                <select name="action">
                    <option selected></option>
                    <option value="transfer"> Transfer </option>
                    <option value="heal"> Heal </option>
                    <option value="rest"> Rest </option>
                    <option value="training"> Training </option>
                    <option value="offRest"> Off rest </option>
                    <option value="changeType"> Change Type </option>
                </select>
                <div>
                    <div id="heal">
                        <p> Select item to heal: </p>
                        <div id="selected">
                            
                        </div>
                        <div id="selected_t"></div>
                        <input type="number" id="quantity" min="0" />
                        <div id="inventory">
                            <?php require(constant('ROUTE_VIEW') . "inventory.php"); url();?>
                        </div>
                    </div>
                    <div id="training">
                        <label for="type"> Select type of training</label>
                        <select name="type">
                            <option></option>
                            <option value="general"> General </option>
                            <option value="stamina"> Stamina </option>
                            <option value="technique"> Technique </option>
                            <option value="precision"> Precision </option>
                            <option value="strength"> Strength </option>
                        </select></br>
                    </div>
                    <button> Do Action </button>
                </div>
                <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
                <script src="<?php echo constant('ROUTE_JS') . 'selectitem.js';?>"></script>
            </div>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php');?>
        </aside>
    </body>
</html>
