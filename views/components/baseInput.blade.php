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
    <label for="{{ $name }}"
        class="block text-start">{{ $label }}</label>
    <input
        {{ $attributes->merge(['class' => 'custom-input m-auto block w-full rounded-lg border-2 border-stone-500 bg-primary-300 px-2 py-1']) }}
        type="{{ $type }}" name="{{ $name }}"
        id="{{ $id }}" value="{{ $value }}" />
    <div>
        {{ $slot }}
    </div>
</div>
