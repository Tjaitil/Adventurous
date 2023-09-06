@props(['store-items'])
@php
    /**
     * @param StoreItemResource[] $storeItems
     */
@endphp
<div id="store-container-item-list"
    class="pb-05 flex-column min-[336px] flex basis-2/5 overflow-y-scroll border-r-2 border-primary-400">
    @foreach ($storeItems as $key)
        <x-store.storeContainerItem :item="$key" />
    @endforeach
</div>
