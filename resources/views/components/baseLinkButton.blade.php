@props(['id' => '', 'type' => 'button'])
<button id="{{ $id }}" type="{{ $type }}"
    {{ $attributes->merge(['class' => 'outline-none border-0 bg-transparent border-none custom-button']) }}>
    {!! $slot !!}
</button>
