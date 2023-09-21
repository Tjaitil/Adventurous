@props([
    'store-resource',
    'options' => [
        'item-requirements' => false,
        'item-information' => false,
        'show-input-amount' => true,
        'show-requirements' => true,
    ],
])
@php
    /**
     * @param StoreResource[] $store_resource
     * @param array
     * $options = [
     *  'item_requirements' => (boolean) Add requirements container. Optional.
     *  'item_information  => (boolean) Add information contiainer about item. Optional.
 *  'show-amount_input' => (boolean) Add amount input. Useful if the amount is set to 1. Default visible.
 *  'show-requirements' => (boolean) Show requirements container. Default visible.
 *
 * ]
 */
$options = array_merge(
    [
        'item-requirements' => false,
        'item-information' => false,
        'show-input-amount' => true,
        'show-requirements' => true,
        ],
        $options,
    );
@endphp
<x-borderInterfaceContainer>
    <div id="store-container-item-wrapper"
        class="max-w-500px relative mx-auto box-border flex max-h-[500px] min-h-[250px] flex-row">
        <x-store.storeItemList :store-items="$storeResource->store_items" />
        <div id="store-container-item-selected"
            class="basis-1/2 border-l-2 border-primary-400 px-4">
            <div id="store-container-do-trade"
                class="hidden min-w-[155px] flex-col justify-between gap-4 py-4">
                <div id="store-container-selected-trade">

                </div>
                <p id="store-contaniner-trade-price"
                    class="flex flex-row justify-center">
                    <span id="store-container-trade-price-span"></span>
                    <x-goldIcon />
                </p>
                @if ($options['show-requirements'] === true)
                    <div class="flex flex-col gap-2 bg-primary-900 p-2">
                        <span class="mb-2">Required</span>
                        <div id="store-container-item-requirements"
                            class="d-flex justify-center"></div>
                        <div class="skill-requirements"></div>
                    </div>
                @endif
                @if (isset($options['item_information']) &&
                        $options['item_information'] === true)
                    <p id="store-container-item-information"></p>
                @endif
                <div @class([
                    'w-full',
                    'hidden' => $options['show-input-amount'] === false,
                ])>
                    <x-baseInput id="store-container-selected-trade-amount"
                        labelText="Select your Amount" name="amount"
                        type="number" min="1" value="1" />
                </div>
                <x-button id="store-container-item-trade-button"
                    text="Trade" />
            </div>
            <div id="store-container-no-trade-selected"
                class="block text-center">
                <p> Select an item in the list </p>
            </div>
        </div>
    </div>
</x-borderInterfaceContainer>
