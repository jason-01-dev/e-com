<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        {{-- ✅ AJOUT SWIPER CSS --}}
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        {{-- Pour le CSS spécifique à la page show.blade.php --}}
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
               @yield('content')
            </main>
        </div>
        
        {{-- ✅ AJOUT SWIPER JS --}}
        <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
        
        {{-- ✅ AJOUT STACK SCRIPTS pour le code JS du carrousel dans show.blade.php --}}
        @stack('scripts')
    </body>
</html>