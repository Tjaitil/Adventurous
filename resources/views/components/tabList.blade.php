@props(['dataIsSetup' => 'false'])
<div {{ $attributes }} role="tablist" data-is-setup="{{ $dataIsSetup }}">
    {!! $slot !!}
</div>
