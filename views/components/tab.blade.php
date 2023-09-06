@props(['id', 'tab_text', 'tab_group', 'target_id'])
@php
    /**
     * @var string $id
     * @param string $tabText
     * @param string $tabGroup
     * @param string $targetId
     */
@endphp
<button id="{{ $id }}" role="tab" aria-controls="{{ $targetId }}"
    class="{{ $tabGroup }}">
    {{ $tabText }}
</button>
