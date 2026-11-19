<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'SIPERDI') }} - Sistem Informasi Perjalanan Dinas</title>
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo-lanri.png') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Load Vite (CSS aja biar aman) -->
        @vite(['resources/css/app.css'])

        <!-- WAJIB: Livewire Styles -->
        @livewireStyles

        <style>
            body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
            /* Ruang untuk bottom bar di HP (tidak bergantung build Tailwind) */
            @media (max-width: 1023.98px) {
                .app-main { padding-bottom: calc(64px + env(safe-area-inset-bottom, 0px) + 16px); }
            }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-700 antialiased selection:bg-blue-900 selection:text-white">
        <div class="min-h-screen">
            @include('layouts.navigation')

            {{-- Form logout (dipakai tombol "Keluar" di navbar desktop & bottom bar mobile) --}}
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
            </form>

            @isset($header)
                <header class="border-b border-slate-200 bg-white">
                    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 sm:py-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- .app-main memberi ruang di HP supaya konten paling bawah tidak tertutup bottom bar --}}
            <main class="app-main">
                {{ $slot }}
            </main>
        </div>

        <!-- Library Tambahan -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://unpkg.com/html5-qrcode"></script>

        <!-- WAJIB: LIVEWIRE (sudah membawa Alpine.js) -->
        @livewireScripts

        <!-- Script Custom -->
        <script>
            document.addEventListener('livewire:init', () => {
                // Notifikasi dari Livewire
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

                // Konfirmasi dari Livewire
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

            // FLASH MESSAGE LARAVEL (muncul setelah redirect)
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof Swal === 'undefined') return;

                @if (session('message'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: @json(session('message')),
                        timer: 3000,
                        showConfirmButton: false
                    });
                @endif

                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: @json(session('error')),
                        confirmButtonColor: '#1e3a8a'
                    });
                @endif

                @if (session('status'))
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        text: @json(session('status')),
                        confirmButtonColor: '#1e3a8a',
                        confirmButtonText: 'OK'
                    });
                @endif
            });
        </script>
    </body>
</html>