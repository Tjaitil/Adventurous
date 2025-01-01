@props(['id', 'ariaLabelledBy', 'active' => false])
@php
    /**
     * @param string $id
     * @param string $tabId
     */
@endphp
<div @class([
    'tabpanel absolute w-full text-center text-white',
    'hidden' => !$active,
]) id="{{ $id }}" role="tabpanel"
    aria-labelledby="{{ $ariaLabelledBy }}">
    {!! $slot !!}
</div>
