<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIPERDI</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo-lanri.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui'] }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 text-slate-700 font-sans antialiased selection:bg-blue-900 selection:text-white">

    <div class="flex min-h-screen flex-col lg:flex-row">

        <!-- KIRI: Gambar Gedung (Hanya Desktop) -->
        <div class="relative hidden w-1/2 lg:block">
            <img class="absolute inset-0 h-full w-full object-cover" src="{{ asset('img/gedung-lanri.png') }}" alt="Gedung LAN RI">
            <div class="absolute inset-0 bg-gradient-to-t from-blue-950/95 via-blue-950/70 to-blue-950/30"></div>

            <div class="absolute inset-0 flex flex-col justify-between p-12 lg:p-16">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('img/lan.png') }}" alt="Logo LAN RI" class="h-14 w-auto object-contain drop-shadow-lg">
                    <span class="text-3xl font-extrabold tracking-tight text-white drop-shadow-md">SIPERDI</span>
                </div>

                <div class="max-w-lg mb-12">
                    <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                        Kelola perjalanan dinas dalam satu sistem.
                    </h2>
                    <p class="mt-4 text-lg text-blue-100/90">
                        Ajukan, setujui, dan laporkan perjalanan dinas secara digital. Prosesnya lebih cepat, transparan, dan tanpa kertas.
                    </p>
                </div>

                <div class="text-sm text-blue-200/80">
                    &copy; {{ date('Y') }} Lembaga Administrasi Negara RI. Seluruh hak dilindungi.
                </div>
            </div>
        </div>

        <!-- KANAN: Form Login -->
        <div class="flex w-full flex-col justify-center px-6 py-12 lg:w-1/2 lg:px-20 xl:px-24">

            <div class="mx-auto w-full max-w-sm lg:max-w-md">

                <!-- HEADER MOBILE -->
                <div class="mb-8 flex flex-col items-center text-center lg:hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('img/lan.png') }}" alt="Logo LAN RI" class="h-10 w-auto">
                        <span class="text-2xl font-extrabold tracking-tight text-slate-900">SIPERDI</span>
                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-blue-900 ring-4 ring-white shadow-sm mb-4">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900">Selamat Datang</h2>
                    <p class="mt-1 text-sm text-slate-500">Silakan masuk ke akun Anda.</p>
                </div>

                <!-- HEADER DESKTOP -->
                <div class="hidden lg:block mb-10">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-blue-900 ring-1 ring-blue-100">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-extrabold text-slate-900">Selamat Datang Kembali</h2>
                    <p class="mt-2 text-base text-slate-500">Silakan masuk ke akun Anda untuk melanjutkan.</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-emerald-600 bg-emerald-50 border border-emerald-200 p-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- FORM -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Input Username/NIP/Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-800 mb-1.5">
                            NIP / Email / Username
                        </label>
                        <input id="email"
                               name="email"
                               type="text"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="username"
                               placeholder="Masukkan NIP, email, atau username"
                               class="block w-full rounded-xl border bg-white px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 transition-shadow @error('email') border-red-400 @else border-slate-300 @enderror">

                        @error('email')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Input Password -->
                    <div x-data="{ showPassword: false }">
                        <label for="password" class="block text-sm font-semibold text-slate-800 mb-1.5">
                            Password
                        </label>
                        <div class="relative">
                            <input id="password"
                                   name="password"
                                   x-bind:type="showPassword ? 'text' : 'password'"
                                   required
                                   autocomplete="current-password"
                                   placeholder="Masukkan password"
                                   class="block w-full rounded-xl border bg-white pl-4 pr-12 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 transition-shadow @error('password') border-red-400 @else border-slate-300 @enderror">

                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-700 focus:outline-none">
                                <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <svg x-show="!showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-900 shadow-sm focus:ring-blue-900 cursor-pointer w-4 h-4" name="remember">
                            <span class="ms-2 text-sm text-slate-600 font-medium">Ingat Saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-900 hover:text-blue-700">
                                Lupa Password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit -->
                    <div class="pt-2">
                        <button type="submit" class="w-full flex items-center justify-center rounded-xl bg-blue-900 px-5 py-4 text-base font-bold text-white transition-colors hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:ring-offset-2 shadow-lg shadow-blue-900/30">
                            Masuk ke Sistem
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- ============================================================
         SWEETALERT SCRIPT — LOGIN / LOGOUT / FLASH MESSAGES
         ============================================================ -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swal === 'undefined') {
            console.warn('[SweetAlert] Library belum ter-load.');
            return;
        }

        // ====== 1. LOGOUT / LOGIN SUKSES ======
        @if (session('message'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('message')),
                timer: 3000,
                showConfirmButton: false,
                customClass: { popup: 'font-sans rounded-2xl' }
            });
        @endif

        // ====== 2. STATUS INFO (fallback) ======
        @if (session('status'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: @json(session('status')),
                confirmButtonColor: '#1e3a8a',
                confirmButtonText: 'OK',
                customClass: { popup: 'font-sans rounded-2xl' }
            });
        @endif

        // ====== 3. ERROR LOGIN / RATE LIMIT ======
        @if ($errors->has('email'))
            @php
                $errorMsg = $errors->first('email');
                $isRateLimited = str_contains($errorMsg, 'Terlalu banyak')
                    || str_contains($errorMsg, 'detik')
                    || str_contains($errorMsg, 'Too many')
                    || str_contains($errorMsg, 'seconds');
            @endphp

            @if ($isRateLimited)
                Swal.fire({
                    icon: 'error',
                    title: 'Akses Diblokir Sementara',
                    text: @json($errorMsg),
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Mengerti',
                    customClass: { popup: 'font-sans rounded-2xl' }
                });
            @else
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: @json($errorMsg),
                    confirmButtonColor: '#1e3a8a',
                    confirmButtonText: 'Coba Lagi',
                    customClass: { popup: 'font-sans rounded-2xl' }
                });
            @endif
        @endif

        // ====== 4. ERROR PASSWORD (kalau ada) ======
        @if ($errors->has('password'))
            Swal.fire({
                icon: 'error',
                title: 'Password Kosong',
                text: @json($errors->first('password')),
                confirmButtonColor: '#1e3a8a',
                customClass: { popup: 'font-sans rounded-2xl' }
            });
        @endif
    });
    </script>
</body>
</html>