<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - NM 23 Artificial Intelligence</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="icon" type="image/png" href="{{ asset('Icon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('Icon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('Icon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('Icon/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="NM23" />
    <link rel="manifest" href="{{ asset('Icon/site.webmanifest') }}" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .background {
            background-image: url({{ asset('assets/4402948_18300.jpg') }})
        }
    </style>
</head>

<body class="background font-sans text-gray-900 antialiased bg-cover bg-center">
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-black/30 backdrop-blur-sm px-5">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden rounded-lg mx-10">
            <h1 class="text-center text-2xl font-bold my-5 uppercase">@yield('header')</h1>
            @yield('content')
        </div>

        <img src="{{ asset('Icon/favicon-96x96.png') }}" alt="Logo NM Ai" class="absolute bottom-5 opacity-50">
    </div>
</body>

</html>
