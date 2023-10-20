@props(['id' => null, 'text' => '', 'type' => 'button'])
@php
    /**
     * @param int $id
     * @param string $text
     */
@endphp
<button id="{{ $id }}" type="{{ $type }}"
    {{ $attributes->merge(['class' => 'cursor-pointer hover:bg-primary-200']) }}>
    {{ $slot }}
</button>
