@props(['name', 'amount' => 0, 'showTooltip' => true, 'showAmount' => true, 'id' => null])
@php
    /**
     * @param string $name
     * @param int $amount
     * @param boolean $showTooltip
     * @param boolean $showAmount
     * @param string $id
     */
@endphp
<div @class(['item', 'no-tooltip' => $showTooltip === false]) @if (isset($id)) id='{{ $id }}' @endif>
    <figure>
        <img class="mx-auto" src="{{ asset('images/' . strtolower($name) . '.png') }}" />
        <figcaption @class(['tooltip' => $showTooltip === true])>
            <span class="tooltip_item">
                {{ ucwords($name) }}
            </span>
        </figcaption>
        <span @class(['item_amount', 'hidden' => !$showAmount])>{{ $amount }}</span>
    </figure>
</div>
