<div class="py-8 sm:py-10">
    @php
        $ini = fn ($n) => mb_strtoupper(collect(preg_split('/\s+/', trim((string) $n)))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode(''));
    @endphp

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Master Data Divisi</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Kelola daftar divisi instansi.</p>
                </div>
            </div>
            <button type="button" wire:click="openModal" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>Tambah Divisi</button>
        </div>
        @if (session()->has('message'))
            <div class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <!-- Toolbar -->
            <div class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div class="relative w-full sm:max-w-xs">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari divisi..." aria-label="Cari divisi..." class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20">
            </div>
            <p class="text-sm text-slate-500">Menampilkan <span class="font-semibold text-slate-700">{{ $divisis->firstItem() ?? 0 }}&ndash;{{ $divisis->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700">{{ $divisis->total() }}</span> data</p>
            </div>

            <!-- Tabel (tablet & desktop) -->
            <div class="hidden overflow-x-auto sm:block" wire:loading.class="opacity-60" wire:target="search">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th scope="col" class="w-16 px-6 py-3 font-semibold">No</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Nama Divisi</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Kode</th>
                            <th scope="col" class="w-44 px-6 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($divisis as $index => $d)
                            <tr wire:key="row-{{ $d->id }}" class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 text-slate-400">{{ $divisis->firstItem() + $index }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3"><div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div><span class="font-semibold text-slate-900">{{ $d->nama }}</span></div>
                                </td>
                                <td class="px-6 py-4 text-slate-600"><span class="inline-flex rounded-md bg-slate-100 px-2 py-0.5 font-mono text-xs font-semibold text-slate-700">{{ $d->kode }}</span></td>
                                <td class="px-6 py-4"><div class="flex gap-2"><button type="button" wire:click="edit({{ $d->id }})" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>Edit</button><button type="button" x-data
                                                @click.prevent="
                                                    Swal.fire({
                                                        title: 'Hapus Divisi?',
                                                        text: 'Data yang dihapus tidak dapat dikembalikan!',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#dc2626',
                                                        cancelButtonColor: '#64748b',
                                                        confirmButtonText: 'Ya, Hapus!',
                                                        cancelButtonText: 'Batal'
                                                    }).then((r) => { if (r.isConfirmed) { $wire.delete({{ $d->id }}) } })
                                                " class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Hapus</button></div></td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><div class="px-6 py-14 text-center">
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
    <p class="font-semibold text-slate-800">Belum ada data divisi</p>
    <p class="mt-1 text-sm text-slate-500">Mulai dengan menambahkan data pertama.</p>
    <button type="button" wire:click="openModal" class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>Tambah Divisi</button>
</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Daftar kartu (mobile) -->
            <div class="divide-y divide-slate-100 sm:hidden" wire:loading.class="opacity-60" wire:target="search">
                @forelse ($divisis as $d)
                    <div wire:key="card-{{ $d->id }}" class="p-4">
                        <div class="flex items-center gap-3"><div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div><div class="min-w-0 truncate font-semibold text-slate-900">{{ $d->nama }}</div></div>
                        <dl class="mt-3 space-y-1.5 border-t border-slate-100 pt-3"><div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Kode</dt><dd class="text-right text-slate-800"><span class="inline-flex rounded-md bg-slate-100 px-2 py-0.5 font-mono text-xs font-semibold text-slate-700">{{ $d->kode }}</span></dd></div></dl>
                        <div class="mt-4 grid grid-cols-2 gap-2"><button type="button" wire:click="edit({{ $d->id }})" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 py-2.5"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>Edit</button><button type="button" x-data
                                                @click.prevent="
                                                    Swal.fire({
                                                        title: 'Hapus Divisi?',
                                                        text: 'Data yang dihapus tidak dapat dikembalikan!',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#dc2626',
                                                        cancelButtonColor: '#64748b',
                                                        confirmButtonText: 'Ya, Hapus!',
                                                        cancelButtonText: 'Batal'
                                                    }).then((r) => { if (r.isConfirmed) { $wire.delete({{ $d->id }}) } })
                                                " class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2 py-2.5"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Hapus</button></div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center">
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
    <p class="font-semibold text-slate-800">Belum ada data divisi</p>
    <p class="mt-1 text-sm text-slate-500">Mulai dengan menambahkan data pertama.</p>
    <button type="button" wire:click="openModal" class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>Tambah Divisi</button>
</div>
                @endforelse
            </div>

            @if ($divisis->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $divisis->links() }}</div>
            @endif
        </div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 backdrop-blur-sm sm:items-center sm:p-4" wire:keydown.escape.window="closeModal" role="dialog" aria-modal="true">
        <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-t-2xl bg-white shadow-xl sm:rounded-2xl">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ $divisiId ? 'Edit Divisi' : 'Tambah Divisi' }}</h3>
                        <p class="text-xs text-slate-500">Lengkapi data di bawah ini.</p>
                    </div>
                </div>
                <button type="button" wire:click="closeModal" aria-label="Tutup" class="rounded-md p-1 text-slate-400 transition-colors hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form wire:submit="save">
                <div class="space-y-5 p-5 sm:p-6">
                    <div>
                        <label for="kode" class="block text-sm font-semibold text-slate-800">Kode Divisi</label>
                        <input wire:model="kode" id="kode" type="text" placeholder="Contoh: KEU" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('kode') ? 'border-red-400' : 'border-slate-300' }}">
                        <x-input-error :messages="$errors->get('kode')" class="mt-2" />
                    </div>
                    <div>
                        <label for="nama" class="block text-sm font-semibold text-slate-800">Nama Divisi</label>
                        <input wire:model="nama" id="nama" type="text" placeholder="Contoh: Keuangan" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('nama') ? 'border-red-400' : 'border-slate-300' }}">
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                </div>
            <div class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                <button type="button" wire:click="closeModal" class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-semibold transition-colors disabled:opacity-60 border border-slate-300 bg-white text-slate-700 hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Batal</button>
                <button type="submit" wire:loading.attr="disabled" wire:target="save" class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-semibold transition-colors disabled:opacity-60 bg-blue-900 text-white hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Simpan</button>
            </div>
            </form>
        </div>
    </div>
    @endif
</div>