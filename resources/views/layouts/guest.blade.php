<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIPERDI') }} - Sistem Informasi Perjalanan Dinas</title>
        <link rel="icon" href="{{ asset('assets/logo-lanri.png') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        </style>
    </head>
    <body class="bg-white text-slate-700 antialiased selection:bg-blue-900 selection:text-white">
        <div class="grid min-h-screen lg:grid-cols-2">

            <!-- Panel kiri (desktop saja) -->
            <aside class="relative isolate hidden overflow-hidden bg-blue-950 lg:flex lg:flex-col lg:justify-between lg:p-12">
                <img src="{{ asset('img/gedung-lanri.png') }}" alt="" aria-hidden="true" class="absolute inset-0 -z-20 h-full w-full object-cover object-center">
                <div class="absolute inset-0 -z-10 bg-gradient-to-b from-blue-950/90 via-blue-950/70 to-blue-950/95"></div>

                <a href="{{ url('/') }}" class="flex w-fit items-center gap-4 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-blue-950">
                    <img src="{{ asset('img/lan.png') }}" alt="Logo LAN RI" class="h-12 w-auto object-contain brightness-0 invert">
                    <span class="flex flex-col leading-none">
                        <span class="text-xl font-extrabold tracking-tight text-white">SIPERDI</span>
                        <span class="mt-1 text-xs text-blue-200">Lembaga Administrasi Negara RI</span>
                    </span>
                </a>

                <div class="max-w-md">
                    <h2 class="text-3xl font-extrabold leading-tight tracking-tight text-white xl:text-4xl">
                        Kelola perjalanan dinas dalam satu sistem
                    </h2>
                    <p class="mt-4 text-base leading-relaxed text-blue-100">
                        Ajukan, setujui, dan laporkan perjalanan dinas secara digital, lebih cepat dan tanpa kertas.
                    </p>
                </div>

                <p class="text-sm text-blue-200">&copy; {{ date('Y') }} Lembaga Administrasi Negara</p>
            </aside>

            <!-- Panel kanan: form -->
            <main class="grid min-h-screen grid-rows-[1fr_auto_1fr] bg-slate-50 lg:bg-white">
                <!-- Logo (mobile saja, rata kiri) -->
                <div class="row-start-1 flex items-center justify-start self-start border-b border-slate-200 bg-white px-4 py-4 sm:px-6 lg:hidden">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                        <img src="{{ asset('img/lan.png') }}" alt="Logo LAN RI" class="h-9 w-auto object-contain">
                        <span class="h-7 w-px bg-slate-200"></span>
                        <span class="text-lg font-extrabold tracking-tight text-slate-900">SIPERDI</span>
                    </a>
                </div>

                <!-- Kartu login (tepat di tengah layar) -->
                <div class="row-start-2 flex justify-center px-4 py-8 sm:px-6">
                    <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 lg:border-0 lg:p-0">
                        {{ $slot }}
                    </div>
                </div>

                <p class="row-start-3 self-end px-4 pb-6 text-center text-xs text-slate-500 lg:hidden">&copy; {{ date('Y') }} Lembaga Administrasi Negara</p>
            </main>

        </div>
    </body>
</html>