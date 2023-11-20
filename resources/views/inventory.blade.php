<p>Inventory <span id="inventory-status"> {{ '(' . count($Inventory) . ' / ' . '18' . ')' }}</span>
</p>
<div id="item_tooltip">
    <ul>
        <li></li>
        <li>
            <span id="tooltip_item_price"></span>
            <x-goldIcon /> each
        </li>
    </ul>
</div>
@foreach ($Inventory as $key)
    <div class="inventory_item">
        <figure>
            <img src="{{ asset('images/' . $key->item . '.png') }}" />
            <figcaption class="tooltip"> {{ ucwords($key->item) }}
                @if ($key->amount)
                    <br> {{ $key->amount }} x {{ $key->amount }}
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
