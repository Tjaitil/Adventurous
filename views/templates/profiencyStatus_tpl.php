<?php

use App\enums\SkillNames;
use App\resources\SkillActionResource;
use App\resources\TraderAssignmentResource;
use Carbon\Carbon;

/**
 * Generate html section based on location and data
 *
 * @param array $skillActionResource
 *
 * @return void
 */

function generateStatusSection(array $skillActionResource)
{
    if ($skillActionResource['skill'] === SkillNames::FARMER->value) {
        $type = 'crop_type';
    } else {
        $img_src_ending = ' ore.png';
        $type = 'mining_type';
    }
?>
    <div class="countdown-flex-container">
        <?php
        if (
            $skillActionResource['countdown']['minutes_left'] < 0 &&
            $skillActionResource['type'] === ""
        ) : ?>
            <p> Nothing happening </p>
        <?php else : ?>
            <div>
                <p>Used workforce <?php echo $skillActionResource['workforce']['location_amount'] . '/' .
                                        $skillActionResource['workforce']['total_amount']; ?></p>
                <p>In progress</p>
            </div>
            <div>
                <figure class="item">
                    <img src="<?php echo constant('ROUTE_IMG') . $skillActionResource['type'] . $img_src_ending; ?>" />
                    <figcaption><?php echo ucwords($skillActionResource['type']); ?></figcaption>
                </figure>
            </div>
        <?php endif; ?>
    </div>
<?php
}
?>
<?php
/**
 * @var array $data Array with countdown data
 * @property TraderAssignmentResource $data.trader_assignment
 * @property SkillActionResource[] $data.farmer_resources
 * @property SkillActionResource[] $data.miner_resources
 * @property array $data.warrior_statuses
 */
?>
<?php

?>
<table class="lightTextColor">
    <caption> Profiency countdowns </caption>
    <tr>
        <td>
            <img src="<?php echo constant('ROUTE_IMG') . 'farmer icon.png'; ?>" />

            <?php foreach ($data['farmer_countdowns'] as $key) : ?>
                <h3 class="mt-1"><?php echo $key->location; ?></h3>
                <?php
                $diff_in_minutes = $key->crop_countdown->diffInMinutes(Carbon::now());

                if (Carbon::now()->isAfter($key->crop_countdown) && !$key->crop_type) : ?>
                    <p> Nothing happening </p>
                <?php else : ?>
                    <p><?php echo $diff_in_minutes; ?></p>
                <?php endif; ?>

            <?php endforeach; ?>

            <?php foreach ($data['farmer_resources'] as $key => $value) : ?>

                <?php
                // generateStatusSection($value);
                ?>
            <?php endforeach; ?>
        </td>
    </tr>
    <tr>
        <td>
            <img src="<?php echo constant('ROUTE_IMG') . 'miner icon.png'; ?>" />
            <?php foreach ($data['miner_resources'] as $key => $value) : ?>
                <h3 class="mt-1"><?= ucwords($value['workforce']['location']) ?></h3>
                <?php generateStatusSection($value); ?>
            <?php endforeach; ?>
        </td>
    </tr>
    <tr>
        <td>
            <img src="<?php echo constant('ROUTE_IMG') . 'trader icon.png'; ?>" />
            <?php if (intval($data['trader_assignment']['assignment_id']) === 0) : ?>
                <?php echo 'none'; ?>
            <?php else : ?>
                <div id="traderAssignment_current">
                    <div class="traderAssignment_fullColumn fullLengthColumn">
                        <figure class="item">
                            <img src="<?php echo constant('ROUTE_IMG') .
                                            strtolower($data['trader_assignment']['cargo']) . '.png'; ?>" />
                            <figcaption><?php echo ucwords($data['trader_assignment']['cargo']); ?></figcaption>
                        </figure>
                    </div>
                    <div>
                        <?php echo ucwords($data['trader_assignment']['base']) . ' --> ' .
                            ucwords($data['trader_assignment']['destination']); ?>
                    </div>
                    <div>
                        <span>
                            Cart Capasity: <?php echo $data['trader_assignment']['cart_amount'],
                                            '/', $data['trader_assignment']['cart_capasity']; ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td>
            <img src="<?php echo constant('ROUTE_IMG') . 'warrior icon.png'; ?>" />
            <h3>Warrior(s)</h3>
            <p><?php echo 'finished training: ', $data['warrior_statuses']['statuses']['finished_training']; ?></p>
            <p><?php echo 'training: ', $data['warrior_statuses']['statuses']['training']; ?></p>
            <p><?php echo 'on mission: ', $data['warrior_statuses']['statuses']['on_mission']; ?></p>
            <p><?php echo 'resting: ', $data['warrior_statuses']['statuses']['resting']; ?></p>
            <p><?php echo 'idle: ', $data['warrior_statuses']['statuses']['idle']; ?></p>
            <h3>Armymissions</h3>
            <?php
            // foreach ($data['warrior_status']['army_mission']['current_army_missions'] as $key) : 
            ?>
            <p><?php
                // echo $key['countdown']; 
                ?></p>
            <?php
            // endforeach; 
            ?>
        </td>
    </tr>
</table>