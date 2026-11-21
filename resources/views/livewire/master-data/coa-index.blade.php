<x-ui.page>
    <x-ui.page-header
        title="Master Data COA"
        desc="Kelola Chart of Accounts untuk kategori biaya."
        icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
        <x-slot:action>
            <button type="button" wire:click="openModal" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>
                Tambah COA
            </button>
        </x-slot:action>
    </x-ui.page-header>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-4 sm:p-5">
            <div class="relative w-full sm:w-72">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari kode atau nama COA..."
                    class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="w-16 px-6 py-3 font-semibold">No</th>
                        <th class="px-6 py-3 font-semibold">Kode</th>
                        <th class="px-6 py-3 font-semibold">Nama</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                        <th class="w-40 px-6 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($coas as $index => $c)
                        <tr wire:key="row-{{ $c->id }}" class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-slate-400">{{ $coas->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $c->kode }}</td>
                            <td class="px-6 py-4 text-slate-700">{{ $c->nama }}</td>
                            <td class="px-6 py-4">
                                @if ($c->aktif)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Aktif</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button wire:click="edit({{ $c->id }})" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-blue-900 hover:text-blue-900">Edit</button>
                                    <button wire:click="delete({{ $c->id }})" wire:confirm="Yakin hapus COA ini?" class="inline-flex items-center rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-14 text-center text-slate-500">Belum ada data COA.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($coas->hasPages())
            <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $coas->links() }}</div>
        @endif
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" wire:click.self="closeModal" role="dialog" aria-modal="true">
            <div class="w-full max-w-md rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h3 class="text-base font-bold text-slate-900">{{ $modelId ? 'Edit COA' : 'Tambah COA' }}</h3>
                    <button type="button" wire:click="closeModal" class="rounded-md p-1 text-slate-400 hover:text-slate-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-800 mb-1">Kode COA</label>
                        <input wire:model="form.kode" type="text" class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm @error('form.kode') border-red-400 @enderror">
                        @error('form.kode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-800 mb-1">Nama COA</label>
                        <input wire:model="form.nama" type="text" class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm @error('form.nama') border-red-400 @enderror">
                        @error('form.nama') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="form.aktif" class="rounded border-slate-300 text-blue-900 focus:ring-blue-900">
                            <span class="text-sm font-medium text-slate-700">Aktif</span>
                        </label>
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