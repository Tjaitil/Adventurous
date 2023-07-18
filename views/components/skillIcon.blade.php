<p @class([
    'not-able-color' => !$has_required_level && $show_able_color,
    'able-color' => $has_required_level && $show_able_color,
])>
    <img class="mx-auto w-8 h-8" src="{{ constant('ROUTE_IMG') . strtolower($skill) . ' icon.png' }}" />
    {{ $level }}
</p>
