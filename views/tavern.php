tavern.css|tavern.js|
<h1 class="page_title"> Tavern </h1>
<?php
/**
 * @var array $this->data
 * @property array $workers
 */

get_template('tavern', $this->data['workers'], true); ?>

<div id="eat">
    <h3 class="mb-05">Current hunger</h3>
    <?php get_template("select_item", null, true); ?>
    <p id="item_healing_amount" class="mb-1"></p>
    <input type="number" min="0" id="healing-item-amount" />
    <button id="tavern-eat-button"> Eat </button>
</div>