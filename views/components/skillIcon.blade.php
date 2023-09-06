@props(['skill', 'level', 'has-required-level' => true, 'show-able-color' => true, 'size' => 'small'])
@php
    /**
     * @param string $skill
     * @param int $level
     * @param bool $hasRequiredLevel
     * @param bool $showAbleColor
     * @param string $size
     */
@endphp
<p @class([
    'not-able-color' => !$hasRequiredLevel && $showAbleColor,
    'able-color' => $hasRequiredLevel && $showAbleColor,
    'text-center',
])>
    <img {{ $attributes->class(['mx-auto', $size === 'medium' ? 'w-12 h-12' : 'w-8 h-8']) }}
        src="{{ constant('ROUTE_IMG') . strtolower($skill) . ' icon.png' }}" />
    {{ $level }}
</p>
