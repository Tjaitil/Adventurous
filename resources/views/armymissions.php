    armymissions.css|armymissions.js|
    <?php
    // Get template
    get_template('createArmymissionContainers', null, true);
    ?>
    <h1 class="page_title"> Army Missions </h1>
    <button id="navigate-army-camp"> Back to army camp</button>
    <div id="current_missions" class="cont_close">
        <h3 class="mt-1 mb-1">Current Missions</h3>
        <div>
            <ul id="mission-tab-toggle-outer-container">
                <?php
                if (count($this->data['current_army_missions']) > 0) {

                    foreach ($this->data['current_army_missions'] as $key => $value) {
                        createActiveArmyMissionButtonTab($key);
                    };
                }
                ?>
            </ul>
        </div>
        <div id="mission-tabs-outer-container">
            <?php
            if (count($this->data['current_army_missions']) > 0) {
                foreach ($this->data['current_army_missions'] as $key => $value) {
                    createActiveArmyMissionTab($value, $key);
                }
            } ?>
        </div>
    </div>
    <div id="new_missions">
        <div id="new-mission-selected-container">
            <h2>Start mission</h2>
            <div>
                <!-- Placeholder for mission to be cloned -->
            </div>
            <?php
            get_template("warrior_select", $this->data['warriors'], true); ?>
            <button id="mission-enabled-choose-another">Choose another mission</button>
            <button id="mission-enabled-do">Do mission</button>
        </div>
        <div id="missions-container">
            <?php
            foreach ($this->data['army_missions'] as $key => $value) {
                createArmyMissionWrapper($value, true);
            }
            ?>
        </div>
    </div>