@php
    /**
     * @param string $name
     * @param int $amount
     * @param boolean $show_tooltip
     * @param boolean $show_amount
     * @param string $id
     */
@endphp
<div @class(['item', 'no-tooltip' => $show_tooltip === false]) @if (isset($id))
    id='{{ $id }}'
    @endif
    >
    <figure>
        <img class="mx-auto" src="{{ constant('ROUTE_IMG') . strtolower($name) . '.png' }}" />
        <figcaption @class(['tooltip' => $show_tooltip === true])>
            {{ ucwords($name) }}
        </figcaption>
        <span @class([
            'item_amount',
            'hidden' => isset($show_amount) && $show_amount === false,
        ])>{{ $amount }}</span>
    </figure>
</div>
