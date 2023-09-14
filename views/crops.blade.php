<h1 class="page_title">Crops</h1>
<div id="grow_crops">
    <x-skillActionContainer do-action-text="Grow"
        cancel-action-text="Cancel growing" finish-action-text="Harvest"
        action-type-label="Crops" :show-permits="false" :action-items="$action_items"
        :workforce-data="$workforce_data" />

</div>
<div id="seed_generator">
    <p>Select a item to get seeds from. The amount will be 1</p>
    <x-selectItem :show-amount-input="true"
        label-text="Select of amount of seeds to generate" />
</div>
