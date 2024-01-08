<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel='shortcut icon' type='image/x-icon' href="{{ asset('favicon.ico') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Old+Standard+TT&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Martel&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/header.css" />
    <link rel="stylesheet" type="text/css" href="/css/layout.css" />
    <link rel="stylesheet" type="text/css" href="/css/progressbar.css" />
    <link rel="stylesheet" type="text/css" href="/css/warriorSelect.css" />
    <link rel="stylesheet" type="text/css" href="/css/battleresult.css" />
</head>

<body>
    <header class="col-span-6 row-start-1">
        @include('header')
    </header>
    @yield('content')
</body>

</html>
