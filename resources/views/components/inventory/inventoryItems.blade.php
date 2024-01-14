@foreach ($inventory as $key)
<div class="inventory_item">
    <figure>
        <img src="{{ asset('images/' . $key->item . '.png') }}" />
        <figcaption class="tooltip"> {{ ucwords($key->item) }}
            @if ($key->amount)
                <br> x {{ $key->amount }}
            @endif
        </figcaption>
    </figure>
    <span class="item_amount">
        @if ($key->amount > 1000)
            {{ round($key->amount / 1000, 1) }}k
        @else
            {{ $key->amount }}
        @endif
    </span>
</div>
@endforeach
