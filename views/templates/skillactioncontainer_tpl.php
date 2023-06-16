<?php

/**
 * @var array $data
 * @property string $do_action_text
 * @property string $finish_action_text
 * @property string $cancel_action_text
 * @property string $action_type_label
 * @property array $action_types
 * @property bool $show_permits
 */

?>
<div id="action_div" class="div_content">
    <div id="actions" class>
        <p id="info-action-element"></p>
        <p id="time"></p>
        <button id="finish-action"><?php echo $data['finish_action_text']; ?></button>
        <button id="cancel-action"><?php echo $data['cancel_action_text']; ?></button>
    </div>
    <div id="action_body">
        <div id="select">
            <?php
            foreach ($data['action_items'] as $key) :
                $type = isset($key['crop_type']) ? $key['crop_type'] : $key['mineral_type'];
            ?>

                <img class="item-type" src="
                    <?php echo constant('ROUTE_IMG') . $type . '.png'; ?>" alt="<?php echo $type; ?>" />
            <?php endforeach; ?>
        </div>
        <div id="data_container" class="px-1">
            <div id="data">
                <?php if ($data['show_permits']) : ?>
                    <p id="total_permits"> Your total permits: <?php echo $data['permits']; ?></p>
                <?php endif; ?>
                <figure id="selected_item"></figure>
                <form id="data_form">
                    <label for="action-type"><?php echo $data['action_type_label']; ?></label>
                    <input type="text" id="selected-action-type" name="action-type" readonly />
                    <label for="time"> Time </label>
                    <input type="text" name="time" readonly />
                    <span>Efficiency reduction</span><span id="reduction_time"></span>
                    <label for="location">Location</label>
                    <input type="text" name="location" readonly />
                    <label for="level">Level</label>
                    <input type="text" name="level" readonly />
                    <label for="experience">Experience</label>
                    <input type="text" name="experience" readonly />
                    <label for="seeds">Seeds</label>
                    <input type="text" name="seeds" readonly />
                    <label for="workforce">Select workers (max)</label>
                    <div class="me-auto">
                        <input name="workforce_amount" id="workforce_amount" type="number" min="0" required />
                        <span id="data_container_avail_workforce">
                            (<?php echo $data['workforce_data']['avail_workforce'] ?>)
                        </span>
                    </div>
                    </br>
                </form>
                <button type="button" id="do-action" class="mb-2"><?php echo $data['do_action_text']; ?></button>
            </div>
        </div>
    </div>
</div>