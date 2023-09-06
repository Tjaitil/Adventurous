@props(['is-active', 'notice-text'])
@php
    /**
     * @property bool $isActive
     * @property string $noticeText
     */
@endphp

@if ($isActive)
    <span class="text-green-700">{{ $noticeText }}</span>
@endif
