@props(['id' => null, 'text' => '', 'type' => 'button', 'style' => 'white'])
@php
    /**
     * @param int $id
     * @param string $text
     */
    if ($style == 'white') {
        $class = ' border-outset mb-4 rounded-md border-2 bg-primary-50 px-[12px] py-[10px] text-sm font-bold text-black shadow-xs shadow-amber-950';
    }
@endphp
<button id="{{ $id }}" type="{{ $type }}"
    {{ $attributes->merge(['class' => 'cursor-pointer hover:bg-primary-200' . $class]) }}>
    {{ $slot }}
</button>
