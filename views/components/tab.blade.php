@props(['id', 'tabText' => '', 'tabGroup' => '', 'ariaControls', 'active' => false])
@php
    /**
     * @var string $id
     * @param string $tabText
     * @param string $tabGroup
     * @param string $targetId
     * @param boolean $active
     */
@endphp
<button id="{{ $id ?? '' }}" role="tab" aria-controls="{{ $ariaControls }}"
    data-tab-id="{{ $id }}" aria-selected="{{ $active ? 'true' : 'false' }}"
    {{ $attributes->merge(['type' => 'button', 'class' => $tabGroup . ' bg-orange-50', 'data-toggle' => 'tab']) }}>
    {{ $tabText }} {!! $slot !!}
</button>
