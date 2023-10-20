@php
    $Stockpile = $stockpile;
    $max_amount = $max_amount ?? $attributes['max-amount'];
    /**
     * @var \App\Models\Stockpile[] $Stockpile
     * @var int $max_amount
     */
@endphp
<x-borderInterfaceContainer>
    <p class="mb-3">Item slots: {{ $Stockpile->count() }} / {{ $max_amount }}
    </p>
    @foreach ($Stockpile as $key)
        <div class="stockpile_item">
            <figure>
                <img src="{{ constant('ROUTE_IMG') . $key->item . '.png' }}" />
                <figcaption class="tooltip">{{ ucwords($key->item) }}
                </figcaption>
            </figure>
            <span
                class="item_amount">{{ $key->amount > 1000 ? round($key->amount / 1000, 1) . 'k' : $key->amount }}</span>
        </div>
    @endforeach

</x-borderInterfaceContainer>
