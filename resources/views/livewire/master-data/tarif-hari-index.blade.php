<x-ui.page>
    <x-ui.page-header
        title="Master Data Tarif Harian"
        desc="Kelola tarif uang harian per kota dan golongan."
        icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
        <x-slot:action>
            <button type="button" wire:click="openModal" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>
                Tambah Tarif
            </button>
        </x-slot:action>
    </x-ui.page-header>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-4 sm:p-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari kota..."
                    class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20">
            </div>
            <select wire:model.live="filterProvinsi" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                <option value="">Semua Provinsi</option>
                @foreach ($provinsis as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterGolongan" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                <option value="">Semua Golongan</option>
                @foreach ($golongans as $g)
                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="w-16 px-6 py-3 font-semibold">No</th>
                        <th class="px-6 py-3 font-semibold">Kota</th>
                        <th class="px-6 py-3 font-semibold">Golongan</th>
                        <th class="px-6 py-3 font-semibold text-right">Nominal</th>
                        <th class="w-40 px-6 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tarifs as $index => $t)
                        <tr wire:key="row-{{ $t->id }}" class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-slate-400">{{ $tarifs->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $t->kota->nama ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-700">{{ $t->golongan->nama ?? '-' }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-900"><x-ui.rupiah :value="$t->nominal" /></td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button wire:click="edit({{ $t->id }})" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-blue-900 hover:text-blue-900">Edit</button>
                                    <button wire:click="delete({{ $t->id }})" wire:confirm="Yakin hapus tarif ini?" class="inline-flex items-center rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-14 text-center text-slate-500">Belum ada data tarif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tarifs->hasPages())
            <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $tarifs->links() }}</div>
        @endif
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" wire:click.self="closeModal" role="dialog" aria-modal="true">
            <div class="w-full max-w-md rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h3 class="text-base font-bold text-slate-900">{{ $modelId ? 'Edit Tarif' : 'Tambah Tarif' }}</h3>
                    <button type="button" wire:click="closeModal" class="rounded-md p-1 text-slate-400 hover:text-slate-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-800 mb-1">Kota</label>
                        <select wire:model="form.kota_id" class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm @error('form.kota_id') border-red-400 @enderror">
                            <option value="">-- Pilih Kota --</option>
                            @foreach ($kotas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}{{ $k->provinsi ? ' - ' . $k->provinsi : '' }}</option>
                            @endforeach
                        </select>
                        @error('form.kota_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-800 mb-1">Golongan</label>
                        <select wire:model="form.golongan_id" class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm @error('form.golongan_id') border-red-400 @enderror">
                            <option value="">-- Pilih Golongan --</option>
                            @foreach ($golongans as $g)
                                <option value="{{ $g->id }}">{{ $g->nama }}</option>
                            @endforeach
                        </select>
                        @error('form.golongan_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-800 mb-1">Nominal (Rp)</label>
                        <input wire:model="form.nominal" type="number" min="0" class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm @error('form.nominal') border-red-400 @enderror">
                        @error('form.nominal') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button type="button" wire:click="closeModal" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-blue-900 hover:text-blue-900">Batal</button>
                        <button type="submit" class="rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-ui.page>