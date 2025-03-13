@extends('landingLayout')
@section('title', 'Welcome')
@section('content')
    <div class="absolute top-0 h-[200vh] w-full bg-[length:200%_200%]">
        <img src="{{ asset('images/7.5m.png') }}" width="3200px"
            class="absolute max-w-none transition duration-100 ease-linear" id="background_image" />
    </div>
    <section id="slot1" class="flex h-screen snap-start items-center justify-center">
        <div
            class="relative z-10 flex flex-row items-center justify-center rounded-md bg-primary-400 p-5 text-center shadow-lg shadow-black">
            <img src="{{ asset('images/adventurous_logo.png') }}" id="bac" width="150"
                class="image-crisp" />
            <h1 class="text-6xl" id="logo-text">Adventurous</h1>
        </div>
    </section>
    <section id="slot2" class="block h-screen snap-start">
        <div
            class="relative top-[50px] mx-auto h-52 w-1/2 rounded-lg bg-primary-400 p-1 text-center text-white shadow-lg shadow-black">
            <img src="{{ asset('images/adventurous_logo.png') }}" />
            <a href="/login" class="inline-block hover:bg-primary-200 border-outset mb-4 cursor-pointer rounded-md border-2 bg-primary-50 px-[12px] py-[10px] text-sm font-bold text-black shadow-xs shadow-amber-950">
                Login
            </a>
            <p>Registration is currently closed, contact kjetil@baksaas.no for access</p>
        </div>
    </section>
@endsection
