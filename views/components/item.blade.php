@props(['name', 'amount', 'show_tooltip' => true, 'show_amount' => true, 'id' => null])
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
        @if ($showAmount)
            <span @class(['item_amount'])>{{ $amount }}</span>
        @endif
    </figure>
</div>
