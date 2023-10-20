<?php

/**
 * @var array $data
 * @property StoreItemResource[] $data['offers']
 * @property string $data['location']
 */

$diplomacy_price_adjust = 1;

// Will be done inside Diplomacyservice
// Adjust price based on diplomacy
// if (in_array($data['location'], GameConstants::DIPLOMACY_LOCATIONS)) {
//     $diplomacy_price_ratio = $data['diplomacy'][$location];
//     if ($diplomacy_price_ratio > 1.2) {
//         $diplomacy_price_adjust = 0.1;
//     } else if ($diplomacy_price_ratio > 1) {
//         $diplomacy_price_adjust = ($diplomacy_price_ratio - 1) / 2;
//     } else {
//         $diplomacy_price_adjust = (1 - $diplomacy_price_ratio) / 2;
//     }
// }

if ($data['location'] === "fagna") : ?>
    <p>
        No store trades in Fagna
    </p>
<?php
    /**
     * @var StoreItemResource[] $data['offers']
     */
elseif (count($data['offers']) === 0) : ?>
    <p>No available trades at the moment</p>
    <?php else :
    foreach ($data['offers'] as $key) :
        // $price_info = getBuyPrice(
        //     $key['store_value'],
        //     $key['sell_value'],
        //     $diplomacy_price_adjust,
        //     $diplomacy_price_ratio
        // );
    ?>
        <div class="merchant-offer">
            <figure>
                <img src="<?php echo constant('ROUTE_IMG') . $key['name'] . '.png'; ?>" />
                <figcaption class="tooltip"><?php echo ucwords($key['name']); ?></figcaption>
            </figure>
            <p class="merchant-offer-price">
                <span>
                    <span class="item_buy_price"><?php echo number_format($key["adjusted_store_value"], 0, ',', '.'); ?></span><br>
                    <?php
                    $difference = $key['adjusted_store_value'] - $key['store_value'];
                    if ($difference !== 0) :
                    ?>
                        <span class="<?php echo getDiplomacyPriceClass($difference); ?>">
                            <?php echo '(' . $difference . ')'; ?>
                        </span>
                    <?php endif; ?>
                </span>
                <span><?php echo number_format($key['sell_value'], 0, ',', '.'); ?></span>
            </p>
            <img class="gold" src="<?php echo constant("ROUTE_IMG") . 'gold.png'; ?>" />
            <p class="merchant-offer-amount"> X <?php echo $key['amount']; ?></p>
        </div>
<?php endforeach;
endif;

function getDiplomacyPriceClass(int $difference)
{
    if ($difference < 0) {
        $class = "positiveDiplomacy";
    } else {
        $class = "negativeDiplomacy";
    }

    return $class;
}

// Will be done inside Diplomacyservice
function getBuyPrice($original_price, $sell_price, $diplomacy_price_adjust, $diplomacy_price_ratio)
{
    $buy_price = "";
    $difference = 0;
    $class = "";
    if (!isset($diplomacy_price_ratio)) {
        return array("buy_price" => $original_price, "difference" => $difference, "class" => $class);
    } else {
        // Calculate ratio, example 0.95 diplomacy would result in (0.05 in paranthesis)
        if (round(($diplomacy_price_ratio < 1))) {
            $buy_price = $original_price * (1.0 + $diplomacy_price_adjust);
            $class = "negativeDiplomacy";
        } else {
            $buy_price = $original_price * (1.0 - $diplomacy_price_adjust);
            $class = "positiveDiplomacy";
        }
        $difference = $original_price - $buy_price;
        if ($difference > 0) $difference = "- " . $difference;
        if ($buy_price < $sell_price) $buy_price = $sell_price;
        return array("buy_price" => $buy_price, "difference" => $difference, "class" => $class);
    }
}
?>