@php
    /**
     * @param StoreItemResource $key
     */
@endphp
<div
    class="store-container-item relative flex flex-row gap-3 p-4 cursor-pointer 
            border-b-2 border-primary-400 last:border-0">
    @component('components.item', [
        'name' => $key['name'],
        'show_tooltip' => false,
        'show_amount' => false,
        'amount' => 1,
    ])
    @endcomponent
    <p>
        <span>
            <span @class([
                'store-container-item-price',
                'line-through' => $key['adjusted_store_value'] > 0,
            ])>
                {{ $key['store_value'] }}
            </span>
            @if ($key['adjusted_store_value'] > 0)
                <span class="able-color">
                    {{ $key['adjusted_store_value'] }}
                </span>
            @endif
        </span>
    </p>
    @component('components.goldIcon')
    @endcomponent
</div>
