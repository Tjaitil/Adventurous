<!-- Add a item -->

<?php 
/** 
 * Create item container(s) from data provided
 * @param array $itemArray
*/
function createItem($itemArray) {
    foreach ($itemArray as $key): ?>
        <div class="item">
            <figure>
                <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png'; ?>">
                <figcaption class="tooltip"><?php echo $key['item']; ?></figcaption>
                <span class="item_amount"><?php echo $key['amount']; ?></span>
            </figure>
        </div>
<?php endforeach;
} ?>