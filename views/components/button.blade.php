@php
    /**
     * @param string $id
     * @param string $text
     */
@endphp
<button @if ($id) id="{{ $id }}" @endif class="hover:bg-primary-200 cursor-pointer">
    {{ $text }}
</button>
