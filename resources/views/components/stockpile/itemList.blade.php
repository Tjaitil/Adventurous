@props(['stockpile'])
@php
    $max_amount = $max_amount ?? $attributes['max-amount'];
    /**
     * @var \App\Models\Stockpile[] $Stockpile
     * @var int $max_amount
     */
@endphp
<x-borderInterfaceContainer>
    <p class="mb-3">Item slots: {{ $stockpile->count() }} / {{ $max_amount }}
    </p>
    @foreach ($stockpile as $key)
        <div class="stockpile_item">
            <figure>
                <img src="{{ asset('images/' . $key->item . '.png') }}" />
                <figcaption class="tooltip">{{ ucwords($key->item) }}
                </figcaption>
            </figure>
            <span
                class="item_amount">{{ $key->amount > 1000 ? round($key->amount / 1000, 1) . 'k' : $key->amount }}</span>
        </div>
    @endforeach

</x-borderInterfaceContainer>
