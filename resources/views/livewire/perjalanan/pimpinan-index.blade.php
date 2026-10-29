<div class="py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

            <!-- Header -->
            <div class="border-b border-slate-200 p-4 sm:p-6">
                <h2 class="text-lg font-bold tracking-tight text-slate-900">Persetujuan & Tanda Tangan SPPD</h2>
                <p class="mt-1 text-sm text-slate-500">Tinjau dokumen SPPD dan berikan tanda tangan pengesahan.</p>
            </div>

            <!-- Tabel (tablet & desktop) -->
            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">No. Pengajuan</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Pegawai</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Tujuan</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Estimasi</th>
                            <th scope="col" class="px-6 py-3 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pengajuans as $p)
                            <tr wire:key="row-{{ $p->id }}" class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $p->nomor_pengajuan }}</td>
                                <td class="px-6 py-4 text-slate-600">
                                    <div class="text-slate-800">{{ $p->pegawai->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $p->divisi->nama ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $p->kotaTujuan->nama }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">Rp {{ number_format($p->total_estimasi, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('sppd.pdf', $p->id) }}" target="_blank" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                                            Lihat PDF
                                        </a>

                                        @if($p->status === 'FINANCE_APPROVED')
                                            <!-- Tombol Desktop dengan ID Unik -->
                                            <button type="button" 
                                                    onclick="konfirmasiTtd({{ $p->id }})"
                                                    class="rounded-md bg-blue-900 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                                                Tanda Tangani
                                            </button>
                                            <!-- Tombol Tersembunyi untuk memicu Livewire -->
                                            <button type="button" id="btn-ttd-{{ $p->id }}" wire:click="setujui({{ $p->id }})" class="hidden"></button>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Sudah TTD</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada pengajuan yang menunggu tanda tangan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Daftar kartu (mobile) -->
            <div class="divide-y divide-slate-100 sm:hidden">
                @forelse ($pengajuans as $p)
                    <div wire:key="card-{{ $p->id }}" class="p-4">
                        <div class="font-semibold text-slate-900">{{ $p->nomor_pengajuan }}</div>
                        <dl class="mt-3 space-y-1.5">
                            <div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Pegawai</dt><dd class="text-right text-slate-800">{{ $p->pegawai->name }}</dd></div>
                            <div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Tujuan</dt><dd class="text-right text-slate-800">{{ $p->kotaTujuan->nama }}</dd></div>
                            <div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Estimasi</dt><dd class="text-right font-semibold text-slate-900">Rp {{ number_format($p->total_estimasi, 0, ',', '.') }}</dd></div>
                        </dl>
                        
                        <div class="mt-4 flex flex-col gap-2">
                            <a href="{{ route('sppd.pdf', $p->id) }}" target="_blank" class="block w-full rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900">
                                Lihat PDF
                            </a>
                            @if($p->status === 'FINANCE_APPROVED')
                                <!-- Tombol Mobile dengan ID Unik -->
                                <button type="button" 
                                        onclick="konfirmasiTtd({{ $p->id }})"
                                        class="block w-full rounded-lg bg-blue-900 px-4 py-2 text-center text-sm font-semibold text-white transition-colors hover:bg-blue-800">
                                    Tanda Tangani
                                </button>
                                <button type="button" id="btn-ttd-mobile-{{ $p->id }}" wire:click="setujui({{ $p->id }})" class="hidden"></button>
                            @else
                                <div class="text-center text-xs font-semibold text-emerald-600 mt-1">Sudah TTD</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-slate-500">Belum ada pengajuan yang menunggu tanda tangan.</div>
                @endforelse
            </div>

            @if ($pengajuans->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $pengajuans->links() }}</div>
            @endif
        </div>
    </div>
</div>

<!-- SCRIPT SWEETALERT KHUSUS HALAMAN INI -->
<script>
    function konfirmasiTtd(id) {
        Swal.fire({
            title: 'Tanda Tangani SPPD?',
            text: 'Dokumen PDF akan segera diterbitkan dengan tanda tangan Anda.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1e3a8a',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Tanda Tangani',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Klik tombol tersembunyi untuk memicu Livewire
                const btnDesktop = document.getElementById('btn-ttd-' + id);
                const btnMobile = document.getElementById('btn-ttd-mobile-' + id);
                
                if (btnDesktop) btnDesktop.click();
                if (btnMobile) btnMobile.click();
            }
        });
    }
</script>