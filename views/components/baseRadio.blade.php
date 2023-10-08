@props(['name', 'value', 'id'])
@php
    /**
     * @param string $name
     * @param string $id
     * @param mixed $value
     */
@endphp
<label class="user-select-none relative my-1 cursor-pointer" for="{{ $id }}">
    {!! $slot !!}
    <input type="radio" name="{{ $name }}" id="{{ $id }}"
        value="{{ $value }}" class="peer absolute h-0 w-0 cursor-pointer opacity-0"
        {{ $attributes }}>
    <span
        class="relative inline-block h-5 w-5 rounded-full border-2 border-transparent bg-neutral-100 after:absolute after:left-[14%] after:top-[14%] after:hidden after:h-3 after:w-3 after:rounded-full after:border-b-[3px] after:border-r-[3px] after:border-transparent after:bg-orange-600 after:content-[''] hover:bg-gray-200 peer-checked:border-orange-600 peer-checked:drop-shadow-xl after:peer-checked:block"></span>
</label>
