        armycamp.css|armycamp.js|
        <?php $i = 1; if($i = 0): ?>
            <?php $_SESSION['gamedata']['level_up_data'] = $data['warrior_level_up'];?>
                <script src="<?php echo constant("ROUTE_JS") . 'warriorLevelUp.js'?>"></script>
        <?php endif?>
            <h3 class="page_title"> Army camp </h3>
            <button type="button"> Army Missions </button>
            <button type="button"> Armory </button>
            <div id="overview">
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
                    <div id="heal">
                        <p> Select item to heal: </p>
                        <div id="selected">
                            
                        </div>
                        <div id="selected_t"></div>
                        <input type="number" id="selected_amount" min="0" />
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
                <div id="warriors">
                    <?php get_template('warriors_levels', array($this->data['warrior_data'], $this->data['levels_data']), true);?>
                </div>
            </div>
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