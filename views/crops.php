crops.css|crops.js|
<?php

use App\libs\TemplateFetcher;
?>
<h1 class="page_title">Crops</h1>
<div id="grow_crops">
    <?php
    $this->data['do_action_text'] = "Grow";
    $this->data['finish_action_text'] = "Harvest";
    $this->data['cancel_action_text'] = "Cancel growing";
    $this->data['action_type_label'] = "Crops";
    $this->data['show_permits'] = false;

    fetchTemplate('skillactioncontainer', $this->data);
    ?>
</div>
<div id="seed_generator">
    <p>Select a item to get seeds from. The amount will be 1</p>
    <?php
    fetchTemplate('select_item', [
        'show_amount_input' => true,
        'selected_amount_label' => "Select amount of seeds to generate"
    ]);
    ?>
    <button type="button" id="seed_generator_action">Generate</button>
</div>