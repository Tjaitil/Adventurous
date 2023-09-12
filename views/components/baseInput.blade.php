@props(['id', 'name', 'label', 'type' => 'text', 'value' => '', 'show-label' => true])
@php
    /**
     * @var string $name
     * @var string $label
     * @var string $type
     * @var string $value
     */
@endphp
<div>
    <label for="{{ $name }}">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}"
        id="{{ $id }}" value="{{ $value }}"
        {{ $attributes }} />
    {{ $slot }}
</div>
