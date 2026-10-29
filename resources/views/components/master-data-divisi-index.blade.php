<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('message') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Master Data Divisi</h3>
                    <div class="flex gap-2">
                        <x-text-input wire:model.live.debounce.300ms="search" placeholder="Cari divisi..." class="w-64" />
                        <x-primary-button wire:click="openModal">
                            + Tambah Divisi
                        </x-primary-button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 w-16">No</th>
                                <th class="px-6 py-3">Kode</th>
                                <th class="px-6 py-3">Nama Divisi</th>
                                <th class="px-6 py-3 w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($divisis as $index => $d)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $divisis->firstItem() + $index }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $d->kode }}</td>
                                    <td class="px-6 py-4">{{ $d->nama }}</td>
                                    <td class="px-6 py-4 flex gap-2">
                                        <button wire:click="edit({{ $d->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <button wire:click="delete({{ $d->id }})" wire:confirm="Yakin hapus divisi ini?" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center">Belum ada data divisi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $divisis->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold">{{ $divisiId ? 'Edit Divisi' : 'Tambah Divisi' }}</h3>
                <button wire:click="closeModal" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form wire:submit="save">
                <div class="p-4 space-y-4">
                    <div>
                        <x-input-label for="kode" value="Kode Divisi" />
                        <x-text-input wire:model="kode" id="kode" class="block mt-1 w-full" type="text" placeholder="Contoh: KEU, UMUM" />
                        <x-input-error :messages="$errors->get('kode')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="nama" value="Nama Divisi" />
                        <x-text-input wire:model="nama" id="nama" class="block mt-1 w-full" type="text" placeholder="Contoh: Keuangan" />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>
                </div>
                <div class="flex justify-end p-4 border-t gap-2">
                    <x-secondary-button wire:click="closeModal">Batal</x-secondary-button>
                    <x-primary-button type="submit">Simpan</x-primary-button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>