<?php

/**
 * Create button to toggle army mission tab
 * @param String|Int $key DOM key, undefined if it rendered in ajax call
 * @return html Generates html content
 */
function createActiveArmyMissionButtonTab($key = 'undefined')
{
?>
    <li class="mission-tab-toggle-container" data-mission-tab-toggle-container="<?php echo $key; ?>">
        <button class="mission-tab-toggle" data-mission-tab-toggle="<?php echo $key; ?>">
            Mission
        </button>
    </li>
<?php
}

/**
 * Create army mission tab
 * 
 * @param array $mission_data Mission data to be displayed in container
 * @param string|int $key DOM key, undefined if it rendered in ajax call
 * @return template Generates html content
 */
function createActiveArmyMissionTab($mission_data, $key = 'undefined')
{ ?>
    <div class="mission-tab" data-mission-tab="<?php echo $key; ?>" data-mission-id="<?php echo $mission_data['mission_id']; ?>">
        <p class="mission-countdown"></p>
        <?php
        createArmyMissionWrapper($mission_data, true);
        ?>
        <div>
            <button class="current-mission-cancel">
                Cancel mission
            </button>
            <button class="current-mission-get-report">
                Get mission report
            </button>
        </div>
    </div>
<?php
}

/**
 * Check if player is in the correct location
 * 
 * @param String $location Location of army mission
 * @return String classname that represent if player are able or not
 */
function checkLocation($location)
{
    if ($location === $_SESSION['gamedata']['location']) {
        return 'green-color';
    } else {
        return 'not-able-color';
    }
}

/**
 * Check if player have the required level
 * 
 * @param String $difficulty Difficulty of army mission
 * @return String classname that represent if player are able or not
 */

function checkWarriorLevel($difficulty)
{
    $level = array("easy" => 5, "medium" => 20, "hard" => 34);
    if ($level[$difficulty] > $_SESSION['gamedata']['warrior']['level']) {
        return 'not-able-color';
    } else {
        return 'green-color';
    }
}

/**
 * Create wrapper that holds army mission details
 * 
 * @param array $data Array that holds the army missions data
 * @return html
 */
function createArmyMissionWrapper($data, $active_mission)
{ ?>
    <div class="mission-info mb-1 mt-1 div_content_light div_content cur-pointer" data-mission-id="<?php echo $data['mission_id']; ?>">
        <div class="mission-info-wrapper">
            <div class="text-align-left">
                <p>Mission</p>
                <p><?php echo $data['mission_id']; ?></p>
            </div>
        </div>
        <div class="mission-info-wrapper">
            <div>
                <p>Warriors required</p>
                <p><?php echo $data['required_warriors']; ?></p>
            </div>
            <div>
                <p>Location</p>
                <p class="<?php echo checkLocation($data['location']); ?>">
                    <?php echo ucwords($data['location']); ?>
                </p>
            </div>
        </div>
        <div class="mission-info-wrapper">
            <div>
                <p>Difficulty</p>
                <p class="<?php echo checkWarriorLevel($data['difficulty']); ?>">
                    <?php echo ucwords($data['difficulty']); ?>
                </p>
            </div>
            <div>
                <p>Reward</p>
                <p><?php echo $data['reward']; ?></p>
            </div>
        </div>
        <div class="mission-info-wrapper">
            <div>
                <p>Minutes</p>
                <p><?php echo $data['time']; ?></p>
            </div>
            <div>
                <p>Combat</p>
                <p><?php echo $data['combat']; ?></p>
            </div>
        </div>
    </div>
<?php
}
?>