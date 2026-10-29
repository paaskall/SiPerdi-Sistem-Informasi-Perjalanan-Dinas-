<div class="py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        @if (session()->has('message'))
            <div class="mb-5 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

            <!-- Header -->
            <div class="flex flex-col gap-4 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div>
                    <h2 class="text-lg font-bold tracking-tight text-slate-900">Master Data Kota</h2>
                    <p class="mt-1 text-sm text-slate-500">Kelola kota tujuan dan provinsinya.</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                        <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari kota/provinsi..." aria-label="Cari kota/provinsi..." class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 sm:w-64">
                    </div>
                    <button type="button" wire:click="openModal" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
                        Tambah Kota
                    </button>
                </div>
            </div>

            <!-- Tabel (tablet & desktop) -->
            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th scope="col" class="w-16 px-6 py-3 font-semibold">No</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Nama Kota</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Provinsi</th>
                            <th scope="col" class="w-40 px-6 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($kotas as $index => $k)
                            <tr wire:key="row-{{ $k->id }}" class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 text-slate-500">{{ $kotas->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $k->nama }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $k->provinsi ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button type="button" wire:click="edit({{ $k->id }})" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Edit</button>
                                        <button type="button" wire:click="delete({{ $k->id }})" wire:confirm="Yakin hapus?" class="rounded-md border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Daftar kartu (mobile) -->
            <div class="divide-y divide-slate-100 sm:hidden">
                @forelse ($kotas as $k)
                    <div wire:key="card-{{ $k->id }}" class="p-4">
                        <div class="font-semibold text-slate-900">{{ $k->nama }}</div>
                <dl class="mt-3 space-y-1.5">
                    <div class="flex items-center justify-between gap-4 text-sm"><dt class="text-slate-500">Provinsi</dt><dd class="text-right text-slate-800">{{ $k->provinsi ?? '-' }}</dd></div>
                </dl>
                        <div class="mt-4 flex gap-2">
                            <button type="button" wire:click="edit({{ $k->id }})" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Edit</button>
                            <button type="button" wire:click="delete({{ $k->id }})" wire:confirm="Yakin hapus?" class="rounded-md border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Hapus</button>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data.</div>
                @endforelse
            </div>

            @if ($kotas->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $kotas->links() }}</div>
            @endif
        </div>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 sm:items-center sm:p-4" wire:keydown.escape.window="closeModal" role="dialog" aria-modal="true">
        <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-t-2xl bg-white sm:rounded-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="text-base font-bold text-slate-900">{{ $kotaId ? 'Edit Kota' : 'Tambah Kota' }}</h3>
                <button type="button" wire:click="closeModal" aria-label="Tutup" class="rounded-md p-1 text-slate-400 transition-colors hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save">
                <div class="space-y-5 p-5 sm:p-6">
                    <div>
                        <label for="nama" class="block text-sm font-semibold text-slate-800">Nama Kota</label>
                        <input wire:model="nama" id="nama" type="text" placeholder="Contoh: Jakarta" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('nama') ? 'border-red-400' : 'border-slate-300' }}">
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                    <div>
                        <label for="provinsi" class="block text-sm font-semibold text-slate-800">Provinsi (Opsional)</label>
                        <input wire:model="provinsi" id="provinsi" type="text" placeholder="Contoh: DKI Jakarta" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('provinsi') ? 'border-red-400' : 'border-slate-300' }}">
                        <x-input-error :messages="$errors->get('provinsi')" class="mt-2" />
                    </div>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-slate-200 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                    <button type="button" wire:click="closeModal" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save" class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800 disabled:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>