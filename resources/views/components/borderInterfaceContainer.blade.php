<div
    {{ $attributes->merge(['class' => "relative p-2 my-4 bg-primary-800 border-primary-700 text-white border-4 rounded border-default shadow-lg after:border-gray-950/60 after:pointer-events-none after:absolute after:top-0 after:left-0 after:content-[''] after:w-full after:h-full after:border-solid after:border-4 after:rounded"]) }}>
    {!! $slot !!}
</div>
