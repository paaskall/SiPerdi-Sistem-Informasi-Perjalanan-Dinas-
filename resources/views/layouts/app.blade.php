<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'SIPERDI') }} - Sistem Informasi Perjalanan Dinas</title>
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/logo-lanri.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-700 antialiased selection:bg-blue-900 selection:text-white">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="border-b border-slate-200 bg-white">
                    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 sm:py-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Library untuk OCR & Kamera -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://unpkg.com/html5-qrcode"></script>

        <script>
            document.addEventListener('livewire:init', () => {
                // Menangkap notifikasi biasa
                Livewire.on('swal', (data) => {
                    const config = data[0] || data; 
                    Swal.fire({
                        title: config.title || '',
                        text: config.text || '',
                        icon: config.icon || 'info',
                        confirmButtonColor: '#1e3a8a',
                        ...config
                    });
                });

                // Menangkap konfirmasi
                Livewire.on('swal:confirm', (data) => {
                    const config = data[0] || data;
                    Swal.fire({
                        title: config.title,
                        text: config.text,
                        icon: config.icon || 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1e3a8a',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: config.confirmButtonText || 'Ya',
                        cancelButtonText: config.cancelButtonText || 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.dispatch(config.method, config.params);
                        }
                    });
                });
            });
        </script>
    </body>
</html>