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

<div>
<div class="py-6 sm:py-10"
     x-data="{
        dokumenOpen: false,
        activeId: null,
        activeNomor: '',
        docs: @js($dokumenList),
        bukaDokumen(id, nomor) { this.activeId = id; this.activeNomor = nomor; this.dokumenOpen = true; },
        url(tpl) { return tpl.replace('ID_PLACEHOLDER', this.activeId); }
     }"
     x-effect="document.body.classList.toggle('umk-lock', dokumenOpen)">

    @include('livewire.perjalanan.riwayat.styles')

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

    @include('livewire.perjalanan.riwayat.modal-dokumen')

    @include('livewire.perjalanan.riwayat.modal-edit')

</div>

    @include('livewire.perjalanan.riwayat.scripts')
</div>