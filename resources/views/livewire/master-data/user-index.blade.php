<x-ui.page>
    <x-ui.page-header
        title="Manajemen User"
        desc="Kelola akun pengguna aplikasi SIPERDI."
        icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
        <x-slot:action>
            <button type="button" wire:click="openModal"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>
                Tambah User
            </button>
        </x-slot:action>
    </x-ui.page-header>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-4 sm:p-5">
            <div class="relative w-full sm:w-72">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari nama, NIP, username, email..."
                    class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="w-16 px-6 py-3 font-semibold">No</th>
                        <th class="px-6 py-3 font-semibold">Nama</th>
                        <th class="px-6 py-3 font-semibold">Username / NIP</th>
                        <th class="px-6 py-3 font-semibold">Divisi / Jabatan</th>
                        <th class="px-6 py-3 font-semibold">Role</th>
                        <th class="w-40 px-6 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $index => $u)
                        <tr wire:key="row-{{ $u->id }}" class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-slate-400">{{ $users->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                                <div class="text-xs text-slate-500">{{ $u->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                <div>{{ $u->username }}</div>
                                <div class="text-xs text-slate-500">{{ $u->nip ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                <div>{{ $u->divisi->nama ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $u->jabatan->nama ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @foreach ($u->getRoleNames() as $r)
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-200">{{ $r }}</span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button wire:click="edit({{ $u->id }})" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-blue-900 hover:text-blue-900">Edit</button>
                                    <button wire:click="delete({{ $u->id }})" wire:confirm="Yakin hapus user ini?" class="inline-flex items-center rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-14 text-center text-slate-500">Belum ada data user.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $users->links() }}</div>
        @endif
    </div>

    {{-- Modal --}}
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4"
             wire:click.self="closeModal" role="dialog" aria-modal="true">
            <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h3 class="text-base font-bold text-slate-900">{{ $form->userId ? 'Edit User' : 'Tambah User' }}</h3>
                    <button type="button" wire:click="closeModal" class="rounded-md p-1 text-slate-400 hover:text-slate-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {{-- Username --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-1">Username <span class="text-red-500">*</span></label>
                            <input wire:model="form.username" type="text"
                                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm @error('form.username') border-red-400 @enderror">
                            @error('form.username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nama --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-1">Nama Lengkap</label>
                            <input wire:model="form.name" type="text"
                                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm @error('form.name') border-red-400 @enderror">
                            @error('form.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- NIP --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-1">NIP</label>
                            <input wire:model="form.nip" type="text"
                                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm @error('form.nip') border-red-400 @enderror">
                            @error('form.nip') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-1">Email</label>
                            <input wire:model="form.email" type="email"
                                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm @error('form.email') border-red-400 @enderror">
                            @error('form.email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Divisi --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-1">Divisi</label>
                            <select wire:model="form.divisi_id" class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm">
                                <option value="">-- Pilih Divisi --</option>
                                @foreach ($divisis as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama }}</option>
                                @endforeach
                            </select>
                            @error('form.divisi_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Jabatan --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-1">Jabatan</label>
                            <select wire:model="form.jabatan_id" class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach ($jabatans as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                @endforeach
                            </select>
                            @error('form.jabatan_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Golongan --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-1">Golongan</label>
                            <select wire:model="form.golongan_id" class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm">
                                <option value="">-- Pilih Golongan --</option>
                                @foreach ($golongans as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                @endforeach
                            </select>
                            @error('form.golongan_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Pangkat --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-1">Pangkat</label>
                            <select wire:model="form.pangkat_id" class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm">
                                <option value="">-- Pilih Pangkat --</option>
                                @foreach ($pangkats as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                            @error('form.pangkat_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Role --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-800 mb-1">Role</label>
                            <select wire:model="form.role" class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->name }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                            @error('form.role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-1">
                                Password {{ $form->userId ? '(kosongkan jika tidak diubah)' : '' }}
                                @if (!$form->userId) <span class="text-red-500">*</span> @endif
                            </label>
                            <input wire:model="form.password" type="password"
                                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm @error('form.password') border-red-400 @enderror">
                            @error('form.password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-1">Konfirmasi Password</label>
                            <input wire:model="form.password_confirmation" type="password"
                                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm">
                        </div>
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