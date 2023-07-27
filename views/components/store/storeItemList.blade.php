@php
    /**
     * @param StoreItemResource[] $store_items
     */
@endphp
<div id="store-container-item-list"
    class="pb-05 basis-2/5 flex flex-column overflow-y-scroll min-[336px]
            border-r-2 border-primary-400">
    @foreach ($store_items as $key)
        @component('components.store.storeContainerItem', [
            'key' => $key,
            'options' => $options,
        ])
        @endcomponent
    @endforeach
</div>
