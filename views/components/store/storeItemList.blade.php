@props(['store-items'])
@php
    /**
     * @param StoreItemResource[] $storeItems
     */
@endphp
<div id="store-container-item-list"
    class="pb-05 flex-column min-[336px] flex basis-1/2 overflow-y-scroll">
    @foreach ($storeItems as $key)
        <x-store.storeContainerItem :item="$key" />
    @endforeach
</div>
