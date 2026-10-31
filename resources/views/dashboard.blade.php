<x-app-layout>
    @php
        $user      = Auth::user();
        $firstName = explode(' ', trim($user->name))[0];
        $roles     = $user->getRoleNames();

        // Akses cepat per peran
        $actions = [
            ['role' => 'pegawai',  'label' => 'Buat Pengajuan',      'desc' => 'Ajukan perjalanan dinas baru beserta estimasi biayanya.', 'route' => 'perjalanan.buat',        'icon' => 'M12 5v14M5 12h14'],
            ['role' => 'pegawai',  'label' => 'Riwayat Saya',        'desc' => 'Pantau status pengajuan, unduh SPPD, dan laporkan realisasi.', 'route' => 'perjalanan.riwayat',     'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['role' => 'keuangan', 'label' => 'Persetujuan',         'desc' => 'Tinjau pengajuan yang masuk, lalu setujui atau tolak.',      'route' => 'perjalanan.persetujuan', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['role' => 'keuangan', 'label' => 'Reimbursement',       'desc' => 'Periksa laporan realisasi biaya beserta struknya.',          'route' => 'reimbursement.index',    'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
            ['role' => 'pimpinan', 'label' => 'Tanda Tangan SPPD',   'desc' => 'Tanda tangani SPPD yang sudah disetujui Keuangan.',          'route' => 'perjalanan.pimpinan',    'icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'],
            ['role' => 'pimpinan', 'label' => 'Reimbursement',       'desc' => 'Tinjau dan setujui laporan realisasi biaya perjalanan.',     'route' => 'reimbursement.index',    'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
        ];
        $actions = array_values(array_filter($actions, fn ($a) => $user->hasRole($a['role'])));

        $masters = [
            ['Divisi', 'master.divisi'], ['Golongan', 'master.golongan'], ['COA', 'master.coa'],
            ['Kota', 'master.kota'], ['Tarif Harian', 'master.tarif'],
        ];
        $showMaster = $user->hasRole('keuangan');

        // Alur kerja; "roles" = peran yang bertugas pada langkah itu
        $steps = [
            ['n' => 1, 'title' => 'Buat pengajuan',       'desc' => 'Pegawai mengisi tujuan, jadwal, dan estimasi biaya.',          'roles' => ['pegawai']],
            ['n' => 2, 'title' => 'Verifikasi Keuangan',  'desc' => 'Keuangan memeriksa kelengkapan dan kewajaran anggaran.',       'roles' => ['keuangan']],
            ['n' => 3, 'title' => 'Tanda tangan Pimpinan','desc' => 'Pimpinan mengesahkan dan SPPD terbit dalam format PDF.',       'roles' => ['pimpinan']],
            ['n' => 4, 'title' => 'Laporan realisasi',    'desc' => 'Pegawai melaporkan biaya riil beserta struk untuk ditinjau.',  'roles' => ['pegawai', 'keuangan', 'pimpinan']],
        ];
    @endphp

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Banner sambutan -->
            <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-900 via-blue-700 to-blue-500 p-6 text-white shadow-lg sm:p-8">
                <div class="relative z-10 max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-200">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Selamat datang, {{ $firstName }}!</h1>
                    <p class="mt-2 text-sm leading-relaxed text-blue-100 sm:text-base">
                        Kelola pengajuan, persetujuan, dan pelaporan perjalanan dinas dalam satu tempat.
                    </p>

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
                <div class="pointer-events-none absolute -bottom-20 -right-32 h-80 w-80 rounded-full bg-blue-400/20 blur-3xl"></div>
            </div>

            <!-- Akses cepat -->
            @if (count($actions))
                <section class="mb-8">
                    <h2 class="text-lg font-bold tracking-tight text-slate-900">Akses cepat</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Menu yang tersedia sesuai peran Anda.</p>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($actions as $a)
                            <a href="{{ route($a['route']) }}"
                               class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                                <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20">
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

            <!-- Master data (Keuangan) -->
            @if ($showMaster)
                <section class="mb-8">
                    <h2 class="text-lg font-bold tracking-tight text-slate-900">Master data</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Data acuan yang dipakai pada pengajuan dan perhitungan biaya.</p>

                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                        @foreach ($masters as [$label, $route])
                            <a href="{{ route($route) }}"
                               class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                                {{ $label }}
                                <svg class="h-4 w-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Alur kerja -->
            <section>
                <h2 class="text-lg font-bold tracking-tight text-slate-900">Alur perjalanan dinas</h2>
                <p class="mt-0.5 text-sm text-slate-500">Dari pengajuan hingga laporan realisasi. Langkah bertanda biru adalah tugas Anda.</p>

                <ol class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($steps as $s)
                        @php $mine = count(array_filter($s['roles'], fn ($r) => $user->hasRole($r))) > 0; @endphp
                        <li class="relative rounded-2xl border p-5 shadow-sm {{ $mine ? 'border-blue-200 bg-blue-50/60' : 'border-slate-200 bg-white' }}">
                            <div class="flex items-center justify-between">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold {{ $mine ? 'bg-blue-900 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $s['n'] }}</span>
                                @if ($mine)
                                    <span class="rounded-full bg-blue-900 px-2.5 py-0.5 text-[11px] font-semibold text-white">Tugas Anda</span>
                                @endif
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