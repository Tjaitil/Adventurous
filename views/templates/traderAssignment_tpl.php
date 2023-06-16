<?php

/**
 * @var array $data
 * @property int $assignment_id Trader assignment id
 * @property int $cart_amount Amount of items in cart
 * @property int $cart_type Type of cart the user has
 * @property int $delivered Amount the user has delivered
 * @property string $trading_countdown Countdown for the assignment
 * @property int $cart_capasity Countdown for the assignment
 * @property string $base
 * @property string $destination
 * @property string $cargo
 * @property int $assignment_amount
 * @property string $assignment_type
 * @property string $assignment_reward
 * @property int $assignment_time
 */

if ($data['assignment_id'] != 0) : ?>

    <div class="traderAssignment_fullColumn">
        <figure class="item">
            <img src="<?php echo constant('ROUTE_IMG') . strtolower($data['cargo']) . '.png'; ?>" />
            <figcaption><?php echo ucwords($data['cargo']); ?></figcaption>
        </figure>
    </div>
    <div class="traderAssignment_fullColumn">
        <span>
            Route:&nbsp;
            <?php echo ucwords($data['base']) . ' -> ' . ucwords($data['destination']); ?>
        </span>
    </div>
    <div id="traderAssignment_progressBar" class="progressBarContainer traderAssignment_fullColumn mx-auto">
        <div class="progressBarOverlayShadow">
        </div>
        <div class="progressBarOverlay">
        </div>
        <div class="progressBar">
            <span class="progressBar_currentValue"><?php echo $data['delivered']; ?></span>
            &nbsp/&nbsp
            <span class="progressBar_maxValue"><?php echo $data['assignment_amount']; ?></span>
        </div>
    </div>
    <div>
        <span>
            Assignment type: &nbsp;
            <?php echo $data['assignment_type']; ?>
        </span>
    </div>
    <div>
        Cart Capasity: <span id="traderAssignment_cart_amount"><?php echo $data['cart_amount']; ?></span> /
        <span id="traderAssignment_cart_"><?php echo $data['cart_capasity']; ?></span>
    </div>
    <div class="traderAssignment_fullColumn mb-2">
        <button id="traderAssignment-pick-up">Pick up items</button>
        <button id="traderAssignment-deliver">Deliver</button>
    </div>
<?php else : ?>
    <div class="traderAssignment_fullColumn">
        No current assignment, to get new assignment see the list above
    </div>
<?php endif; ?>