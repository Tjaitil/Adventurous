<?php

/**
 * @var array data
 * @property int $discount
 * @property string $discount_text
 * 
 */

if ($data['discount'] > 0) : ?>
    <span class="text-success"><?php echo $data['discount_text']; ?></span>
<?php endif; ?>