<?php
if (!function_exists('calculateHealth')) {
    function calculateHealth($rest_start, $health)
    {
        $rest_start = date_timestamp_get(new DateTime($rest_start));
        $date_now = date_timestamp_get(new DateTime(date("Y-m-d H:i:s")));
        $health_gained = (($date_now - $rest_start) / 60) * 3;
        if ($health_gained + $health > 100) {
            return $health = 100;
        } else {
            return $health + $health_gained;
        }
    }
}
if (!function_exists('warriorStatus')) {
    function warriorStatus($warrior)
    {
        // Check the data for if the what the current status for the warrior is
        // If mission is 0 it is not active, it will be 1 if it is active
        if ($warrior['army_mission'] !== 0) {
            return "On Mission " . $warrior['army_mission'];
        } else if ($warrior['army_mission'] == 2) {
            return "On Adventure";
        }
        // If fetch_report is 1 training is active, depending on the training countdown it may be done
        if ($warrior['fetch_report'] == 1) {
            return "Training";
        }
        // If rest is 1 the warrior is resting
        if ($warrior['rest'] == 1) {
            return "Resting";
        } else {
            return "Idle";
        }
    }
}

/**
 * @var WarriorResource $data
 */
?>

<div id="warrior_<?php echo $data['warrior_id']; ?>" class="warrior">
    <div class="info">
        <div class="warrior-info-container">
            <img src="<?php echo constant('ROUTE_IMG') . $data['type'] . ' icon.png'; ?>" alt="<?php echo $data['warrior_id']; ?>" />
            <div>
                <p><?php echo '#', $data['warrior_id']; ?></p>
                <p class="warrior-location">Location: <?php echo ucwords($data['location']); ?></p>
            </div>
        </div>
        <div class="div_content pb-05">
            <div class="warrior-info-container">
                <p>Health
                <p class="warrior-health">
                    <?php echo $data['health'];
                    echo ($data['rest'] == '1') ? " (" . calculateHealth($data['rest_start'], $data['health']) . ")" : ""; ?>
                </p>
            </div>
            <div class="warrior-info-container">
                <p>Status </p>
                <div>
                    <?php $status = warriorStatus($data); ?>
                    <p class="warrior-status <?php echo ($status === "Idle") ? 'green-color' : ""; ?>">
                        <?php echo $status; ?>
                    </p>
                    <p class="countdown"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="warrior_level_up mt-1 mb-1">

    </div>
    <div class="levels">
        <div class="warrior-skill-container">
            <figure>
                <img class="warrior_skill" src="<?php echo constant('ROUTE_IMG') . 'stamina icon.png'; ?>" />
                <figcaption class="warrior-skill-level"><?php echo $data['levels']['stamina_level']; ?></figcaption>
            </figure>
            <div class="progressBarContainer skill_bar stamina_skill_bar">
                <div class="progressBarOverlayShadow">
                </div>
                <div class="progressBarOverlay">
                </div>
                <div class="progressBar skill_bar_progress">
                    <span class="progressBar_currentValue">
                        <?php echo $data['levels']['stamina_xp']; ?>
                    </span>
                    &nbsp/&nbsp
                    <span class="progressBar_maxValue">
                        <?php
                        // Get the next level xp by accessing array with the current skill level
                        echo $data['levels']['stamina_next_level_xp']; ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="warrior-skill-container">
            <figure>
                <img class="warrior_skill" src="<?php echo constant('ROUTE_IMG') . 'technique icon.png'; ?>" />
                <figcaption class="warrior-skill-level"><?php echo $data['levels']['technique_level']; ?></figcaption>
            </figure>
            <div class="progressBarContainer skill_bar technique_skill_bar">
                <div class="progressBarOverlayShadow">
                </div>
                <div class="progressBarOverlay">

                </div>
                <div class="progressBar skill_bar_progress">
                    <span class="progressBar_currentValue">
                        <?php echo $data['levels']['technique_xp']; ?>
                    </span>
                    &nbsp/&nbsp
                    <span class="progressBar_maxValue">
                        <?php echo $data['levels']['technique_next_level_xp']; ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="warrior-skill-container">
            <figure>
                <img class="warrior_skill" src="<?php echo constant('ROUTE_IMG') . 'precision icon.png'; ?>" />
                <figcaption class="warrior-skill-level"><?php echo $data['levels']['precision_level']; ?></figcaption>
            </figure>
            <div class="progressBarContainer skill_bar precision_skill_bar">
                <div class="progressBarOverlayShadow">
                </div>
                <div class="progressBarOverlay">

                </div>
                <div class="progressBar skill_bar_progress">
                    <span class="progressBar_currentValue">
                        <?php echo $data['levels']['precision_xp']; ?>
                    </span>
                    &nbsp/&nbsp
                    <span class="progressBar_maxValue">
                        <?php echo $data['levels']['precision_next_level_xp']; ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="warrior-skill-container">
            <figure>
                <img class="warrior_skill" src="<?php echo constant('ROUTE_IMG') . 'strength icon.png'; ?>" />
                <figcaption class="warrior-skill-level"><?php echo $data['levels']['strength_level']; ?></figcaption>
            </figure>
            <div class="progressBarContainer skill_bar strength_skill_bar">
                <div class="progressBarOverlayShadow">
                </div>
                <div class="progressBarOverlay">

                </div>
                <div class="progressBar skill_bar_progress">
                    <span class="progressBar_currentValue">
                        <?php echo $data['levels']['strength_xp']; ?>
                    </span>
                    &nbsp/&nbsp
                    <span class="progressBar_maxValue">
                        <?php echo $data['levels']['strength_next_level_xp']; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>