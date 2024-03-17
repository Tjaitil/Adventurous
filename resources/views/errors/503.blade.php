@extends('landingLayout')
@section('title', 'Adventurous down')
@section('content')
    <div class="absolute top-0 h-full w-full bg-[length:200%_200%]">
        <img src="{{ asset('images/5.7m.png') }}" width="3200"
            class="absolute h-full max-w-none object-cover transition duration-100 ease-linear" id="background_image" />
    </div>
    <div class="w-screen h-screen flex flex-row items-center justify-center">
        <div id="login"
            class="relative md:mx-auto mx-4 w-full md:w-1/2 rounded-lg bg-primary-400 p-1 text-center text-white shadow-lg shadow-black">
            <h1 class="text-3xl">Adventurous is down for manintenace</h1>
            <h2 class="text-xl">We'll be back shortly</h2>
        </div>
    </div>
@endsection
