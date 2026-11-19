@php
    // ---------- Data & helper (didefinisikan sekali, dipakai ulang) ----------
    $dokumenList = collect([
        ['UMK', 'Uang Muka Kerja (UMK)', 'umk.cetak', 'umk.excel'],
        ['RIN', 'Rincian Biaya', 'umk.rincian-pdf', 'umk.rincian-excel'],
        ['KWT', 'Kwitansi', 'umk.kwitansi-pdf', 'umk.kwitansi-excel'],
        ['SPD', 'Surat Perjalanan Dinas', 'umk.spd-pdf', 'umk.spd-excel'],
        ['DPR', 'Daftar Pengeluaran Riil', 'umk.dpr-pdf', 'umk.dpr-excel'],
        ['AMP', 'Amplop Perjalanan', 'umk.amplop-pdf', 'umk.amplop-excel'],
    ])->map(fn ($d) => [
        'kode' => $d[0],
        'label' => $d[1],
        'pdf' => route($d[2], 'ID_PLACEHOLDER'),
        'xls' => route($d[3], 'ID_PLACEHOLDER'),
    ])->values();

    $statusMeta = fn ($status) => match ($status) {
        'DRAFT' => ['Draft', 'bg-slate-100 text-slate-700 ring-slate-200'],
        'PRINTED' => ['Sudah Dicetak', 'bg-amber-50 text-amber-700 ring-amber-200'],
        'RECEIVED_BY_FINANCE' => ['Diterima Keuangan', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        'REJECTED' => ['Ditolak', 'bg-red-50 text-red-700 ring-red-200'],
        'PIMPINAN_SIGNED' => ['Disetujui Pimpinan', 'bg-blue-50 text-blue-700 ring-blue-200'],
        'COMPLETED' => ['Selesai', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        default => [$status, 'bg-slate-100 text-slate-700 ring-slate-200'],
    };

    $statusBisaLapor = ['PRINTED', 'RECEIVED_BY_FINANCE', 'PIMPINAN_SIGNED', 'COMPLETED'];
    $rp = fn ($n) => 'Rp ' . number_format((float) ($n ?? 0), 0, ',', '.');


    $stepTitles = [1 => 'Info perjalanan', 2 => 'Rincian biaya', 3 => 'Konfirmasi'];
@endphp

<div class="py-6 sm:py-10"
     x-data="{
        dokumenOpen: false,
        activeId: null,
        activeNomor: '',
        docs: @js($dokumenList),
        bukaDokumen(id, nomor) { this.activeId = id; this.activeNomor = nomor; this.dokumenOpen = true; },
        url(tpl) { return tpl.replace('ID_PLACEHOLDER', this.activeId); }
     }"
     x-effect="document.body.classList.toggle('umk-lock', dokumenOpen || $wire.showEditModal)">

    <style>
        /* ====== Modal UMK: CSS biasa (tidak bergantung build Tailwind) ====== */
        .swal2-container { z-index: 20000 !important; }
        body.umk-lock { overflow: hidden; }

        /* Kartu statistik: mobile ditumpuk, desktop 3 kolom sejajar */
        .umk-stats { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin-bottom: 24px; }
        @media (min-width: 640px) { .umk-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; } }

        /* Overlay: modal SELALU di tengah. Di HP/tablet (bottom nav & header tampil) area
           atas/bawah disisakan setinggi header (±64px) & bottom nav (64px) + safe area,
           jadi modal tidak mungkin tertimpa meski z-index layout lebih tinggi. */
        .umk-ov {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            box-sizing: border-box; padding: 16px;
            background: rgba(15, 23, 42, .6);
        }
        @media (max-width: 1023.98px) {
            .umk-ov {
                padding: calc(72px + env(safe-area-inset-top, 0px)) 12px calc(76px + env(safe-area-inset-bottom, 0px));
            }
        }

        .umk-card {
            position: relative; display: flex; flex-direction: column;
            width: 100%; max-height: 100%; box-sizing: border-box;
            background: #fff; border-radius: 16px; overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .35);
        }
        .umk-card-sm { max-width: 480px; }
        .umk-card-lg { max-width: 768px; }

        .umk-head { flex: none; padding: 14px 16px 12px; border-bottom: 1px solid #e2e8f0; background: #fff; }
        .umk-head-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .umk-h { margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; }
        .umk-sub { margin: 2px 0 0; font-size: 12px; color: #64748b; word-break: break-word; }
        .umk-x { flex: none; width: 36px; height: 36px; margin: -4px -6px 0 0; display: flex; align-items: center; justify-content: center; border: 0; border-radius: 9999px; background: none; color: #64748b; cursor: pointer; }
        .umk-x:hover { background: #f1f5f9; }
        .umk-steps { display: flex; gap: 6px; margin-top: 10px; }
        .umk-steps i { flex: 1; height: 6px; border-radius: 9999px; background: #e2e8f0; transition: background .2s; }
        .umk-steps i.on { background: #1e3a8a; }

        .umk-body { flex: 1 1 auto; min-height: 0; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch; padding: 16px; background: #f8fafc; }
        .umk-foot { flex: none; padding: 12px 16px; border-top: 1px solid #e2e8f0; background: #fff; }
        .umk-foot-in { display: flex; flex-direction: column; gap: 10px; }
        .umk-total { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; }
        .umk-total span { font-size: 12px; font-weight: 500; color: #64748b; }
        .umk-total strong { font-size: 18px; font-weight: 800; color: #0f172a; }
        .umk-btns { display: flex; gap: 10px; }

        .umk-btn { flex: 1 1 0%; min-height: 48px; padding: 0 16px; border: 0; border-radius: 12px; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; transition: background .15s, opacity .15s; }
        .umk-btn-g { background: #f1f5f9; color: #334155; }
        .umk-btn-g:hover { background: #e2e8f0; }
        .umk-btn-p { flex: 2 1 0%; background: #1e3a8a; color: #fff; }
        .umk-btn-p:hover { background: #1e40af; }
        .umk-btn:disabled { opacity: .6; cursor: wait; }

        .umk-form { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 12px; padding: 16px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
        .umk-full { grid-column: 1 / -1; }
        .umk-lb { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 600; color: #1e293b; }
        .umk-lb-s { font-size: 12px; color: #334155; }
        .umk-in { display: block; width: 100%; box-sizing: border-box; min-height: 46px; padding: 10px 14px; font: inherit; font-size: 16px; color: #0f172a; background: #fff; border: 1px solid #cbd5e1; border-radius: 12px; outline: none; }
        .umk-in:focus { border-color: #1d4ed8; box-shadow: 0 0 0 3px rgba(29, 78, 216, .15); }
        .umk-in:disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }
        .umk-er { margin: 6px 0 0; font-size: 12px; font-weight: 500; color: #dc2626; }
        .umk-hint { margin: 0 0 12px; font-size: 12px; color: #64748b; }

        .umk-item { margin-bottom: 12px; padding: 14px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
        .umk-item-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
        .umk-chip { padding: 3px 10px; border-radius: 9999px; background: #eff6ff; color: #1e40af; font-size: 12px; font-weight: 700; }
        .umk-item-sub { font-size: 14px; font-weight: 800; color: #0f172a; }
        .umk-item-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .umk-item-grid .s2 { grid-column: span 2; }
        .umk-sm-only { display: none; }
        .umk-readonly { display: flex; align-items: center; min-height: 40px; padding: 8px 12px; box-sizing: border-box; font-size: 14px; font-weight: 600; color: #0f172a; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; }
        .umk-empty { padding: 32px 16px; text-align: center; font-size: 14px; color: #64748b; background: #fff; border: 1px dashed #cbd5e1; border-radius: 16px; }

        .umk-sum { margin-bottom: 12px; padding: 14px 16px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
        .umk-sum-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .umk-sum-head h4 { margin: 0; font-size: 14px; font-weight: 700; color: #0f172a; }
        .umk-link { border: 0; background: none; padding: 0; font: inherit; font-size: 12px; font-weight: 600; color: #1e40af; cursor: pointer; }
        .umk-sum-row { display: flex; justify-content: space-between; gap: 16px; padding: 5px 0; font-size: 14px; }
        .umk-sum-row > span { flex: none; color: #64748b; }
        .umk-sum-row > strong { text-align: right; font-weight: 600; color: #0f172a; word-break: break-word; }
        .umk-sum-item { padding: 8px 0; border-top: 1px solid #f1f5f9; }
        .umk-sum-item:first-of-type { border-top: 0; padding-top: 0; }
        .umk-sum-item small { display: block; margin-top: 2px; font-size: 12px; font-weight: 400; color: #64748b; }
        .umk-sum-item strong { text-align: left; }

        .umk-doc { margin-bottom: 12px; padding: 14px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
        .umk-doc-top { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .umk-badge { flex: none; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: #eff6ff; color: #1e40af; font-size: 12px; font-weight: 700; }
        .umk-doc-name { font-size: 14px; font-weight: 600; line-height: 1.3; color: #1e293b; }
        .umk-doc-btns { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .umk-dl { display: flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; border-radius: 8px; color: #fff; font-size: 14px; font-weight: 700; text-decoration: none; }
        .umk-dl-pdf { background: #dc2626; } .umk-dl-pdf:hover { background: #b91c1c; }
        .umk-dl-xls { background: #059669; } .umk-dl-xls:hover { background: #047857; }

        .umk-anim { animation: umk-pop .18s ease-out; }
        @keyframes umk-pop { from { opacity: 0; transform: scale(.97); } to { opacity: 1; transform: scale(1); } }
        @media (prefers-reduced-motion: reduce) { .umk-anim { animation: none; } }

        @media (min-width: 640px) {
            .umk-head { padding: 20px 24px 16px; }
            .umk-h { font-size: 18px; }
            .umk-sub { font-size: 14px; }
            .umk-body { padding: 20px 24px; background: #fff; }
            .umk-foot { padding: 14px 24px; background: #f8fafc; }
            .umk-foot-in { flex-direction: row; align-items: center; justify-content: space-between; }
            .umk-btn { flex: none; min-height: 40px; padding: 0 20px; }
            .umk-form { grid-template-columns: 1fr 1fr; padding: 0; border: 0; background: transparent; gap: 16px; }
            .umk-full { grid-column: auto; }
            .umk-in { min-height: 40px; font-size: 14px; }
            .umk-item { background: #f8fafc; }
            .umk-item-sub { display: none; }
            .umk-item-grid { grid-template-columns: repeat(4, 1fr); }
            .umk-item-grid .s2.s1-sm { grid-column: span 1; }
            .umk-sm-only { display: block; }
            .umk-card-sm .umk-body { background: #f8fafc; }

            /* Modal Dokumen di desktop: daftar ringkas, satu baris per dokumen */
            .umk-card-sm { max-width: 540px; }
            .umk-card-sm .umk-body { background: #fff; padding: 12px 24px 20px; }
            .umk-card-sm .umk-foot { display: none; }
            .umk-card-sm .umk-hint { margin-bottom: 8px; }
            .umk-doc { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 0; padding: 10px 0; background: transparent; border: 0; border-radius: 0; border-top: 1px solid #f1f5f9; }
            .umk-doc-top { margin-bottom: 0; gap: 12px; min-width: 0; }
            .umk-badge { width: 32px; height: 32px; border-radius: 8px; font-size: 11px; }
            .umk-doc-btns { display: flex; flex: none; gap: 8px; }
            .umk-dl { min-height: 0; padding: 7px 14px; font-size: 12px; font-weight: 600; border-radius: 8px; gap: 6px; }
        }
    </style>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-5 flex flex-col gap-4 sm:mb-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20 sm:h-12 sm:w-12">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">
                        {{ $isSuperAdmin ? 'Semua Riwayat UMK' : 'Riwayat UMK Saya' }}
                    </h2>
                    <p class="mt-0.5 text-sm text-slate-500">
                        {{ $isSuperAdmin ? 'Pantau seluruh UMK dari semua pegawai.' : 'Pantau status form Uang Muka Kerja Anda.' }}
                    </p>
                </div>
            </div>
            <a href="{{ route('perjalanan.buat') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>
                Buat UMK Baru
            </a>
        </div>

        <!-- Statistik -->
        <div class="umk-stats">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-medium leading-tight text-slate-500">Total UMK</p>
                <p class="mt-1 text-2xl font-extrabold text-blue-900 sm:text-3xl">{{ $riwayats->total() }}</p>
                <p class="mt-0.5 text-[11px] text-slate-400">semua halaman</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-medium leading-tight text-slate-500">Perlu Dilaporkan</p>
                <p class="mt-1 text-2xl font-extrabold text-amber-600 sm:text-3xl">{{ $riwayats->getCollection()->whereIn('status', $statusBisaLapor)->filter(fn ($r) => !$r->laporan)->count() }}</p>
                <p class="mt-0.5 text-[11px] text-slate-400">di halaman ini</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-medium leading-tight text-slate-500">Selesai</p>
                <p class="mt-1 text-2xl font-extrabold text-emerald-600 sm:text-3xl">{{ $riwayats->getCollection()->filter(fn ($r) => $r->laporan)->count() }}</p>
                <p class="mt-0.5 text-[11px] text-slate-400">di halaman ini</p>
            </div>
        </div>

        <!-- Tabel (Desktop) -->
        <div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm sm:block">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th class="px-6 py-3 font-semibold">No. UMK</th>
                            @if($isSuperAdmin)
                                <th class="px-6 py-3 font-semibold">Pegawai</th>
                            @endif
                            <th class="px-6 py-3 font-semibold">Tujuan</th>
                            <th class="px-6 py-3 font-semibold">Tanggal</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="px-6 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($riwayats as $r)
                            @php
                                [$label, $badge] = $statusMeta($r->status);
                                $iconStatus = match ($r->status) {
                                    'RECEIVED_BY_FINANCE', 'PIMPINAN_SIGNED', 'COMPLETED' => 'M5 13l4 4L19 7',
                                    'REJECTED' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                    default => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                };
                            @endphp
                            <tr wire:key="row-{{ $r->id }}" class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        </div>
                                        <span class="font-semibold text-slate-900">{{ $r->nomor_pengajuan }}</span>
                                    </div>
                                </td>
                                @if($isSuperAdmin)
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-800">{{ $r->pegawai->name ?? '-' }}</div>
                                        <div class="mt-0.5 text-xs text-slate-500">{{ $r->pegawai->nip ?? '-' }}</div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-slate-600">{{ $r->kotaTujuan->nama }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-slate-600">{{ $r->tanggal_berangkat->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $badge }}">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconStatus }}"/></svg>
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($r->status === 'DRAFT')
                                            <button type="button" wire:click="openEditModal({{ $r->id }})" class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 hover:text-blue-900">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </button>
                                        @elseif(in_array($r->status, $statusBisaLapor) && !$r->laporan)
                                            <a href="{{ route('perjalanan.lapor', ['id' => $r->id, 'from' => 'riwayat']) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 hover:text-blue-900">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Lapor
                                            </a>
                                        @elseif($r->laporan)
                                            <span class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Tuntas
                                            </span>
                                        @endif
                                        <button type="button" @click="bukaDokumen(@js($r->id), @js($r->nomor_pengajuan))" class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors bg-blue-900 text-white hover:bg-blue-800">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Dokumen
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isSuperAdmin ? 6 : 5 }}" class="px-6 py-14 text-center text-slate-500">
                                    Belum ada riwayat UMK. Buat UMK baru untuk memulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($riwayats->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $riwayats->links() }}</div>
            @endif
        </div>

        <!-- Card List (Mobile) -->
        <div class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm sm:hidden">
                @forelse ($riwayats as $r)
                    @php [$label, $badge] = $statusMeta($r->status); @endphp
                    <div wire:key="card-{{ $r->id }}" class="p-4">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="break-words font-bold text-slate-900">{{ $r->nomor_pengajuan }}</div>
                                @if($isSuperAdmin)
                                    <div class="mt-0.5 text-xs font-semibold text-blue-800">{{ $r->pegawai->name ?? '-' }}</div>
                                @endif
                                <div class="mt-1 text-xs text-slate-500">Ke {{ $r->kotaTujuan->nama }} &middot; {{ $r->tanggal_berangkat->format('d/m/Y') }}</div>
                            </div>
                            <span class="inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 ring-inset {{ $badge }}">{{ $label }}</span>
                        </div>

                        <div class="mt-4 flex flex-col gap-2">
                            @if($r->status === 'DRAFT')
                                <button type="button" wire:click="openEditModal({{ $r->id }})" class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2.5 text-xs font-semibold transition-colors bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 hover:text-blue-900">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </button>
                            @elseif(in_array($r->status, $statusBisaLapor) && !$r->laporan)
                                <a href="{{ route('perjalanan.lapor', ['id' => $r->id, 'from' => 'riwayat']) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2.5 text-xs font-semibold transition-colors bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 hover:text-blue-900">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Lapor
                                </a>
                            @elseif($r->laporan)
                                <span class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2.5 text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Tuntas
                                </span>
                            @endif
                            <button type="button" @click="bukaDokumen(@js($r->id), @js($r->nomor_pengajuan))" class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2.5 text-xs font-semibold transition-colors bg-blue-900 text-white hover:bg-blue-800">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Dokumen
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center font-semibold text-slate-800">Belum ada riwayat UMK</div>
                @endforelse
            @if($riwayats->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">{{ $riwayats->links() }}</div>
            @endif
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL DOKUMEN - di tengah layar, di antara header & bottom -->
    <!-- nav (tidak pernah tertimpa)                                -->
    <!-- ========================================================= -->
    <div x-show="dokumenOpen" style="display: none;" class="umk-ov"
         @click.self="dokumenOpen = false"
         @keydown.escape.window="dokumenOpen = false"
         role="dialog" aria-modal="true" aria-labelledby="dokumen-title">

        <div class="umk-card umk-card-sm umk-anim">
            <div class="umk-head umk-head-row">
                <div style="min-width:0">
                    <h3 id="dokumen-title" class="umk-h">Dokumen UMK</h3>
                    <p class="umk-sub" x-text="activeNomor"></p>
                </div>
                <button type="button" class="umk-x" @click="dokumenOpen = false" aria-label="Tutup">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="umk-body">
                <p class="umk-hint">Pilih format file untuk setiap dokumen. PDF terbuka di tab baru, Excel langsung terunduh.</p>

                <template x-for="doc in docs" :key="doc.kode">
                    <div class="umk-doc">
                        <div class="umk-doc-top">
                            <span class="umk-badge" x-text="doc.kode"></span>
                            <span class="umk-doc-name" x-text="doc.label"></span>
                        </div>
                        <div class="umk-doc-btns">
                            <a :href="url(doc.pdf)" target="_blank" rel="noopener" class="umk-dl umk-dl-pdf">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                PDF
                            </a>
                            <a :href="url(doc.xls)" class="umk-dl umk-dl-xls">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/></svg>
                                Excel
                            </a>
                        </div>
                    </div>
                </template>
            </div>

            <div class="umk-foot">
                <button type="button" class="umk-btn umk-btn-g" style="width:100%" @click="dokumenOpen = false">Tutup</button>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL EDIT UMK - satu komponen, selalu di tengah layar     -->
    <!-- ========================================================= -->
    @if($showEditModal)
    <div class="umk-ov" wire:click.self="closeEditModal"
         role="dialog" aria-modal="true" aria-labelledby="edit-umk-title"
         x-data @keydown.escape.window="$wire.closeEditModal()">

        <form wire:submit="updateUmk"
              @keydown.enter="if ($event.target.tagName !== 'BUTTON') $event.preventDefault()"
              class="umk-card umk-card-lg umk-anim">

            <!-- Header + stepper -->
            <div class="umk-head">
                <div class="umk-head-row">
                    <div style="min-width:0">
                        <h3 id="edit-umk-title" class="umk-h">Edit UMK</h3>
                        <p class="umk-sub">Langkah {{ $editStep }} dari 3 &middot; {{ $stepTitles[$editStep] ?? '' }}</p>
                    </div>
                    <button type="button" wire:click="closeEditModal" class="umk-x" aria-label="Tutup form edit">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="umk-steps" role="progressbar" aria-valuemin="1" aria-valuemax="3" aria-valuenow="{{ $editStep }}">
                    @foreach($stepTitles as $n => $t)
                        <i class="{{ $editStep >= $n ? 'on' : '' }}"></i>
                    @endforeach
                </div>
            </div>

            <!-- Isi: hanya bagian ini yang scroll -->
            <div class="umk-body"
                 x-ref="body"
                 x-effect="$wire.editStep; $nextTick(() => $refs.body && $refs.body.scrollTo({ top: 0 }))">

                <!-- STEP 1 -->
                <div style="{{ $editStep === 1 ? '' : 'display:none' }}">
                    <div class="umk-form">
                        <div class="umk-full">
                            <label for="editTempatBerangkat" class="umk-lb">Tempat Berangkat</label>
                            <input id="editTempatBerangkat" wire:model="editTempatBerangkat" type="text" autocomplete="off" class="umk-in">
                            @error('editTempatBerangkat') <p class="umk-er">{{ $message }}</p> @enderror
                        </div>
                        <div class="umk-full">
                            <label for="editMaksud" class="umk-lb">Maksud Perjalanan</label>
                            <input id="editMaksud" wire:model="editMaksud" type="text" autocomplete="off" class="umk-in">
                            @error('editMaksud') <p class="umk-er">{{ $message }}</p> @enderror
                        </div>
                        <div class="umk-full">
                            <label for="editProvinsi" class="umk-lb">Provinsi</label>
                            <select id="editProvinsi" wire:model.live="editProvinsi" class="umk-in">
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinsis as $prov)
                                    <option value="{{ $prov }}">{{ $prov }}</option>
                                @endforeach
                            </select>
                            @error('editProvinsi') <p class="umk-er">{{ $message }}</p> @enderror
                        </div>
                        <div class="umk-full">
                            <label for="editKotaTujuanId" class="umk-lb">Kota Tujuan</label>
                            <select id="editKotaTujuanId" wire:model="editKotaTujuanId" wire:loading.attr="disabled" wire:target="editProvinsi" class="umk-in" @if(!$editProvinsi) disabled @endif>
                                <option value="">{{ $editProvinsi ? '-- Pilih Kota --' : 'Pilih provinsi dulu' }}</option>
                                @foreach($kotas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                            @error('editKotaTujuanId') <p class="umk-er">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="editAlatAngkutan" class="umk-lb">Alat Angkutan</label>
                            <select id="editAlatAngkutan" wire:model="editAlatAngkutan" class="umk-in">
                                <option value="Pesawat">Pesawat</option>
                                <option value="Kereta">Kereta</option>
                                <option value="Bus">Bus</option>
                                <option value="Mobil">Mobil</option>
                            </select>
                            @error('editAlatAngkutan') <p class="umk-er">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="editTingkatBiaya" class="umk-lb">Tingkat Biaya</label>
                            <select id="editTingkatBiaya" wire:model="editTingkatBiaya" class="umk-in">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                            @error('editTingkatBiaya') <p class="umk-er">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="editTanggalBerangkat" class="umk-lb">Tgl Berangkat</label>
                            <input id="editTanggalBerangkat" wire:model.live="editTanggalBerangkat" type="date" class="umk-in">
                            @error('editTanggalBerangkat') <p class="umk-er">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="editTanggalPulang" class="umk-lb">Tgl Pulang</label>
                            <input id="editTanggalPulang" wire:model.live="editTanggalPulang" type="date" @if($editTanggalBerangkat) min="{{ $editTanggalBerangkat }}" @endif class="umk-in">
                            @error('editTanggalPulang') <p class="umk-er">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- STEP 2 -->
                <div style="{{ $editStep === 2 ? '' : 'display:none' }}">
                    @forelse($editRincian as $index => $item)
                        <div wire:key="edit-rincian-{{ $index }}" class="umk-item">
                            <div class="umk-item-top">
                                <span class="umk-chip">Item {{ $index + 1 }}</span>
                                <span class="umk-item-sub">{{ $rp($item['nominal'] ?? 0) }}</span>
                            </div>
                            <div class="umk-item-grid">
                                <div class="s2">
                                    <label for="rincian-{{ $index }}-coa" class="umk-lb umk-lb-s">Kategori</label>
                                    <select id="rincian-{{ $index }}-coa" wire:model="editRincian.{{ $index }}.coa_id" class="umk-in">
                                        <option value="">-- Pilih --</option>
                                        @foreach($coas as $c)
                                            <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="s2">
                                    <label for="rincian-{{ $index }}-ket" class="umk-lb umk-lb-s">Keterangan</label>
                                    <input id="rincian-{{ $index }}-ket" wire:model="editRincian.{{ $index }}.keterangan" type="text" autocomplete="off" class="umk-in">
                                </div>
                                <div>
                                    <label for="rincian-{{ $index }}-qty" class="umk-lb umk-lb-s">Qty</label>
                                    <input id="rincian-{{ $index }}-qty" wire:model.live.debounce.500ms="editRincian.{{ $index }}.qty" type="number" inputmode="numeric" min="0" class="umk-in">
                                </div>
                                <div>
                                    <label for="rincian-{{ $index }}-sat" class="umk-lb umk-lb-s">Satuan</label>
                                    <input id="rincian-{{ $index }}-sat" wire:model="editRincian.{{ $index }}.satuan" type="text" autocomplete="off" class="umk-in">
                                </div>
                                <div class="s2 s1-sm">
                                    <label for="rincian-{{ $index }}-hrg" class="umk-lb umk-lb-s">Harga Satuan (Rp)</label>
                                    <input id="rincian-{{ $index }}-hrg" wire:model.live.debounce.500ms="editRincian.{{ $index }}.harga_satuan" type="number" inputmode="numeric" min="0" class="umk-in">
                                </div>
                                <div class="umk-sm-only">
                                    <span class="umk-lb umk-lb-s">Jumlah</span>
                                    <div class="umk-readonly">{{ $rp($item['nominal'] ?? 0) }}</div>
                                </div>
                            </div>
                            @foreach(['coa_id', 'keterangan', 'qty', 'satuan', 'harga_satuan'] as $f)
                                @error("editRincian.$index.$f") <p class="umk-er">{{ $message }}</p> @enderror
                            @endforeach
                        </div>
                    @empty
                        <div class="umk-empty">Belum ada rincian biaya pada UMK ini.</div>
                    @endforelse
                </div>

                <!-- STEP 3 -->
                <div style="{{ $editStep === 3 ? '' : 'display:none' }}">
                    @php
                        $kotaNama = collect($kotas)->firstWhere('id', $editKotaTujuanId)?->nama;
                        $tglB = $editTanggalBerangkat ? \Carbon\Carbon::parse($editTanggalBerangkat)->format('d/m/Y') : '-';
                        $tglP = $editTanggalPulang ? \Carbon\Carbon::parse($editTanggalPulang)->format('d/m/Y') : '-';
                        $ringkas = [
                            'Berangkat dari' => $editTempatBerangkat ?: '-',
                            'Tujuan' => trim(($kotaNama ?: '-') . ($editProvinsi ? ', ' . $editProvinsi : '')),
                            'Maksud' => $editMaksud ?: '-',
                            'Tanggal' => $tglB . ' s/d ' . $tglP,
                            'Angkutan / Tingkat' => ($editAlatAngkutan ?: '-') . ' / ' . ($editTingkatBiaya ?: '-'),
                        ];
                    @endphp

                    <div class="umk-sum">
                        <div class="umk-sum-head">
                            <h4>Info perjalanan</h4>
                            <button type="button" wire:click="prevEditStep" class="umk-link">Ubah</button>
                        </div>
                        @foreach($ringkas as $k => $v)
                            <div class="umk-sum-row"><span>{{ $k }}</span><strong>{{ $v }}</strong></div>
                        @endforeach
                    </div>

                    <div class="umk-sum">
                        <div class="umk-sum-head"><h4>Rincian biaya ({{ count($editRincian) }} item)</h4></div>
                        @foreach($editRincian as $item)
                            <div class="umk-sum-row umk-sum-item">
                                <div style="min-width:0">
                                    <strong>{{ $item['keterangan'] ?: (collect($coas)->firstWhere('id', $item['coa_id'] ?? null)?->nama ?? '-') }}</strong>
                                    <small>{{ $item['qty'] ?? 0 }} {{ $item['satuan'] ?? '' }} &times; {{ $rp($item['harga_satuan'] ?? 0) }}</small>
                                </div>
                                <strong style="flex:none">{{ $rp($item['nominal'] ?? 0) }}</strong>
                            </div>
                        @endforeach
                    </div>

                    <p class="umk-hint" style="text-align:center">Periksa kembali data di atas, lalu tekan <b>Simpan perubahan</b>.</p>
                </div>
            </div>

            <!-- Footer: total selalu terlihat + tombol navigasi (di dalam kartu) -->
            <div class="umk-foot">
                <div class="umk-foot-in">
                    <div class="umk-total"><span>Total estimasi</span><strong>{{ $rp($editTotalEstimasi) }}</strong></div>
                    <div class="umk-btns">
                        @if($editStep === 1)
                            <button type="button" wire:click="closeEditModal" class="umk-btn umk-btn-g">Batal</button>
                            <button type="button" wire:click="nextEditStep" wire:loading.attr="disabled" wire:target="nextEditStep" class="umk-btn umk-btn-p">Selanjutnya</button>
                        @elseif($editStep === 2)
                            <button type="button" wire:click="prevEditStep" class="umk-btn umk-btn-g">Kembali</button>
                            <button type="button" wire:click="nextEditStep" wire:loading.attr="disabled" wire:target="nextEditStep" class="umk-btn umk-btn-p">Lanjut konfirmasi</button>
                        @else
                            <button type="button" wire:click="prevEditStep" class="umk-btn umk-btn-g">Kembali</button>
                            <button type="button" onclick="confirmSave(event)" wire:loading.attr="disabled" wire:target="updateUmk" class="umk-btn umk-btn-p">
                                <span wire:loading.remove wire:target="updateUmk">Simpan perubahan</span>
                                <span wire:loading wire:target="updateUmk">Menyimpan...</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- SWEETALERT (KONFIRMASI & NOTIFIKASI)       -->
    <!-- ========================================== -->
    <script>
        if (!window.__umkRiwayatBound) {
            window.__umkRiwayatBound = true;

            document.addEventListener('livewire:init', () => {
                const toast = (icon, fallback) => (data) => {
                    const config = data[0] || data;
                    Swal.fire({
                        toast: true, position: 'top', icon: icon,
                        title: config.message || fallback,
                        showConfirmButton: false, timer: 3000, timerProgressBar: true,
                        customClass: { popup: 'font-sans rounded-xl' }
                    });
                };
                Livewire.on('swal:success', toast('success', 'Berhasil disimpan!'));
                Livewire.on('swal:error', toast('error', 'Terjadi kesalahan.'));
            });

            window.confirmSave = function (event) {
                if (event) event.preventDefault();
                const form = event && event.target ? event.target.closest('form') : null;

                Swal.fire({
                    title: 'Simpan perubahan?',
                    text: 'Pastikan data perjalanan dan rincian biaya sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1e3a8a',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, simpan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'font-sans rounded-2xl' }
                }).then((result) => {
                    if (!result.isConfirmed) return;
                    if (form) {
                        form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                    }
                });
            };
        }
    </script>
</div>