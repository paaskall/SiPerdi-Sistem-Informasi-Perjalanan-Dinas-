<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SIPERDI - Sistem Informasi Perjalanan Dinas</title>
        <meta name="description" content="Kelola pengajuan, persetujuan, dan pelaporan perjalanan dinas LAN RI secara digital.">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

        <!-- Scripts & Styles (Tailwind & Vite) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        </style>
    </head>
    <body class="bg-white text-slate-700 antialiased selection:bg-blue-900 selection:text-white">

        <!-- Navbar -->
        <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:h-20 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3 sm:gap-4 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                    <img src="{{ asset('img/lan.png') }}" alt="Logo LAN RI" class="h-9 w-auto object-contain sm:h-11">
                    <span class="hidden h-8 w-px bg-slate-200 sm:block"></span>
                    <span class="flex flex-col leading-none">
                        <span class="text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">SIPERDI</span>
                        <span class="mt-1 hidden text-xs text-slate-500 sm:block">Sistem Informasi Perjalanan Dinas</span>
                    </span>
                </a>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center rounded-lg bg-blue-900 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 sm:px-5 sm:py-2.5">
                            Ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-800 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 sm:px-5 sm:py-2.5">
                            Login Pegawai
                        </a>
                    @endauth
                @endif
            </div>
        </header>

        <main>
            <!-- Hero -->
            <section class="relative isolate overflow-hidden bg-blue-950">
                <!-- Gambar latar -->
                <img src="{{ asset('img/gedung-lanri.png') }}" alt="Gedung LAN RI" class="absolute inset-0 -z-20 h-full w-full object-cover object-center">
                <!-- Overlay navy -->
                <div class="absolute inset-0 -z-10 bg-gradient-to-b from-blue-950/90 via-blue-950/75 to-blue-950/90 lg:bg-gradient-to-r lg:from-blue-950/95 lg:via-blue-950/75 lg:to-blue-950/30"></div>

                <div class="mx-auto flex min-h-[28rem] max-w-7xl items-center px-4 py-20 sm:min-h-[32rem] sm:px-6 sm:py-24 lg:min-h-[36rem] lg:px-8 lg:py-28">
                    <div class="mx-auto max-w-2xl text-center lg:mx-0 lg:text-left">
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-200 sm:text-sm">Lembaga Administrasi Negara RI</p>
                        <h1 class="mt-4 text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl">
                            Kelola perjalanan dinas dalam satu sistem
                        </h1>
                        <p class="mt-5 text-base leading-relaxed text-blue-100 sm:text-lg">
                            Ajukan, setujui, dan laporkan perjalanan dinas secara digital. Prosesnya lebih cepat, transparan, dan tanpa kertas.
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center lg:justify-start">
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-7 py-3 text-base font-semibold text-blue-900 transition-colors hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-blue-950">
                                Masuk ke SIPERDI
                            </a>
                            <a href="#alur" class="inline-flex items-center justify-center rounded-lg border border-white/40 px-7 py-3 text-base font-semibold text-white transition-colors hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-blue-950">
                                Lihat alur kerja
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Fitur -->
            <section class="bg-white py-16 sm:py-20 lg:py-24">
                <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Fitur utama</h2>
                        <p class="mt-3 text-base text-slate-600 sm:text-lg">Dirancang untuk memudahkan administrasi perjalanan dinas Anda.</p>
                    </div>

                    <div class="mt-12 grid grid-cols-1 gap-px overflow-hidden rounded-2xl border border-slate-200 bg-slate-200 md:grid-cols-3">
                        <!-- Fitur 1 -->
                        <div class="bg-white p-6 sm:p-8">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-50 text-blue-900">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h3 class="mt-5 text-lg font-bold text-slate-900">Pengajuan digital</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">Buat pengajuan perjalanan dinas dengan rincian estimasi biaya yang terstruktur, tanpa kertas fisik.</p>
                        </div>

                        <!-- Fitur 2 -->
                        <div class="bg-white p-6 sm:p-8">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-50 text-blue-900">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="mt-5 text-lg font-bold text-slate-900">Persetujuan berjenjang</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">Alur persetujuan dari Keuangan hingga Pimpinan, dilengkapi notifikasi di setiap tahap.</p>
                        </div>

                        <!-- Fitur 3 -->
                        <div class="bg-white p-6 sm:p-8">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-50 text-blue-900">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            </div>
                            <h3 class="mt-5 text-lg font-bold text-slate-900">SPPD dalam format PDF</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">Cetak Surat Perintah Perjalanan Dinas otomatis, lengkap dengan tanda tangan digital pimpinan.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Alur kerja -->
            <section id="alur" class="scroll-mt-20 border-t border-slate-200 bg-slate-50 py-16 sm:py-20 lg:py-24">
                <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Alur kerja</h2>
                        <p class="mt-3 text-base text-slate-600 sm:text-lg">Dari pengajuan sampai surat tugas terbit, dalam empat langkah.</p>
                    </div>

                    <ol class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
                        <li class="border-t-2 border-blue-900 pt-5">
                            <span class="text-sm font-bold text-blue-900">1</span>
                            <h3 class="mt-1 text-base font-bold text-slate-900">Buat pengajuan</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">Pegawai mengisi tujuan, jadwal, dan estimasi biaya perjalanan.</p>
                        </li>
                        <li class="border-t-2 border-blue-900 pt-5">
                            <span class="text-sm font-bold text-blue-900">2</span>
                            <h3 class="mt-1 text-base font-bold text-slate-900">Verifikasi Keuangan</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">Bagian Keuangan memeriksa kelengkapan dan kewajaran anggaran.</p>
                        </li>
                        <li class="border-t-2 border-blue-900 pt-5">
                            <span class="text-sm font-bold text-blue-900">3</span>
                            <h3 class="mt-1 text-base font-bold text-slate-900">Persetujuan Pimpinan</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">Pimpinan menyetujui pengajuan dan membubuhkan tanda tangan digital.</p>
                        </li>
                        <li class="border-t-2 border-blue-900 pt-5">
                            <span class="text-sm font-bold text-blue-900">4</span>
                            <h3 class="mt-1 text-base font-bold text-slate-900">Unduh SPPD</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">Surat Perintah Perjalanan Dinas siap diunduh dalam format PDF.</p>
                        </li>
                    </ol>
                </div>
            </section>

            <!-- CTA -->
            <section class="bg-blue-900">
                <div class="mx-auto flex max-w-6xl flex-col items-center gap-6 px-4 py-12 text-center sm:px-6 md:flex-row md:justify-between md:text-left lg:px-8">
                    <div>
                        <h2 class="text-xl font-bold text-white sm:text-2xl">Siap mengajukan perjalanan dinas?</h2>
                        <p class="mt-1 text-sm text-blue-100 sm:text-base">Masuk dengan akun pegawai Anda untuk memulai.</p>
                    </div>
                    <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-white px-7 py-3 text-base font-semibold text-blue-900 transition-colors hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-blue-900 sm:w-auto">
                        Masuk ke SIPERDI
                    </a>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl flex-col items-center gap-4 px-4 py-8 text-center sm:px-6 md:flex-row md:justify-between md:text-left lg:px-8">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/lan.png') }}" alt="Logo LAN RI" class="h-8 w-auto">
                    <span class="text-sm text-slate-600">&copy; {{ date('Y') }} Lembaga Administrasi Negara. Seluruh hak dilindungi.</span>
                </div>
                <span class="text-sm font-medium text-slate-500">SIPERDI &ndash; Sistem Informasi Perjalanan Dinas</span>
            </div>
        </footer>
    </body>
</html>