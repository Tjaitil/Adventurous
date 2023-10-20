@props(['id', 'current-value', 'max-value'])
@php
    /**
     * @param string $id
     * @param int $currentValue
     * @param int $maxValue
     */
@endphp
<div id="{{ $id }}" class="progressBarContainer w-100 mx-auto">
    <div class="progressBarOverlayShadow">
    </div>
    <div class="progressBarOverlay">
    </div>
    <div class="progressBar">
        <span class="progressBar_currentValue">{{ $currentValue }}</span>
        &nbsp/&nbsp
        <span class="progressBar_maxValue">{{ $maxValue }}</span>
    </div>
</div>
