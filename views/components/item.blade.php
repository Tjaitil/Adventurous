@props(['name', 'amount', 'show-tooltip' => true, 'show-amount' => true, 'id' => null])
@php
    /**
     * @param string $name
     * @param int $amount
     * @param boolean $showTooltip
     * @param boolean $showAmount
     * @param string $id
     */
@endphp
<div @class(['item', 'no-tooltip' => $showTooltip === false]) @if (isset($id))
    id='{{ $id }}'
    @endif
    >
    <figure>
        <img class="mx-auto"
            src="{{ constant('ROUTE_IMG') . strtolower($name) . '.png' }}" />
        <figcaption @class(['tooltip' => $showTooltip === true])>
            {{ ucwords($name) }}
        </figcaption>
        <span @class(['item_amount', 'hidden' => !$showAmount])>{{ $amount }}</span>
    </figure>
</div>
