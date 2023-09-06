@props(['id' => null, 'text'])
@php
    /**
     * @param int $id
     * @param string $text
     */
@endphp
<button id="{{ $id }}"
    {{ $attributes->merge(['class' => 'cursor-pointer hover:bg-primary-200']) }}>
    {{ $text }}
</button>
