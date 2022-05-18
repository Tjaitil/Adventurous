<?php
$locations = array("hirtam", "pvitul", "khanz", "ter", "fansalplains");
$diplomacy_price_adjust = 1;
$location = str_replace("-", "", $_SESSION['gamedata']['location']);
if (in_array($location, $locations)) {
    $diplomacy_price_ratio = $data['diplomacy'][$location];
    if ($diplomacy_price_ratio > 1.2) {
        $diplomacy_price_adjust = 0.1;
    } else if ($diplomacy_price_ratio > 1) {
        $diplomacy_price_adjust = ($diplomacy_price_ratio - 1) / 2;
    } else {
        $diplomacy_price_adjust = (1 - $diplomacy_price_ratio) / 2;
    }
}

if($_SESSION['gamedata']['location'] === "fagna"): ?>
<p> 
    No store trades in Fagna
</p>
<?php elseif(count($data['offers']) === 0):?>
<p>No available trades at the moment</p>
<?php else: 
foreach ($data['offers'] as $key) : 
    $price_info = getBuyPrice($key['user_buy_price'], $key['user_sell_price'], $diplomacy_price_adjust);
    ?>
    <div class="merchant-offer">
        <figure>
            <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png'; ?>" />
            <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
        </figure>
        <p class="merchant-offer-price">
            <span>
                <span class="item_buy_price"><?php echo number_format($price_info[0], 0, ',', '.'); ?></span>
                <?php if($price_info[1] !== 0): ?>
                    <span class="<?php echo $price_info[2];?>"><?php echo '(' . $price_info[1] . ')';?></span>
                <?php endif;?>
            </span>
            <span><?php echo number_format($key['user_sell_price'], 0, ',', '.');?></span>
        </p>
        <img class="gold" src="<?php echo constant("ROUTE_IMG") . 'gold.png'; ?>" />
        <p class="merchant-offer-amount"> X <?php echo $key['amount'];?></p>
    </div>
<?php endforeach; endif; ?>
<?php 
function getBuyPrice($original_price, $sell_price, $diplomacy_price_adjust) {
    $buy_price = "";
    $difference = 0;
    $class = "";
    if(!isset($diplomacy_price_ratio)) {
        return [$original_price, $difference, $class];
    } else {
        // Calculate ratio, example 0.95 diplomacy would result in (0.05 in paranthesis)
        if(round(($diplomacy_price_ratio < 1))) {
            $buy_price = $original_price * (1.0 + $diplomacy_price_adjust);
            $class = "negativeDiplomacy";
        } else {
            $buy_price = $original_price * (1.0 - $diplomacy_price_adjust);
            $class = "positiveDiplomacy";
        }
        $difference = $original_price - $buy_price;
        if($difference > 0) $difference = "+ " . $difference;
        if($buy_price < $sell_price) $buy_price = $sell_price;
        return [$buy_price, $difference, $class];
    }
}
?>