|mine.js|
<h1 class="page_title">Mine</h1>

<div id="mine">

    <?php
    $this->data['do_action_text'] = "Mine";
    $this->data['finish_action_text'] = "Fetch minerals";
    $this->data['cancel_action_text'] = "Cancel mining";
    $this->data['action_type_label'] = "Minerals";
    $this->data['show_permits'] = true;

    fetchTemplate('skillactioncontainer', $this->data);
    ?>
</div>