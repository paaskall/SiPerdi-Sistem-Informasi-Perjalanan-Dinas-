<x-app-layout>
    @php
        $user      = Auth::user();
        $firstName = explode(' ', trim($user->name))[0];
        $roles     = $user->getRoleNames();

        // ==========================================
        // AKSES CEPAT PER ROLE
        // ==========================================
        $actions = [];

        if ($user->hasRole('pegawai')) {
            $actions[] = ['label' => 'Buat UMK Baru', 'desc' => 'Ajukan Uang Muka Kerja untuk perjalanan dinas baru.', 'route' => 'perjalanan.buat', 'icon' => 'M12 5v14M5 12h14', 'color' => '#1e3a8a'];
            $actions[] = ['label' => 'Riwayat UMK', 'desc' => 'Pantau status UMK dan cetak formulir UMK Anda.', 'route' => 'perjalanan.riwayat', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => '#059669'];
            $actions[] = ['label' => 'Laporan Saya', 'desc' => 'Isi dan pantau laporan hasil perjalanan dinas Anda.', 'route' => 'laporan.riwayat', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => '#d97706'];
        }

        if ($user->hasRole('keuangan')) {
            $actions[] = ['label' => 'Master Data', 'desc' => 'Kelola Divisi, Golongan, COA, Kota, dan Tarif.', 'route' => 'master.divisi', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4', 'color' => '#1e3a8a'];
            $actions[] = ['label' => 'Audit Trail', 'desc' => 'Lihat jejak audit semua aktivitas di sistem.', 'route' => 'audit.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => '#334155'];
        }

        if ($user->hasRole('pimpinan')) {
            $actions[] = ['label' => 'Audit Trail', 'desc' => 'Pantau semua aktivitas yang terjadi di sistem.', 'route' => 'audit.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => '#334155'];
        }

        $totalUmk = 0;
        $belumLapor = 0;
        $sudahLapor = 0;

        if ($user->hasRole('pegawai')) {
            $perjalananUser = \App\Models\PerjalananDinas::with('laporan')
                ->where('pegawai_id', $user->id)
                ->whereIn('status', ['PRINTED', 'RECEIVED_BY_FINANCE', 'PIMPINAN_SIGNED', 'COMPLETED'])
                ->get();
            $totalUmk = $perjalananUser->count();
            $belumLapor = $perjalananUser->filter(fn($p) => !$p->laporan)->count();
            $sudahLapor = $perjalananUser->filter(fn($p) => $p->laporan)->count();
        }

        $steps = [
            ['n' => 1, 'title' => 'Isi Form UMK',        'desc' => 'Pegawai melengkapi data perjalanan dan estimasi biaya.', 'roles' => ['pegawai']],
            ['n' => 2, 'title' => 'Cetak & Serahkan',    'desc' => 'Cetak PDF UMK, bawa ke Keuangan untuk diproses.', 'roles' => ['pegawai']],
            ['n' => 3, 'title' => 'Laporkan Hasil',      'desc' => 'Setelah perjalanan, isi laporan hasil dan unggah bukti.', 'roles' => ['pegawai']],
            ['n' => 4, 'title' => 'Selesai',             'desc' => 'Laporan tersimpan dan siap diarsipkan oleh Keuangan.', 'roles' => ['keuangan']],
        ];
    @endphp

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-900 via-blue-700 to-blue-500 p-6 text-white shadow-lg sm:p-8">
                <div class="relative z-10 max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-200">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Selamat datang, {{ $firstName }}!</h1>
                    <p class="mt-2 text-sm leading-relaxed text-blue-100 sm:text-base">Kelola Uang Muka Kerja (UMK) dan laporan perjalanan dinas Anda dalam satu tempat.</p>
                    @if ($roles->isNotEmpty())
                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <span class="text-xs text-blue-200">Peran Anda:</span>
                            @foreach ($roles as $r)
                                <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-semibold capitalize text-white ring-1 ring-inset ring-white/25">{{ $r }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
            </div>

            @if (count($actions))
                <section class="mb-8">
                    <h2 class="text-lg font-bold tracking-tight text-slate-900">Akses cepat</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Menu yang tersedia sesuai peran Anda.</p>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($actions as $a)
                            <a href="{{ route($a['route']) }}" class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                                <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl text-white shadow-lg" style="background-color: {{ $a['color'] }};">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $a['icon'] }}"/></svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block font-bold text-slate-900 group-hover:text-blue-900">{{ $a['label'] }}</span>
                                    <span class="mt-1 block text-sm leading-relaxed text-slate-500">{{ $a['desc'] }}</span>
                                </span>
                                <svg class="mt-1 h-5 w-5 flex-shrink-0 text-slate-300 transition-all group-hover:translate-x-0.5 group-hover:text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($user->hasRole('pegawai'))
                <section class="mb-8">
                    <h2 class="text-lg font-bold tracking-tight text-slate-900">Ringkasan UMK Anda</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Statistik perjalanan dinas yang sudah diproses.</p>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-medium text-slate-500">Total UMK Diproses</p><p class="mt-1 text-3xl font-extrabold text-blue-900">{{ $totalUmk }}</p></div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-medium text-slate-500">Perlu Dilaporkan</p><p class="mt-1 text-3xl font-extrabold text-amber-600">{{ $belumLapor }}</p></div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-medium text-slate-500">Sudah Dilaporkan</p><p class="mt-1 text-3xl font-extrabold text-emerald-600">{{ $sudahLapor }}</p></div>
                    </div>
                </section>
            @endif

            <section>
                <h2 class="text-lg font-bold tracking-tight text-slate-900">Alur perjalanan dinas</h2>
                <p class="mt-0.5 text-sm text-slate-500">Dari pembuatan UMK hingga laporan realisasi. Langkah bertanda biru adalah tugas Anda.</p>
                <ol class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($steps as $s)
                        @php $mine = count(array_filter($s['roles'], fn ($r) => $user->hasRole($r))) > 0; @endphp
                        <li class="relative rounded-2xl border p-5 shadow-sm {{ $mine ? 'border-blue-200 bg-blue-50/60' : 'border-slate-200 bg-white' }}">
                            <div class="flex items-center justify-between">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold {{ $mine ? 'bg-blue-900 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $s['n'] }}</span>
                                @if ($mine)<span class="rounded-full bg-blue-900 px-2.5 py-0.5 text-[11px] font-semibold text-white">Tugas Anda</span>@endif
                            </div>
                            <h3 class="mt-3 font-bold text-slate-900">{{ $s['title'] }}</h3>
                            <p class="mt-1 text-sm leading-relaxed text-slate-500">{{ $s['desc'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>
    </div>
</x-app-layout>