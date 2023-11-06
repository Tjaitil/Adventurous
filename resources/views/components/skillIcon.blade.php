@props(['skill', 'level', 'hasRequiredLevel' => true, 'showAbleColor' => true, 'size' => 'small'])
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
        src="{{ asset('images/' . strtolower($skill) . ' icon.png') }}" />
    {{ $level }}
</p>
