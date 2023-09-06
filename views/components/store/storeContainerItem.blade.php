@props(['item'])
@php
    /**
     * @param StoreItemResource $item
     */
@endphp
<div
    class="store-container-item relative flex cursor-pointer flex-row gap-3 border-b-2 border-primary-400 p-4 last:border-0">
    <x-item :name="$item->name" :show-tooltip="false" :show-amount="false"
        :id="$item->id" />
    <div class="flex grow flex-row items-center justify-between">
        <p class="flex flex-row items-center justify-center">
            <span>
                <span @class([
                    'store-container-item-price',
                    'line-through' => $item->adjusted_difference > 0,
                    'mr-2',
                ])>
                    {{ $item->store_value }}
                </span>
                @if ($item->adjusted_difference > 0)
                    <span class="able-color">
                        {{ $item->adjusted_store_value }}
                    </span>
                @endif
            </span>
            <x-goldIcon />
        </p>
        @if ($item->amount > -1)
            <span>x {{ $item->amount }}</span>
        @endif
    </div>
</div>
