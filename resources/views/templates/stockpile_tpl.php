<?php
function amounts($value)
{
    switch (true) {
        case $value >= 1000:
            return round($value / 1000, 1) . 'k';
            break;
        default:
            return $value;
            break;
    }
} ?>
<p>Item slots: <?php echo count($data['stockpile']), " / 60" ?></p>

<?php
foreach ($data['stockpile'] as $key) : ?>
    <div class="stockpile_item">
        <figure>
            <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png'; ?>" />
            <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
        </figure>
        <span class="item_amount"><?php echo amounts($key['amount']); ?></span>
    </div>
<?php endforeach; ?>