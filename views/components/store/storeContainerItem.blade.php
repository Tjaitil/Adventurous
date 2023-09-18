@props(['item'])
@php
    /**
     * @param StoreItemResource $item
     */
@endphp
<div
    class="store-container-item relative flex cursor-pointer flex-row gap-3 border-b-2 border-primary-400 p-3 last:border-0">
    <x-item :name="$item->name" :show-tooltip="false" :show-amount="false"
        :amount="$item->amount" :id="$item->id" />
    <div class="flex grow flex-row items-center justify-center">
        <span>
            <span @class([
                'store-container-item-price',
                'line-through block' => $item->adjusted_difference !== 0,
            ])>
                {{ $item->store_value }}
            </span>
            @if ($item->adjusted_difference > 0)
                <span class="able-color">
                    {{ $item->adjusted_store_value }}
                </span>
            @elseif ($item->adjusted_difference < 0)
                <span class="not-able-color">
                    {{ $item->adjusted_store_value }}
                </span>
            @endif
        </span>
        <x-goldIcon />
    </div>
    @if ($item->amount > -1)
        <span class="flex items-center">x {{ $item->amount }}</span>
    @endif
</div>
