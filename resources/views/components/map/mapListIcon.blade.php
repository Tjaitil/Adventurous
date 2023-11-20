@props(['src', 'text'])
<li class="my-2"><img src="{{ asset('images/' . $src) }}" class="max-w-none">
    <span class="w-[32]">{{ ucwords($text) }}</span>
</li>
