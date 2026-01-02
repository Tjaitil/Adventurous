<!DOCTYPE html>
<html lang="en" class="snap-y snap-mandatory overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel='shortcut icon' type='image/x-icon' href="{{ asset('favicon.ico') }}" />
    @vite(['resources/css/app.css'])
    @vite(['resources/js/backgroundScroller.ts'])
</head>

<body>
    @yield('content')
</body>

</html>
