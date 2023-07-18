<figure @class(['item', 'no-tooltip' => !$show_tooltip])>
    <img class="mx-auto" src="{{ constant('ROUTE_IMG') . strtolower($name) . '.png' }}" />
    <figcaption @class(['tooltip' => $show_tooltip])>
        {{ ucwords($name) }}
    </figcaption>
</figure>
