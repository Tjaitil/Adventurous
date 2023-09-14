@props(['show-amount_input' => false, 'label-text' => 'amount'])
@php
    /**
     * @param boolean $show-amount-input
     * @param string $label-text
     */
@endphp
<div id="selected-item-data-wrapper">
    <div id="selected" class="mb-1 mt-1">

    </div>
    <div id="selected_item_amount_wrapper">
        @if ($showAmountInput)
            <div class="mb-2">
                <x-baseInput id="selected-item-amount" name="selected-item-amount"
                    class="mx-auto w-24" :label="$labelText" type="number"
                    value="" :show-label="true" />
            </div>
        @endif
        <x-button id="seed_generator_action" text="Generate" />
    </div>
</div>
