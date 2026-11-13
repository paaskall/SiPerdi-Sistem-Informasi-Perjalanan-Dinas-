<div class="py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Verifikasi Form UMK</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Daftar UMK yang menunggu dan sudah diverifikasi.</p>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <!-- Toolbar -->
            <div class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <div class="relative w-full sm:max-w-xs">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari no. UMK atau pegawai..." class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20">
                </div>
                <p class="text-sm text-slate-500">Menampilkan <span class="font-semibold text-slate-700">{{ $umks->firstItem() ?? 0 }}&ndash;{{ $umks->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700">{{ $umks->total() }}</span> data</p>
            </div>

            <!-- Tabel Desktop -->
            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th class="w-16 px-6 py-3 font-semibold">No</th>
                            <th class="px-6 py-3 font-semibold">No. UMK</th>
                            <th class="px-6 py-3 font-semibold">Pegawai</th>
                            <th class="px-6 py-3 font-semibold">Tujuan</th>
                            <th class="px-6 py-3 font-semibold">Tgl Berangkat</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="w-40 px-6 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($umks as $index => $u)
                            <tr wire:key="row-{{ $u->id }}" class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 text-slate-400">{{ $umks->firstItem() + $index }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $u->nomor_pengajuan }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-slate-800">{{ $u->pegawai->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $u->pegawai->divisi->nama ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $u->kotaTujuan->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-600">{{ $u->tanggal_berangkat->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    @if($u->status === 'PRINTED')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Menunggu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Terverifikasi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button type="button" wire:click="lihat({{ $u->id }})" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Lihat
                                        </button>
                                        
                                        @if($u->status === 'PRINTED')
                                            <button type="button" 
                                                    x-data
                                                    @click.prevent="
                                                        Swal.fire({
                                                            title: 'Terima UMK ini?',
                                                            text: 'Form UMK akan ditandai sebagai diterima & diverifikasi.',
                                                            icon: 'question',
                                                            showCancelButton: true,
                                                            confirmButtonColor: '#1e3a8a',
                                                            cancelButtonColor: '#64748b',
                                                            confirmButtonText: 'Ya, Terima!',
                                                            cancelButtonText: 'Batal'
                                                        }).then((result) => {
                                                            if (result.isConfirmed) {
                                                                $wire.terima({{ $u->id }})
                                                            }
                                                        })
                                                    "
                                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-900 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Terima
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7">
                                <div class="px-6 py-14 text-center">
                                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <p class="font-semibold text-slate-800">Belum ada data UMK</p>
                                    <p class="mt-1 text-sm text-slate-500">UMK yang sudah dicetak pegawai akan muncul di sini.</p>
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile -->
            <div class="divide-y divide-slate-100 sm:hidden">
                @forelse ($umks as $u)
                    <div wire:key="card-{{ $u->id }}" class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="font-semibold text-slate-900">{{ $u->nomor_pengajuan }}</div>
                            @if($u->status === 'PRINTED')
                                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">Menunggu</span>
                            @else
                                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Terverifikasi</span>
                            @endif
                        </div>
                        <dl class="mt-3 space-y-1.5 border-t border-slate-100 pt-3">
                            <div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">Pegawai</dt><dd class="text-right text-slate-800">{{ $u->pegawai->name }}</dd></div>
                            <div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">Tujuan</dt><dd class="text-right text-slate-800">{{ $u->kotaTujuan->nama }}</dd></div>
                            <div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">Tgl Berangkat</dt><dd class="text-right text-slate-800">{{ $u->tanggal_berangkat->format('d/m/Y') }}</dd></div>
                        </dl>
                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <button type="button" wire:click="lihat({{ $u->id }})" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900">
                                Lihat
                            </button>
                            @if($u->status === 'PRINTED')
                                <button type="button" 
                                        x-data
                                        @click.prevent="
                                            Swal.fire({
                                                title: 'Terima UMK ini?',
                                                text: 'Form UMK akan ditandai sebagai diterima & diverifikasi.',
                                                icon: 'question',
                                                showCancelButton: true,
                                                confirmButtonColor: '#1e3a8a',
                                                cancelButtonColor: '#64748b',
                                                confirmButtonText: 'Ya, Terima!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.terima({{ $u->id }})
                                                }
                                            })
                                        "
                                        class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-900 px-3 py-2.5 text-xs font-semibold text-white transition-colors hover:bg-blue-800">
                                    Terima
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center">
                        <p class="font-semibold text-slate-800">Belum ada data UMK</p>
                    </div>
                @endforelse
            </div>

            @if ($umks->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $umks->links() }}</div>
            @endif
        </div>
    </div>

    <!-- MODAL LIHAT DETAIL -->
    @if($isModalOpen && $selectedUmk)
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 backdrop-blur-sm sm:items-center sm:p-4" wire:keydown.escape.window="closeModal" role="dialog" aria-modal="true">
        <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-t-2xl bg-white shadow-xl sm:rounded-2xl">

            <!-- Header Modal -->
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Detail UMK</h3>
                        <p class="text-xs text-slate-500">{{ $selectedUmk->nomor_pengajuan }}</p>
                    </div>
                </div>
                <button type="button" wire:click="closeModal" aria-label="Tutup" class="rounded-md p-1 text-slate-400 transition-colors hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body Modal -->
            <div class="p-5 sm:p-6 space-y-6">
                <!-- Data Pegawai -->
                <div>
                    <h4 class="text-sm font-bold text-slate-900 mb-3">Data Pegawai</h4>
                    <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div><span class="text-slate-500">Nama</span><p class="font-semibold text-slate-900">{{ $selectedUmk->pegawai->name }}</p></div>
                        <div><span class="text-slate-500">NIP</span><p class="font-semibold text-slate-900">{{ $selectedUmk->pegawai->nip ?? '-' }}</p></div>
                        <div><span class="text-slate-500">Jabatan</span><p class="font-semibold text-slate-900">{{ $selectedUmk->pegawai->jabatan->nama ?? '-' }}</p></div>
                        <div><span class="text-slate-500">Divisi</span><p class="font-semibold text-slate-900">{{ $selectedUmk->pegawai->divisi->nama ?? '-' }}</p></div>
                        <div><span class="text-slate-500">Golongan</span><p class="font-semibold text-slate-900">{{ $selectedUmk->pegawai->golongan->nama ?? '-' }}</p></div>
                        <div><span class="text-slate-500">Pangkat</span><p class="font-semibold text-slate-900">{{ $selectedUmk->pegawai->pangkat->nama ?? '-' }}</p></div>
                    </div>
                </div>

                <!-- Informasi Perjalanan -->
                <div>
                    <h4 class="text-sm font-bold text-slate-900 mb-3">Informasi Perjalanan</h4>
                    <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div><span class="text-slate-500">Kota Tujuan</span><p class="font-semibold text-slate-900">{{ $selectedUmk->kotaTujuan->nama }}</p></div>
                        <div><span class="text-slate-500">Maksud Perjalanan</span><p class="font-semibold text-slate-900">{{ $selectedUmk->maksud_perjalanan }}</p></div>
                        <div><span class="text-slate-500">Tanggal Berangkat</span><p class="font-semibold text-slate-900">{{ $selectedUmk->tanggal_berangkat->format('d F Y') }}</p></div>
                        <div><span class="text-slate-500">Tanggal Pulang</span><p class="font-semibold text-slate-900">{{ $selectedUmk->tanggal_pulang->format('d F Y') }}</p></div>
                        <div><span class="text-slate-500">Lama Perjalanan</span><p class="font-semibold text-slate-900">{{ $selectedUmk->jumlah_hari }} Hari</p></div>
                    </div>
                </div>

                <!-- Rincian Biaya -->
                <div>
                    <h4 class="text-sm font-bold text-slate-900 mb-3">Rincian Estimasi Biaya</h4>
                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs text-slate-500">
                                <tr>
                                    <th class="px-4 py-2 font-semibold">Kategori</th>
                                    <th class="px-4 py-2 font-semibold">Keterangan</th>
                                    <th class="px-4 py-2 font-semibold text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($selectedUmk->rincianBiaya as $r)
                                    <tr>
                                        <td class="px-4 py-2 text-slate-800">{{ $r->coa->nama }}</td>
                                        <td class="px-4 py-2 text-slate-600">{{ $r->keterangan }}</td>
                                        <td class="px-4 py-2 text-right font-medium text-slate-900">Rp {{ number_format($r->nominal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-slate-50">
                                    <td colspan="2" class="px-4 py-2 text-right font-semibold text-slate-800">Total Estimasi</td>
                                    <td class="px-4 py-2 text-right font-bold text-blue-900">Rp {{ number_format($selectedUmk->total_estimasi, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                <button type="button" wire:click="closeModal" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900">Tutup</button>
                
                @if($selectedUmk->status === 'PRINTED')
                    <button type="button" 
                            x-data
                            @click.prevent="
                                Swal.fire({
                                    title: 'Terima UMK ini?',
                                    text: 'Form UMK akan ditandai sebagai diterima & diverifikasi.',
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#1e3a8a',
                                    cancelButtonColor: '#64748b',
                                    confirmButtonText: 'Ya, Terima!',
                                    cancelButtonText: 'Batal'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $wire.terima({{ $selectedUmk->id }})
                                    }
                                })
                            "
                            class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">
                        Terima & Verifikasi
                    </button>
                @endif
            </div>

        </div>
    </div>
    @endif
</div>