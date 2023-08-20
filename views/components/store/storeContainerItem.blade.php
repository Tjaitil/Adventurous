@php
    /**
     * @param StoreItemResource $key
     */
@endphp
<div
    class="store-container-item relative flex flex-row gap-3 p-4 cursor-pointer 
            border-b-2 border-primary-400 last:border-0">
    @component('components.item', [
        'name' => $key->name,
        'show_tooltip' => false,
        'show_amount' => false,
        'amount' => 1,
    ])
    @endcomponent
    <div class="flex flex-row justify-between items-center grow">
        <p class="flex flex-row justify-center items-center">
            <span>
                <span @class([
                    'store-container-item-price',
                    'line-through' => $key->adjusted_difference > 0,
                    'mr-2',
                ])>
                    {{ $key->store_value }}
                </span>
                @if ($key->adjusted_difference > 0)
                    <span class="able-color">
                        {{ $key->adjusted_store_value }}
                    </span>
                @endif
            </span>
            @component('components.goldIcon')
            @endcomponent
        </p>
        @if ($key->amount > -1)
            <span>x {{ $key->amount }}</span>
        @endif
    </div>
</div>
