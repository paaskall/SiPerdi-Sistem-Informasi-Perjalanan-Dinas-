<div class="py-8 sm:py-10">
    @php
        $ini = fn ($n) => mb_strtoupper(collect(preg_split('/\s+/', trim((string) $n)))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode(''));
    @endphp

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg></div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Persetujuan Perjalanan Dinas</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Tinjau pengajuan yang masuk, lalu setujui atau tolak.</p>
                </div>
            </div>
        </div>
        @if (session()->has('message'))
            <div class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif
        <div class="mb-6 grid grid-cols-2 gap-3 sm:gap-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><p class="text-xs font-medium text-slate-500">Menunggu Review</p><p class="mt-1 text-2xl font-extrabold text-blue-900 sm:text-3xl">{{ $pengajuans->total() }}</p></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><p class="text-xs font-medium text-slate-500">Total Estimasi</p><p class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">Rp {{ number_format($pengajuans->getCollection()->sum('total_estimasi'), 0, ',', '.') }}</p><p class="mt-0.5 text-[11px] text-slate-400">di halaman ini</p></div>
        </div>
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-4 sm:p-5"><p class="text-sm text-slate-500">Menampilkan <span class="font-semibold text-slate-700">{{ $pengajuans->firstItem() ?? 0 }}&ndash;{{ $pengajuans->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700">{{ $pengajuans->total() }}</span> data</p></div>
            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500"><tr>
                            <th scope="col" class="px-6 py-3 font-semibold">Pegawai</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Tujuan</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Tanggal</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Estimasi</th>
                            <th scope="col" class="px-6 py-3 font-semibold text-right">Aksi</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pengajuans as $p)
                            <tr wire:key="row-{{ $p->id }}" class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4"><div class="flex items-center gap-3"><div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-800">{{ $ini($p->pegawai->name) }}</div><div class="min-w-0"><div class="font-semibold text-slate-900">{{ $p->pegawai->name }}</div><div class="text-xs text-slate-400">{{ $p->nomor_pengajuan }} &middot; {{ $p->divisi->nama ?? '-' }}</div></div></div></td>
                                <td class="px-6 py-4 text-slate-600">{{ $p->kotaTujuan->nama }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-slate-600">{{ $p->tanggal_berangkat->format('d/m/Y') }} &ndash; {{ $p->tanggal_pulang->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">Rp {{ number_format($p->total_estimasi, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right"><button type="button" wire:click="openModal({{ $p->id }})" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-900 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>Review</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><div class="px-6 py-14 text-center">
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
    <p class="font-semibold text-slate-800">Tidak ada pengajuan baru</p>
    <p class="mt-1 text-sm text-slate-500">Pengajuan yang perlu ditinjau akan muncul di sini.</p>
</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-slate-100 sm:hidden">
                @forelse ($pengajuans as $p)
                    <div wire:key="card-{{ $p->id }}" class="p-4">
                        <div class="flex items-center gap-3"><div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-800">{{ $ini($p->pegawai->name) }}</div><div class="min-w-0"><div class="font-semibold text-slate-900">{{ $p->pegawai->name }}</div><div class="text-xs text-slate-400">{{ $p->nomor_pengajuan }} &middot; {{ $p->divisi->nama ?? '-' }}</div></div></div>
                        <dl class="mt-3 space-y-1.5 border-t border-slate-100 pt-3"><div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">Tujuan</dt><dd class="text-right text-slate-800">{{ $p->kotaTujuan->nama }}</dd></div><div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">Tanggal</dt><dd class="text-right text-slate-800">{{ $p->tanggal_berangkat->format('d/m/Y') }} &ndash; {{ $p->tanggal_pulang->format('d/m/Y') }}</dd></div><div class="flex items-start justify-between gap-4 text-sm"><dt class="shrink-0 text-slate-500">Estimasi</dt><dd class="text-right text-slate-800"><span class="font-semibold text-slate-900">Rp {{ number_format($p->total_estimasi, 0, ',', '.') }}</span></dd></div></dl>
                        <div class="mt-4 [&>button]:w-full [&>button]:py-2.5"><button type="button" wire:click="openModal({{ $p->id }})" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-900 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>Review</button></div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center">
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
    <p class="font-semibold text-slate-800">Tidak ada pengajuan baru</p>
    <p class="mt-1 text-sm text-slate-500">Pengajuan yang perlu ditinjau akan muncul di sini.</p>
</div>
                @endforelse
            </div>
            @if ($pengajuans->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $pengajuans->links() }}</div>
            @endif
        </div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 backdrop-blur-sm sm:items-center sm:p-4" wire:keydown.escape.window="closeModal" role="dialog" aria-modal="true">
        <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-t-2xl bg-white shadow-xl sm:rounded-2xl">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg></div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Review Pengajuan</h3>
                        <p class="text-xs text-slate-500">Berikan keputusan atas pengajuan ini.</p>
                    </div>
                </div>
                <button type="button" wire:click="closeModal" aria-label="Tutup" class="rounded-md p-1 text-slate-400 transition-colors hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
                        <div class="p-5 sm:p-6">
                <label for="catatan_keuangan" class="block text-sm font-semibold text-slate-800">Catatan Keuangan</label>
                <p class="mt-0.5 text-xs text-slate-500">Wajib diisi jika menolak pengajuan.</p>
                <textarea wire:model="catatan_keuangan" id="catatan_keuangan" rows="4" placeholder="Tambahkan catatan..." class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('catatan_keuangan') ? 'border-red-400' : 'border-slate-300' }}"></textarea>
                <x-input-error :messages="$errors->get('catatan_keuangan')" class="mt-2" />
            </div>
            <div class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                <button type="button" wire:click="closeModal" class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-semibold transition-colors disabled:opacity-60 border border-slate-300 bg-white text-slate-700 hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Batal</button>
                <button type="button" wire:click="tolak" wire:loading.attr="disabled" class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-semibold transition-colors disabled:opacity-60 border border-red-300 bg-white text-red-700 hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">Tolak Pengajuan</button>
                <button type="button" wire:click="setujui" wire:loading.attr="disabled" class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-semibold transition-colors disabled:opacity-60 bg-blue-900 text-white hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Setujui Pengajuan</button>
            </div>
        </div>
    </div>
    @endif
</div>