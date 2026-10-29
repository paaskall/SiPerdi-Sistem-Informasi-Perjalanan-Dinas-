<div class="py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

            <!-- Header -->
            <div class="flex flex-col gap-4 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div>
                    <h2 class="text-lg font-bold tracking-tight text-slate-900">Riwayat Pengajuan Saya</h2>
                    <p class="mt-1 text-sm text-slate-500">Pantau status setiap pengajuan perjalanan dinas Anda.</p>
                </div>
                <a href="{{ route('perjalanan.buat') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
                    Buat Pengajuan Baru
                </a>
            </div>

            <!-- Tabel (tablet & desktop) -->
            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">No. Pengajuan</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Tujuan</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Tanggal</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($riwayats as $r)
                            @php
                                [$label, $badge] = match ($r->status) {
                                    'SUBMITTED'        => ['Menunggu Keuangan', 'bg-amber-50 text-amber-700 ring-amber-200'],
                                    'FINANCE_APPROVED' => ['Disetujui Keuangan', 'bg-blue-50 text-blue-700 ring-blue-200'],
                                    'PIMPINAN_SIGNED'  => ['SPPD Terbit', 'bg-indigo-50 text-indigo-700 ring-indigo-200'],
                                    'COMPLETED'        => ['Realisasi Selesai', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
                                    'REJECTED'         => ['Ditolak', 'bg-red-50 text-red-700 ring-red-200'],
                                    default            => [$r->status, 'bg-slate-100 text-slate-700 ring-slate-200'],
                                };
                            @endphp
                            <tr wire:key="row-{{ $r->id }}" class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $r->nomor_pengajuan }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $r->kotaTujuan->nama }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-slate-600">{{ $r->tanggal_berangkat->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $badge }}">{{ $label }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if(in_array($r->status, ['PIMPINAN_SIGNED', 'COMPLETED']))
                                        <div class="flex flex-col gap-1.5 items-center">
                                            <a href="{{ route('sppd.pdf', $r->id) }}" target="_blank" class="inline-flex w-full items-center justify-center rounded bg-slate-800 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-900 transition-colors">
                                                Download SPPD
                                            </a>
                                            
                                            @if($r->status === 'PIMPINAN_SIGNED')
                                                <a href="{{ route('perjalanan.reimb', $r->id) }}" class="inline-flex w-full items-center justify-center rounded bg-blue-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-800 transition-colors">
                                                    Laporkan Realisasi
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada riwayat pengajuan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Daftar kartu (mobile) -->
            <div class="divide-y divide-slate-100 sm:hidden">
                @forelse ($riwayats as $r)
                    @php
                        [$label, $badge] = match ($r->status) {
                            'SUBMITTED'        => ['Menunggu Keuangan', 'bg-amber-50 text-amber-700 ring-amber-200'],
                            'FINANCE_APPROVED' => ['Disetujui Keuangan', 'bg-blue-50 text-blue-700 ring-blue-200'],
                            'PIMPINAN_SIGNED'  => ['SPPD Terbit', 'bg-indigo-50 text-indigo-700 ring-indigo-200'],
                            'COMPLETED'        => ['Realisasi Selesai', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
                            'REJECTED'         => ['Ditolak', 'bg-red-50 text-red-700 ring-red-200'],
                            default            => [$r->status, 'bg-slate-100 text-slate-700 ring-slate-200'],
                        };
                    @endphp
                    <div wire:key="card-{{ $r->id }}" class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="font-semibold text-slate-900">{{ $r->nomor_pengajuan }}</div>
                            <span class="inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $badge }}">{{ $label }}</span>
                        </div>
                        <dl class="mt-3 space-y-1.5">
                            <div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Tujuan</dt><dd class="text-right text-slate-800">{{ $r->kotaTujuan->nama }}</dd></div>
                            <div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Tanggal</dt><dd class="text-right text-slate-800">{{ $r->tanggal_berangkat->format('d/m/Y') }}</dd></div>
                        </dl>
                        
                        @if(in_array($r->status, ['PIMPINAN_SIGNED', 'COMPLETED']))
                            <div class="mt-4 flex flex-col gap-2 border-t border-slate-100 pt-3">
                                <a href="{{ route('sppd.pdf', $r->id) }}" target="_blank" class="block w-full rounded-lg bg-slate-800 px-4 py-2 text-center text-sm font-semibold text-white transition-colors hover:bg-slate-900">
                                    Download SPPD
                                </a>
                                @if($r->status === 'PIMPINAN_SIGNED')
                                    <a href="{{ route('perjalanan.reimb', $r->id) }}" class="block w-full rounded-lg bg-blue-900 px-4 py-2 text-center text-sm font-semibold text-white transition-colors hover:bg-blue-800">
                                        Laporkan Realisasi
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-slate-500">Belum ada riwayat pengajuan.</div>
                @endforelse
            </div>

            @if ($riwayats->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $riwayats->links() }}</div>
            @endif
        </div>
    </div>
</div>