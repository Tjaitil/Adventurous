        armycamp.css|armycamp.js|
        <?php

        use App\libs\TemplateFetcher;

        /**
         * @var array $data
         * @property WarriorResource[] $data['warrior_data']
         */

        ?>
        <h1 class="page_title"> Army camp </h1>
        <button type="button"> Army Missions </button>
        <button type="button"> Armory </button>
        <div id="overview">
            <div id="actions">
                <label for="action"> Select action </label>
                <select name="action">
                    <option selected disabled hidden></option>
                    <option value="transfer">Transfer</option>
                    <option value="heal">Heal</option>
                    <option value="rest">Rest</option>
                    <option value="start training">Training</option>
                    <option value="off rest">Off rest</option>
                    <option value="change type">Change Type</option>
                </select>
                <div id="heal" class="action-additional-div">
                    <p> Select item to heal: </p>
                    <?php
                    echo TemplateFetcher::loadTemplate('select_item', [
                        'show_amount_input' => true,
                    ]);
                    ?>
                </div>
                <div id="training-type-select-wrapper" class="action-additional-div">
                    <label for="type"> Select type of training</label>
                    <select name="type">
                        <option selected hidden disabled></option>
                        <option value="stamina">Stamina</option>
                        <option value="technique">Technique</option>
                        <option value="precision">Precision</option>
                        <option value="strength">Strength</option>
                    </select></br>
                </div>
                <div id="change-warrior-type-select-wrapper" class="action-additional-div">
                    <label for="type"> Select type of warrior</label>
                    <select name="new-warrior-type">
                        <option selected hidden disabled></option>
                        <option value="melee">Melee</option>
                        <option value="ranged">Ranged</option>
                    </select></br>
                </div>
                <p id="multiple-warrior-action-warning" class="mt-1 mb-1"> Only one warrior can be selected for this action </p>
                <button> Do Action </button>
            </div>
            <div id="warriors">
                <?php
                foreach ($data['warrior_data'] as $key => $value) {
                    echo TemplateFetcher::loadTemplate('warrior', $value);
                } ?>
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