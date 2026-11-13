<div class="py-8 sm:py-10">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="mb-5 flex" aria-label="Breadcrumb">
            <ol role="list" class="flex items-center space-x-2">
                @if(auth()->user()->hasRole('pegawai'))
                    <li>
                        <a href="{{ route('laporan.riwayat') }}" class="text-sm font-medium text-slate-500 hover:text-blue-900 transition-colors">
                            Laporan Saya
                        </a>
                    </li>
                @elseif(auth()->user()->hasRole('keuangan'))
                    <li>
                        <a href="{{ route('perjalanan.verifikasi-laporan') }}" class="text-sm font-medium text-slate-500 hover:text-blue-900 transition-colors">
                            Verifikasi Laporan
                        </a>
                    </li>
                @endif
                <li>
                    <div class="flex items-center">
                        <svg class="h-4 w-4 flex-shrink-0 text-slate-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-2 text-sm font-semibold text-slate-900" aria-current="page">Detail Laporan</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-6 flex items-center gap-4">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Detail Laporan Hasil</h2>
                <p class="mt-0.5 text-sm text-slate-500">UMK: {{ $laporan->perjalanan->nomor_pengajuan }}</p>
            </div>
        </div>

        <!-- Card Detail -->
        <div class="space-y-6">

            <!-- Status -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Status Laporan</span>
                    @if($laporan->status === 'SUBMITTED')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Menunggu Verifikasi
                        </span>
                    @elseif($laporan->status === 'VERIFIED')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Terverifikasi
                        </span>
                    @elseif($laporan->status === 'REJECTED')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-200">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Ditolak
                        </span>
                    @endif
                </div>
            </div>

            <!-- Data Pegawai -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">Data Pegawai</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div><span class="text-sm text-slate-500">Nama</span><p class="font-semibold text-slate-900">{{ $laporan->perjalanan->pegawai->name }}</p></div>
                    <div><span class="text-sm text-slate-500">NIP</span><p class="font-semibold text-slate-900">{{ $laporan->perjalanan->pegawai->nip ?? '-' }}</p></div>
                    <div><span class="text-sm text-slate-500">Jabatan</span><p class="font-semibold text-slate-900">{{ $laporan->perjalanan->pegawai->jabatan->nama ?? '-' }}</p></div>
                    <div><span class="text-sm text-slate-500">Divisi</span><p class="font-semibold text-slate-900">{{ $laporan->perjalanan->pegawai->divisi->nama ?? '-' }}</p></div>
                    <div><span class="text-sm text-slate-500">Golongan</span><p class="font-semibold text-slate-900">{{ $laporan->perjalanan->pegawai->golongan->nama ?? '-' }}</p></div>
                    <div><span class="text-sm text-slate-500">Pangkat</span><p class="font-semibold text-slate-900">{{ $laporan->perjalanan->pegawai->pangkat->nama ?? '-' }}</p></div>
                </div>
            </div>

            <!-- Informasi Perjalanan -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">Informasi Perjalanan</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div><span class="text-sm text-slate-500">Kota Tujuan</span><p class="font-semibold text-slate-900">{{ $laporan->perjalanan->kotaTujuan->nama }}</p></div>
                    <div><span class="text-sm text-slate-500">Maksud Perjalanan</span><p class="font-semibold text-slate-900">{{ $laporan->perjalanan->maksud_perjalanan }}</p></div>
                    <div><span class="text-sm text-slate-500">Tanggal Berangkat</span><p class="font-semibold text-slate-900">{{ $laporan->perjalanan->tanggal_berangkat->format('d F Y') }}</p></div>
                    <div><span class="text-sm text-slate-500">Tanggal Pulang</span><p class="font-semibold text-slate-900">{{ $laporan->perjalanan->tanggal_pulang->format('d F Y') }}</p></div>
                    <div><span class="text-sm text-slate-500">Lama Perjalanan</span><p class="font-semibold text-slate-900">{{ $laporan->perjalanan->jumlah_hari }} Hari</p></div>
                </div>
            </div>

            <!-- Laporan Hasil -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">Laporan Hasil</h3>
                <div class="space-y-4">
                    <div>
                        <span class="text-sm text-slate-500">Ringkasan Kegiatan</span>
                        <p class="mt-1 text-sm text-slate-800">{{ $laporan->ringkasan_kegiatan }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-slate-500">Hasil Perjalanan</span>
                        <p class="mt-1 text-sm text-slate-800">{{ $laporan->hasil_perjalanan }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-slate-500">Kendala</span>
                        <p class="mt-1 text-sm text-slate-800">{{ $laporan->kendala ?? 'Tidak ada kendala.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Biaya Riil -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">Biaya Riil</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div><span class="text-sm text-slate-500">Estimasi Awal</span><p class="font-semibold text-slate-900">Rp {{ number_format($laporan->perjalanan->total_estimasi, 0, ',', '.') }}</p></div>
                    <div><span class="text-sm text-slate-500">Total Riil</span><p class="font-semibold text-slate-900">Rp {{ number_format($laporan->total_biaya_riil, 0, ',', '.') }}</p></div>
                    <div>
                        <span class="text-sm text-slate-500">Selisih</span>
                        <p class="font-semibold {{ $laporan->total_biaya_riil - $laporan->perjalanan->total_estimasi > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            Rp {{ number_format($laporan->total_biaya_riil - $laporan->perjalanan->total_estimasi, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Lampiran -->
            @if($laporan->file_lampiran)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Lampiran</h3>
                    <a href="{{ asset('storage/' . $laporan->file_lampiran) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-900 hover:text-blue-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Lihat Lampiran
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>