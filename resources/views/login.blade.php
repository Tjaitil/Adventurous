@extends('landingLayout')
@section('title', 'Login')
@section('content')
    <div class="absolute top-0 h-full w-full bg-[length:200%_200%]">
        <img src="{{ asset('images/5.7m.png') }}" width="3200"
            class="absolute h-full max-w-none object-cover transition duration-100 ease-linear" id="background_image" />
    </div>
    <div class="w-screen h-screen flex flex-row items-center justify-center">
        <div id="login"
            class="relative mx-auto w-1/2 rounded-lg bg-primary-400 p-1 text-center text-white shadow-lg shadow-black">
            <img src="{{ asset('images/adventurous_logo.png') }}" />
            <form method="post" action="/authenticate" class="flex flex-col items-center justify-center gap-3">
                @csrf
                <div class="max-w-md">
                    <x-baseInput name="email" labelText="Email" />
                </div>
                <div class="max-w-md">
                    <x-baseInput name="password" labelText="Password" type="password" minlength="4"
                        textAlignment="text-start" />
                </div>
                <p @class(['text-red-600', 'invisible' => !$errors->has('email')])>
                    Invalid credentials
                </p>
                <x-button type="submit" class="w-full max-w-md self-center">
                    Login
                </x-button>
                <p>Registration is currently closed, contact kjetil@baksaas.no for access</p>
            </form>
        </div>
    </div>
@endsection
