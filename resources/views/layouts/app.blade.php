<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GoLaundry') }}</title>

        <x-favicon />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Sky blue gradient + soft bubbles (walang image file na kailangan) */
            .laundry-bg-pattern {
                background-color: #d6ebfa;
                background-image:
                    radial-gradient(circle 90px at 8% 15%, rgba(255,255,255,0.22) 0, rgba(255,255,255,0.28) 88%, rgba(255,255,255,0.75) 93%, rgba(255,255,255,0) 96%),
                    radial-gradient(circle 40px at 18% 40%, rgba(255,255,255,0.22) 0, rgba(255,255,255,0.28) 88%, rgba(255,255,255,0.75) 93%, rgba(255,255,255,0) 96%),
                    radial-gradient(circle 140px at 88% 12%, rgba(255,255,255,0.22) 0, rgba(255,255,255,0.28) 88%, rgba(255,255,255,0.75) 93%, rgba(255,255,255,0) 96%),
                    radial-gradient(circle 55px at 78% 35%, rgba(255,255,255,0.22) 0, rgba(255,255,255,0.28) 88%, rgba(255,255,255,0.75) 93%, rgba(255,255,255,0) 96%),
                    radial-gradient(circle 110px at 92% 72%, rgba(255,255,255,0.22) 0, rgba(255,255,255,0.28) 88%, rgba(255,255,255,0.75) 93%, rgba(255,255,255,0) 96%),
                    radial-gradient(circle 35px at 70% 85%, rgba(255,255,255,0.22) 0, rgba(255,255,255,0.28) 88%, rgba(255,255,255,0.75) 93%, rgba(255,255,255,0) 96%),
                    radial-gradient(circle 80px at 6% 82%, rgba(255,255,255,0.22) 0, rgba(255,255,255,0.28) 88%, rgba(255,255,255,0.75) 93%, rgba(255,255,255,0) 96%),
                    radial-gradient(circle 50px at 30% 92%, rgba(255,255,255,0.22) 0, rgba(255,255,255,0.28) 88%, rgba(255,255,255,0.75) 93%, rgba(255,255,255,0) 96%),
                    radial-gradient(circle 28px at 50% 10%, rgba(255,255,255,0.22) 0, rgba(255,255,255,0.28) 88%, rgba(255,255,255,0.75) 93%, rgba(255,255,255,0) 96%),
                    radial-gradient(circle 65px at 96% 45%, rgba(255,255,255,0.22) 0, rgba(255,255,255,0.28) 88%, rgba(255,255,255,0.75) 93%, rgba(255,255,255,0) 96%),
                    radial-gradient(ellipse at top left, rgba(2, 132, 199, 0.16), rgba(2, 132, 199, 0) 55%),
                    linear-gradient(135deg, #e8f4fd 0%, #cfe8fa 45%, #b3dbf5 100%);
                background-attachment: fixed;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen laundry-bg-pattern">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>