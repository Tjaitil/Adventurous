@props(['id', 'tab-id'])
@php
    /**
     * @param string $id
     * @param string $tabId
     */
@endphp
<div class="tabpanel invisible absolute w-full text-center text-white"
    id="{{ $id }}" role="tabpanel" aria-labelledby="{{ $tabId }}">
    {!! $slot !!}
</div>
