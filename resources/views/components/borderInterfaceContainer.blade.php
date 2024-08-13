@props(['borderStyle' => 'brown', 'variant' => 'dark'])
@php
    if ($borderStyle === 'orange') {
        $color = 'border-yellow-700';
        $variant = 'light';
    } else {
        $color = 'border-primary-700';
        $variant = 'dark';
    }
@endphp
<baks-card variant="{{ $variant }}" {{ $attributes }}>
    {!! $slot !!}
</baks-card>
</div>
