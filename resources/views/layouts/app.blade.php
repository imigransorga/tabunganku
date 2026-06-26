<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Tabungan KIA') }}</title>

        {{-- set tema lebih awal agar tidak ada kedip (FOUC) --}}
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>

        @include('partials.pwa-head')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-b from-indigo-50/60 via-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header>
                    <div class="max-w-7xl mx-auto pt-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="animate-fade-up">
                {{ $slot }}
            </main>
        </div>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Toggle tema gelap/terang
            window.toggleTheme = function () {
                const html = document.documentElement;
                html.classList.toggle('dark');
                localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
            };

            // Toast notifikasi sukses
            @if (session('success'))
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: @json(session('success')),
                    showConfirmButton: false, timer: 3000, timerProgressBar: true,
                });
            @endif

            // Popup error (mis. melebihi budget)
            @if ($errors->any())
                Swal.fire({
                    icon: 'error', title: 'Oops...',
                    html: @json(implode('<br>', $errors->all())),
                    confirmButtonColor: '#4f46e5',
                });
            @endif

            // Konfirmasi untuk form dengan atribut data-confirm
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (!form.dataset.confirm) return;
                if (form.dataset.confirmed === '1') return;
                e.preventDefault();
                Swal.fire({
                    title: form.dataset.confirmTitle || 'Yakin?',
                    text: form.dataset.confirm,
                    icon: form.dataset.confirmIcon || 'warning',
                    showCancelButton: true,
                    confirmButtonColor: form.dataset.confirmColor || '#4f46e5',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: form.dataset.confirmYes || 'Ya, lanjut',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = '1';
                        form.submit();
                    }
                });
            }, true);
        </script>
    </body>
</html>
