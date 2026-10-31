<div class="py-8 sm:py-10">
    @php
        $ini = fn ($n) => mb_strtoupper(collect(preg_split('/\s+/', trim((string) $n)))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode(''));
    @endphp

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Riwayat Pengajuan Saya</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Pantau status setiap pengajuan perjalanan dinas Anda.</p>
                </div>
            </div>
            <a href="{{ route('perjalanan.buat') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>Buat Pengajuan Baru</a>
        </div>
        <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><p class="text-xs font-medium text-slate-500">Total Pengajuan</p><p class="mt-1 text-2xl font-extrabold text-blue-900 sm:text-3xl">{{ $riwayats->total() }}</p></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><p class="text-xs font-medium text-slate-500">Perlu Realisasi</p><p class="mt-1 text-2xl font-extrabold text-indigo-600 sm:text-3xl">{{ $riwayats->getCollection()->where('status', 'PIMPINAN_SIGNED')->count() }}</p><p class="mt-0.5 text-[11px] text-slate-400">di halaman ini</p></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><p class="text-xs font-medium text-slate-500">Realisasi Selesai</p><p class="mt-1 text-2xl font-extrabold text-emerald-600 sm:text-3xl">{{ $riwayats->getCollection()->where('status', 'COMPLETED')->count() }}</p><p class="mt-0.5 text-[11px] text-slate-400">di halaman ini</p></div>
        </div>
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-4 sm:p-5"><p class="text-sm text-slate-500">Menampilkan <span class="font-semibold text-slate-700">{{ $riwayats->firstItem() ?? 0 }}&ndash;{{ $riwayats->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700">{{ $riwayats->total() }}</span> data</p></div>
            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500"><tr>
                            <th scope="col" class="px-6 py-3 font-semibold">No. Pengajuan</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Tujuan</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Tanggal</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Aksi</th>
                    </tr></thead>
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
                                <td class="px-6 py-4"><div class="flex items-center gap-3"><div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></div><span class="font-semibold text-slate-900">{{ $r->nomor_pengajuan }}</span></div></td>
                                <td class="px-6 py-4 text-slate-600">{{ $r->kotaTujuan->nama }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-slate-600">{{ $r->tanggal_berangkat->format('d/m/Y') }}</td>
                                <td class="px-6 py-4"><span class="inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $badge }}">{{ $label }}</span>@if($r->status === 'REJECTED' && $r->catatan_keuangan)<div class="mt-1 text-xs italic text-red-600">Catatan: {{ $r->catatan_keuangan }}</div>@endif</td>
                                <td class="px-6 py-4">@if(in_array($r->status, ['PIMPINAN_SIGNED', 'COMPLETED']))
                                        <div class="flex flex-wrap gap-2"><a href="{{ route('sppd.pdf', $r->id) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>Download SPPD</a>@if($r->status === 'PIMPINAN_SIGNED')<a href="{{ route('perjalanan.reimb', $r->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-900 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>Laporkan Realisasi</a>@endif</div>
                                    @else<span class="text-xs text-slate-400">&ndash;</span>@endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><div class="px-6 py-14 text-center">
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
    <p class="font-semibold text-slate-800">Belum ada riwayat pengajuan</p>
    <p class="mt-1 text-sm text-slate-500">Pengajuan perjalanan dinas Anda akan muncul di sini.</p>
    <a href="{{ route('perjalanan.buat') }}" class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>Buat Pengajuan Baru</a>
</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

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
                            <div class="flex items-center gap-3"><div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></div><div class="font-semibold text-slate-900">{{ $r->nomor_pengajuan }}</div></div>
                            <span class="inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $badge }}">{{ $label }}</span>
                        </div>
                        <dl class="mt-3 space-y-1.5 border-t border-slate-100 pt-3"><div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">Tujuan</dt><dd class="text-right text-slate-800">{{ $r->kotaTujuan->nama }}</dd></div><div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">Tanggal</dt><dd class="text-right text-slate-800">{{ $r->tanggal_berangkat->format('d/m/Y') }}</dd></div></dl>
                        @if($r->status === 'REJECTED' && $r->catatan_keuangan)<div class="mt-1 text-xs italic text-red-600">Catatan: {{ $r->catatan_keuangan }}</div>@endif
                        @if(in_array($r->status, ['PIMPINAN_SIGNED', 'COMPLETED']))
                            <div class="mt-4 flex flex-col gap-2 [&>a]:py-2.5"><a href="{{ route('sppd.pdf', $r->id) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>Download SPPD</a>@if($r->status === 'PIMPINAN_SIGNED')<a href="{{ route('perjalanan.reimb', $r->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-900 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>Laporkan Realisasi</a>@endif</div>
                        @endif
                    </div>
                @empty
                    <div class="px-6 py-14 text-center">
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
    <p class="font-semibold text-slate-800">Belum ada riwayat pengajuan</p>
    <p class="mt-1 text-sm text-slate-500">Pengajuan perjalanan dinas Anda akan muncul di sini.</p>
    <a href="{{ route('perjalanan.buat') }}" class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>Buat Pengajuan Baru</a>
</div>
                @endforelse
            </div>
            @if ($riwayats->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $riwayats->links() }}</div>
            @endif
        </div>
    </div>

</div>