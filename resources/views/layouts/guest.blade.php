<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Tabungan KIA') }}</title>

        @include('partials.pwa-head')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center p-6
                    bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 relative overflow-hidden">

            {{-- ornamen lingkaran samar di latar --}}
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -right-16 w-96 h-96 bg-purple-300/20 rounded-full blur-3xl"></div>

            {{-- Brand --}}
            <a href="/" class="relative flex flex-col items-center mb-8">
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-white/15 backdrop-blur ring-1 ring-white/30 shadow-lg mb-3">
                    {{-- ikon celengan --}}
                    <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 12.5a4.5 4.5 0 0 0-2.5-4.03V6.5a1.5 1.5 0 0 0-2.56-1.06l-.7.7A8.7 8.7 0 0 0 12 5.5c-4.14 0-7.5 2.91-7.5 6.5 0 1.86.9 3.53 2.34 4.7L6 19.5h2.5l.5-1.27c.47.09.96.15 1.47.18l.53 1.09h2.5l.5-1.45c2.69-.9 4.5-3.06 4.5-5.55Z"/>
                        <circle cx="15.5" cy="11.5" r="1" fill="currentColor" stroke="none"/>
                        <path stroke-linecap="round" d="M3.5 11h1"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Tabungan KIA</h1>
                <p class="text-sm text-indigo-100/80 mt-1">Catat tabungan & keuanganmu</p>
            </a>

            {{-- Kartu form --}}
            <div class="relative w-full sm:max-w-md px-8 py-8 bg-white shadow-2xl rounded-2xl">
                {{ $slot }}
            </div>

            <p class="relative text-xs text-indigo-100/70 mt-6">&copy; {{ date('Y') }} Tabungan KIA</p>
        </div>
    </body>
</html>
