<div class="py-8 sm:py-10">
    @php
        $ini = fn ($n) => mb_strtoupper(collect(preg_split('/\s+/', trim((string) $n)))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode(''));
    @endphp

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Master Data Tarif Harian</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Kelola nominal tarif harian per kota dan golongan.</p>
                </div>
            </div>
            <button type="button" wire:click="openModal" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>Tambah Tarif</button>
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
            <p class="text-sm text-slate-500">Menampilkan <span class="font-semibold text-slate-700">{{ $tarifs->firstItem() ?? 0 }}&ndash;{{ $tarifs->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700">{{ $tarifs->total() }}</span> data</p>
            </div>

            <!-- Tabel (tablet & desktop) -->
            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th scope="col" class="w-16 px-6 py-3 font-semibold">No</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Kota Tujuan</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Golongan</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Nominal</th>
                            <th scope="col" class="w-44 px-6 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tarifs as $index => $t)
                            <tr wire:key="row-{{ $t->id }}" class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 text-slate-400">{{ $tarifs->firstItem() + $index }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3"><div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div><span class="font-semibold text-slate-900">{{ $t->kota->nama ?? '-' }}</span></div>
                                </td>
                                <td class="px-6 py-4 text-slate-600"><span class="inline-flex rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-800 ring-1 ring-inset ring-blue-200">{{ $t->golongan->nama ?? '-' }}</span></td>
                                <td class="px-6 py-4 text-slate-600"><span class="font-semibold text-slate-900">Rp {{ number_format($t->nominal, 0, ',', '.') }}</span></td>
                                <td class="px-6 py-4"><div class="flex gap-2"><button type="button" wire:click="edit({{ $t->id }})" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>Edit</button><button type="button" x-data
                                                @click.prevent="
                                                    Swal.fire({
                                                        title: 'Hapus Tarif?',
                                                        text: 'Data yang dihapus tidak dapat dikembalikan!',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#dc2626',
                                                        cancelButtonColor: '#64748b',
                                                        confirmButtonText: 'Ya, Hapus!',
                                                        cancelButtonText: 'Batal'
                                                    }).then((r) => { if (r.isConfirmed) { $wire.delete({{ $t->id }}) } })
                                                " class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Hapus</button></div></td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><div class="px-6 py-14 text-center">
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
    <p class="font-semibold text-slate-800">Belum ada data tarif</p>
    <p class="mt-1 text-sm text-slate-500">Mulai dengan menambahkan data pertama.</p>
    <button type="button" wire:click="openModal" class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>Tambah Tarif</button>
</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Daftar kartu (mobile) -->
            <div class="divide-y divide-slate-100 sm:hidden">
                @forelse ($tarifs as $t)
                    <div wire:key="card-{{ $t->id }}" class="p-4">
                        <div class="flex items-center gap-3"><div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div><div class="min-w-0 truncate font-semibold text-slate-900">{{ $t->kota->nama ?? '-' }}</div></div>
                        <dl class="mt-3 space-y-1.5 border-t border-slate-100 pt-3"><div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Golongan</dt><dd class="text-right text-slate-800"><span class="inline-flex rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-800 ring-1 ring-inset ring-blue-200">{{ $t->golongan->nama ?? '-' }}</span></dd></div><div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Nominal</dt><dd class="text-right text-slate-800"><span class="font-semibold text-slate-900">Rp {{ number_format($t->nominal, 0, ',', '.') }}</span></dd></div></dl>
                        <div class="mt-4 grid grid-cols-2 gap-2"><button type="button" wire:click="edit({{ $t->id }})" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 py-2.5"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>Edit</button><button type="button" x-data
                                                @click.prevent="
                                                    Swal.fire({
                                                        title: 'Hapus Tarif?',
                                                        text: 'Data yang dihapus tidak dapat dikembalikan!',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#dc2626',
                                                        cancelButtonColor: '#64748b',
                                                        confirmButtonText: 'Ya, Hapus!',
                                                        cancelButtonText: 'Batal'
                                                    }).then((r) => { if (r.isConfirmed) { $wire.delete({{ $t->id }}) } })
                                                " class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2 py-2.5"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Hapus</button></div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center">
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
    <p class="font-semibold text-slate-800">Belum ada data tarif</p>
    <p class="mt-1 text-sm text-slate-500">Mulai dengan menambahkan data pertama.</p>
    <button type="button" wire:click="openModal" class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>Tambah Tarif</button>
</div>
                @endforelse
            </div>

            @if ($tarifs->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $tarifs->links() }}</div>
            @endif
        </div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 backdrop-blur-sm sm:items-center sm:p-4" wire:keydown.escape.window="closeModal" role="dialog" aria-modal="true">
        <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-t-2xl bg-white shadow-xl sm:rounded-2xl">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-800"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ $tarifId ? 'Edit Tarif' : 'Tambah Tarif' }}</h3>
                        <p class="text-xs text-slate-500">Lengkapi data di bawah ini.</p>
                    </div>
                </div>
                <button type="button" wire:click="closeModal" aria-label="Tutup" class="rounded-md p-1 text-slate-400 transition-colors hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form wire:submit="save">
                <div class="space-y-5 p-5 sm:p-6">
                    <div>
                        <label for="kota_id" class="block text-sm font-semibold text-slate-800">Kota Tujuan</label>
                        <select wire:model="kota_id" id="kota_id" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('kota_id') ? 'border-red-400' : 'border-slate-300' }}">
                            <option value="">-- Pilih Kota --</option>
                            @foreach($kotas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('kota_id')" class="mt-2" />
                    </div>
                    <div>
                        <label for="golongan_id" class="block text-sm font-semibold text-slate-800">Golongan Pegawai</label>
                        <select wire:model="golongan_id" id="golongan_id" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('golongan_id') ? 'border-red-400' : 'border-slate-300' }}">
                            <option value="">-- Pilih Golongan --</option>
                            @foreach($golongans as $g)
                                <option value="{{ $g->id }}">{{ $g->nama }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('golongan_id')" class="mt-2" />
                    </div>
                    <div>
                        <label for="nominal" class="block text-sm font-semibold text-slate-800">Nominal Tarif (Rp)</label>
                        <input wire:model="nominal" id="nominal" type="number" min="0" placeholder="Contoh: 150000" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('nominal') ? 'border-red-400' : 'border-slate-300' }}">
                        <x-input-error :messages="$errors->get('nominal')" class="mt-2" />
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