<div class="py-8 sm:py-10">
    @php
        $ini = fn ($n) => mb_strtoupper(collect(preg_split('/\s+/', trim((string) $n)))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode(''));
    @endphp

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Persetujuan &amp; Tanda Tangan SPPD</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Tinjau dokumen SPPD dan berikan tanda tangan pengesahan.</p>
                </div>
            </div>
        </div>
        @if (session()->has('message'))
            <div class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><span>{{ session('error') }}</span></div>
        @endif
        <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><p class="text-xs font-medium text-slate-500">Total Pengajuan</p><p class="mt-1 text-2xl font-extrabold text-blue-900 sm:text-3xl">{{ $pengajuans->total() }}</p></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><p class="text-xs font-medium text-slate-500">Menunggu TTD</p><p class="mt-1 text-2xl font-extrabold text-amber-600 sm:text-3xl">{{ $pengajuans->getCollection()->where('status', 'FINANCE_APPROVED')->count() }}</p><p class="mt-0.5 text-[11px] text-slate-400">di halaman ini</p></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><p class="text-xs font-medium text-slate-500">Total Estimasi</p><p class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">Rp {{ number_format($pengajuans->getCollection()->sum('total_estimasi'), 0, ',', '.') }}</p><p class="mt-0.5 text-[11px] text-slate-400">di halaman ini</p></div>
        </div>
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-4 sm:p-5"><p class="text-sm text-slate-500">Menampilkan <span class="font-semibold text-slate-700">{{ $pengajuans->firstItem() ?? 0 }}&ndash;{{ $pengajuans->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700">{{ $pengajuans->total() }}</span> data</p></div>
            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500"><tr>
                            <th scope="col" class="px-6 py-3 font-semibold">Pegawai</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Tujuan</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Estimasi</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-3 font-semibold text-right">Aksi</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pengajuans as $p)
                            <tr wire:key="row-{{ $p->id }}" class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4"><div class="flex items-center gap-3"><div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-800">{{ $ini($p->pegawai->name) }}</div><div class="min-w-0"><div class="font-semibold text-slate-900">{{ $p->pegawai->name }}</div><div class="text-xs text-slate-400">{{ $p->nomor_pengajuan }} &middot; {{ $p->divisi->nama ?? '-' }}</div></div></div></td>
                                <td class="px-6 py-4 text-slate-600">{{ $p->kotaTujuan->nama }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">Rp {{ number_format($p->total_estimasi, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">@if($p->status === 'FINANCE_APPROVED')<span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">Menunggu TTD</span>@else<span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Sudah TTD</span>@endif</td>
                                <td class="px-6 py-4"><div class="flex justify-end gap-2"><a href="{{ route('sppd.pdf', $p->id) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Lihat PDF</a>@if($p->status === 'FINANCE_APPROVED')<button type="button" x-data
                                                @click.prevent="
                                                    Swal.fire({
                                                        title: 'Tanda Tangani SPPD?',
                                                        text: 'Dokumen PDF akan segera diterbitkan dengan tanda tangan Anda.',
                                                        icon: 'question',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#1e3a8a',
                                                        cancelButtonColor: '#64748b',
                                                        confirmButtonText: 'Ya, Tanda Tangani',
                                                        cancelButtonText: 'Batal'
                                                    }).then((r) => { if (r.isConfirmed) { $wire.setujui({{ $p->id }}) } })
                                                " class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-900 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>Tanda Tangani</button>@endif</div></td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><div class="px-6 py-14 text-center">
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
    <p class="font-semibold text-slate-800">Belum ada pengajuan</p>
    <p class="mt-1 text-sm text-slate-500">Pengajuan yang menunggu tanda tangan akan muncul di sini.</p>
</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-slate-100 sm:hidden">
                @forelse ($pengajuans as $p)
                    <div wire:key="card-{{ $p->id }}" class="p-4">
                        <div class="flex items-start justify-between gap-3"><div class="flex items-center gap-3"><div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-800">{{ $ini($p->pegawai->name) }}</div><div class="min-w-0"><div class="font-semibold text-slate-900">{{ $p->pegawai->name }}</div><div class="text-xs text-slate-400">{{ $p->nomor_pengajuan }} &middot; {{ $p->divisi->nama ?? '-' }}</div></div></div>@if($p->status === 'FINANCE_APPROVED')<span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">Menunggu TTD</span>@else<span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Sudah TTD</span>@endif</div>
                        <dl class="mt-3 space-y-1.5 border-t border-slate-100 pt-3"><div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">No. Pengajuan</dt><dd class="text-right text-slate-800">{{ $p->nomor_pengajuan }}</dd></div><div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">Tujuan</dt><dd class="text-right text-slate-800">{{ $p->kotaTujuan->nama }}</dd></div><div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">Estimasi</dt><dd class="text-right text-slate-800"><span class="font-semibold text-slate-900">Rp {{ number_format($p->total_estimasi, 0, ',', '.') }}</span></dd></div></dl>
                        <div class="mt-4 flex flex-col gap-2"><a href="{{ route('sppd.pdf', $p->id) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 py-2.5"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Lihat PDF</a>@if($p->status === 'FINANCE_APPROVED')<button type="button" x-data
                                                @click.prevent="
                                                    Swal.fire({
                                                        title: 'Tanda Tangani SPPD?',
                                                        text: 'Dokumen PDF akan segera diterbitkan dengan tanda tangan Anda.',
                                                        icon: 'question',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#1e3a8a',
                                                        cancelButtonColor: '#64748b',
                                                        confirmButtonText: 'Ya, Tanda Tangani',
                                                        cancelButtonText: 'Batal'
                                                    }).then((r) => { if (r.isConfirmed) { $wire.setujui({{ $p->id }}) } })
                                                " class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-900 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 py-2.5"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>Tanda Tangani</button>@endif</div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center">
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
    <p class="font-semibold text-slate-800">Belum ada pengajuan</p>
    <p class="mt-1 text-sm text-slate-500">Pengajuan yang menunggu tanda tangan akan muncul di sini.</p>
</div>
                @endforelse
            </div>
            @if ($pengajuans->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $pengajuans->links() }}</div>
            @endif
        </div>
    </div>

</div>