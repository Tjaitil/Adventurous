<?php

use App\Http\Resources\DiplomacyResource;

/**
 * Get CSS class based on diplomacy level
 *
 * @param int $diplomacy level
 *
 * @return void|string
 */
function fetchDiplomacyClass(int $diplomacy)
{
    if ($diplomacy > 1) {
        return 'class="positiveDiplomacy"';
    } else if ($diplomacy < 1) {
        return 'class="negativeDiplomacy"';
    } else {
        return;
    }
}

/**
 * @var DiplomacyResource $data
 */

?>
<div class="mt-1">
    <img src="<?php echo constant('ROUTE_IMG') . 'diplomacy icon.png' ?>">
</div>
<table class="lightTextColor middle-align">
    <thead>
        <tr>
            <td> Location </td>
            <td> Diplomacy </td>
        </tr>
    </thead>
    <tr>
        <td> Hirtam </td>
        <td <?php echo fetchDiplomacyClass($data['hirtam']); ?>>
            <?php echo $data['hirtam']; ?>
        </td>
    </tr>
    <tr>
        <td> Pvitul </td>
        <td <?php echo fetchDiplomacyClass($data['pvitul']); ?>>
            <?php echo $data['pvitul']; ?>
        </td>
    </tr>
    <tr>
        <td> Khanz </td>
        <td <?php echo fetchDiplomacyClass($data['khanz']); ?>>
            <?php echo $data['khanz']; ?>
        </td>
    </tr>
    <tr>
        <td> Ter </td>
        <td <?php echo fetchDiplomacyClass($data['ter']); ?>>
            <?php echo $data['ter']; ?>
        </td>
    </tr>
    <tr>
        <td> Fansal Plains </td>
        <td <?php echo fetchDiplomacyClass($data->fansalplains); ?>>
            <?php echo $data->fansalplains; ?>
        </td>
    </tr>
    </tr>
</table>