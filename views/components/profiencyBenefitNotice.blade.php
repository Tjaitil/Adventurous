@php
    /**
     * @property bool $is_active
     * @property string $notice_text
     */
@endphp

@if ($is_active)
    <span class="text-green-700">{{ $notice_text }}</span>
@endif
