<div class="py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Laporan Perjalanan Saya</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Daftar semua perjalanan dinas yang perlu dan sudah dilaporkan.</p>
                </div>
            </div>
        </div>

        <!-- Counter -->
        <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-medium text-slate-500">Belum Dilaporkan</p>
                <p class="mt-1 text-2xl font-extrabold text-amber-600 sm:text-3xl">{{ $perjalanans->getCollection()->filter(fn($p) => !$p->laporan)->count() }}</p>
                <p class="mt-0.5 text-[11px] text-slate-400">di halaman ini</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-medium text-slate-500">Sudah Dilaporkan</p>
                <p class="mt-1 text-2xl font-extrabold text-emerald-600 sm:text-3xl">{{ $perjalanans->getCollection()->filter(fn($p) => $p->laporan)->count() }}</p>
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
                            <th class="px-6 py-3 font-semibold">Tujuan</th>
                            <th class="px-6 py-3 font-semibold">Tanggal</th>
                            <th class="px-6 py-3 font-semibold">Status Laporan</th>
                            <th class="px-6 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($perjalanans as $p)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        </div>
                                        <span class="font-semibold text-slate-900">{{ $p->nomor_pengajuan }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $p->kotaTujuan->nama }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-slate-600">{{ $p->tanggal_berangkat->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    @if($p->laporan)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Sudah Dilaporkan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Belum Dilaporkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($p->laporan)
                                        <a href="{{ asset('storage/' . $p->laporan->file_lampiran) }}" target="_blank" class="text-sm font-semibold text-blue-900 hover:text-blue-700">
                                            Lihat Lampiran
                                        </a>
                                    @else
                                        <a href="{{ route('perjalanan.lapor', ['id' => $p->id, 'from' => 'laporan']) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-white border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50 hover:text-blue-900">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Laporkan Sekarang
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-14 text-center text-slate-500">
                                    Belum ada perjalanan dinas yang selesai. Laporan akan tersedia di sini setelah UMK dicetak.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($perjalanans->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $perjalanans->links() }}</div>
            @endif
        </div>

        <!-- Card List (Mobile) -->
        <div class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm sm:hidden">
            @forelse($perjalanans as $p)
                <div wire:key="card-{{ $p->id }}" class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </div>
                            <div class="font-semibold text-slate-900">{{ $p->nomor_pengajuan }}</div>
                        </div>
                        @if($p->laporan)
                            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Sudah Dilaporkan
                            </span>
                        @else
                            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Belum
                            </span>
                        @endif
                    </div>
                    <dl class="mt-3 space-y-1.5 border-t border-slate-100 pt-3">
                        <div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">Tujuan</dt><dd class="text-right text-slate-800">{{ $p->kotaTujuan->nama }}</dd></div>
                        <div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">Tanggal</dt><dd class="text-right text-slate-800">{{ $p->tanggal_berangkat->format('d/m/Y') }}</dd></div>
                    </dl>
                    <div class="mt-4 flex flex-col gap-2">
                        @if($p->laporan)
                            <a href="{{ asset('storage/' . $p->laporan->file_lampiran) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-white border border-slate-300 px-3 py-2.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50 hover:text-blue-900">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Lihat Lampiran
                            </a>
                        @else
                            <a href="{{ route('perjalanan.lapor', ['id' => $p->id, 'from' => 'laporan']) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-900 px-3 py-2.5 text-xs font-semibold text-white transition-colors hover:bg-blue-800">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Laporkan Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-14 text-center">
                    <p class="font-semibold text-slate-800">Belum ada perjalanan dinas</p>
                    <p class="mt-1 text-sm text-slate-500">Laporan akan muncul setelah UMK Anda dicetak.</p>
                </div>
            @endforelse
            @if($perjalanans->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">{{ $perjalanans->links() }}</div>
            @endif
        </div>
    </div>
</div>