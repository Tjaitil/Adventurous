<?php

/**
 * @param StoreResource[] $store_resource
 * @param array
 * $options = [
 *  'item_requirements' => (boolean) Add requirements container. Optional.
 *  'item_information  => (boolean) Add information contiainer about item. Optional.
 *  'amount_input' => (boolean) Add amount input. Useful if the amount is set to 1. Default visible.
 * ]
 * @return html
 */
?>
@component('components.BorderInterfaceContainer')
    <div id="store-container-item-wrapper"
        class="relative flex flex-row box-border max-w-500px  min-h-[250px] max-h-[500px] mx-auto">
        @component('components.store.storeItemList', [
            'store_items' => $store_resource->store_items,
            'options' => $options,
            'show_tooltip' => true,
        ])
        @endcomponent
        <div id="store-container-item-selected" class="basis-3/5">
            <div id="store-container-do-trade" class="min-w-[155px] hidden flex-col justify-between gap-4 py-4">
                <div id="store-container-selected-trade">

                </div>
                <p id="store-contaniner-trade-price" class="flex flex-row justify-center">
                    <span></span>
                    @component('components.goldIcon')
                    @endcomponent
                </p>
                <div class="bg-primary-900 p-2 flex flex-col gap-2">
                    <span class="mb-2">Required</span>
                    @if (isset($options['item_requirements']) && $options['item_requirements'] === true)
                        <div id="store-container-item-requirements" class="d-flex justify-center"></div>
                    @endif
                    <div class="skill-requirements"></div>
                </div>
                @if (isset($options['item_information']) && $options['item_information'] === true)
                    <p id="store-container-item-information"></p>
                @endif
                <div @class([
                    'w-full',
                    'hidden' =>
                        isset($options['input_amount']) && $options['input_amount'] === false,
                ])>
                    <label for="amount">Select your Amount</label><br>
                    <input type="number" id="store-container-selected-trade-amount" name="amount" min="1"
                        value="1" />
                </div>
                @component('components.button', ['id' => 'store-container-item-event-button', 'text' => 'Trade'])
                @endcomponent

            </div>
            <div id="store-container-no-trade-selected" class="block text-center">
                <p> Select an item in the list </p>
            </div>
        </div>
    </div>
@endcomponent
