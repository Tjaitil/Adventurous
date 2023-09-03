@php
    $action_amount = $attributes['action-amount'];
@endphp
<li class="bg-primary-750 w-auto cursor-pointer p-1 hover:bg-stone-600"
    data-action-amount="{{ $action_amount }}">
    {!! $slot !!}
</li>
