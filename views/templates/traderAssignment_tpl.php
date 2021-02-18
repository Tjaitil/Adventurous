<?php
    if($data['assignment_id'] != 0): ?>
        <tr class="sectionTableNoPseudo">
            <td colspan="2">
                <figure class="item">
                    <img src="<?php echo constant('ROUTE_IMG') . strtolower($data['cargo']) . '.png';?>" />
                        <figcaption><?php echo ucwords($data['cargo']);?></figcaption</figure></td>
        </tr>
        <tr class="sectionTableNoPseudo">
            <td><span class="detatil">Route:</span>&nbsp;
                <?php echo ucwords($data['base']) . '->' .
                            ucwords($data['destination']);?>
            </td>
            <td>
                Assignment type: &nbsp;
                <?php echo $data['assignment_type'];?>
            </td>
        </tr>
        <tr class="sectionTableNoPseudo">
            <td>Progress: &nbsp;
                <div id="traderAssignment_progressBar" class="progressBarContainer">
                    <div class="progressBarOverlay">
                    </div>
                    <div class="progressBar">
                        <span class="progressBar_currentValue"><?php echo $data['delivered'];?></span>
                        &nbsp/&nbsp
                        <span class="progressBar_maxValue"><?php echo $data['assignment_amount'];?></span>
                    </div>
                </div>
            </td>
            <td>
                Cart Capasity: <?php echo $data['cart_amount'] , '/',
                                $data['capasity'];?>
            </td>
        </tr>
        <tr class="sectionTableNoPseudo">
            <td colspan="2">
                <button onclick="pickUp();">Pick up items</button>
                <button onclick="deliver();">Deliver</button>
            </td>
        </tr>
    <?php else: ?>
        <tr class="sectionTableNoPseudo">
            <td></td>
            <td></td>
        </tr>
        <tr class="sectionTableNoPseudo">
            <td colspan="2"> No current assignment, to get new assignment see the list above </td>
        </tr>
        <tr class="sectionTableNoPseudo">
            <td></td>
            <td></td>
        </tr>
        <tr class="sectionTableNoPseudo">
            <td></td>
            <td></td>
        </tr>
    <?php endif;?>