<?php

/**
 * @var array $data
 * @var boolean $data.show_amount_input
 * @var string $data.selected_amount_label
 */
?>
<div id="selected-item-data-wrapper">
    <div id="selected" class="mt-1 mb-1">

    </div>
    <div id="selected_item_amount_wrapper">
        <?php if (isset($data['show_amount_input']) && $data['show_amount_input'] === true) : ?>
            <label for="selected-item-amount"><?php echo $data['selected_amount_label'] ?? "amount" ?></label>
            <input type="number" name="selected-item-amount" id="selected-item-amount" />
        <?php endif; ?>
    </div>
</div>