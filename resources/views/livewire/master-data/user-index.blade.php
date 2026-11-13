<div class="py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Manajemen User</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Kelola akun pengguna aplikasi.</p>
                </div>
            </div>
            <button type="button" wire:click="openModal" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>
                Tambah User
            </button>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <div class="relative w-full sm:max-w-xs">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari nama/nip/email..." class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20">
                </div>
                <p class="text-sm text-slate-500">Menampilkan <span class="font-semibold text-slate-700">{{ $users->firstItem() ?? 0 }}&ndash;{{ $users->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700">{{ $users->total() }}</span> data</p>
            </div>

            <!-- Tabel Desktop -->
            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th class="w-16 px-6 py-3 font-semibold">No</th>
                            <th class="px-6 py-3 font-semibold">Nama</th>
                            <th class="px-6 py-3 font-semibold">NIP/Username</th>
                            <th class="px-6 py-3 font-semibold">Role</th>
                            <th class="w-40 px-6 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $index => $u)
                            <tr wire:key="row-{{ $u->id }}" class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 text-slate-400">{{ $users->firstItem() + $index }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-800 font-bold">{{ strtoupper(substr($u->name ?? $u->username, 0, 1)) }}</div>
                                        <div>
                                            <div class="font-semibold text-slate-900">{{ $u->name ?? $u->username }}</div>
                                            <div class="text-xs text-slate-400">{{ $u->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <div>{{ $u->nip ?? '-' }}</div>
                                    <div class="text-xs text-slate-400">{{ $u->username }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold capitalize text-slate-700 ring-1 ring-inset ring-slate-200">{{ $u->getRoleNames()->first() ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button type="button" wire:click="edit({{ $u->id }})" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-blue-900 hover:text-blue-900">Edit</button>
                                        <button type="button" wire:click="delete({{ $u->id }})" wire:confirm="Yakin hapus user ini?" class="rounded-md border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada data user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile -->
            <div class="divide-y divide-slate-100 sm:hidden">
                @forelse ($users as $u)
                    <div wire:key="card-{{ $u->id }}" class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-800 font-bold">{{ strtoupper(substr($u->name ?? $u->username, 0, 1)) }}</div>
                            <div>
                                <div class="font-semibold text-slate-900">{{ $u->name ?? $u->username }}</div>
                                <div class="text-xs text-slate-400">{{ $u->nip ?? '-' }} / {{ $u->username }}</div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold capitalize text-slate-700">{{ $u->getRoleNames()->first() ?? '-' }}</span>
                            <div class="flex gap-2">
                                <button type="button" wire:click="edit({{ $u->id }})" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700">Edit</button>
                                <button type="button" wire:click="delete({{ $u->id }})" wire:confirm="Yakin hapus?" class="rounded-md border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700">Hapus</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data.</div>
                @endforelse
            </div>

            @if ($users->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 sm:px-6">{{ $users->links() }}</div>
            @endif
        </div>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 backdrop-blur-sm sm:items-center sm:p-4" wire:keydown.escape.window="closeModal" role="dialog" aria-modal="true">
        <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-t-2xl bg-white shadow-xl sm:rounded-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="text-base font-bold text-slate-900">{{ $userId ? 'Edit User' : 'Tambah User' }}</h3>
                <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-700 text-2xl">&times;</button>
            </div>
            <form wire:submit="save">
                <div class="p-5 sm:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {{-- Username (WAJIB) --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-800">Username <span class="text-red-500">*</span></label>
                            <input wire:model="username" type="text" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm {{ $errors->has('username') ? 'border-red-400' : 'border-slate-300' }}" placeholder="Wajib diisi">
                            @error('username') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        {{-- Password (WAJIB untuk create) --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Password {!! $userId ? '<span class="text-slate-400 text-xs">(Kosongkan jika tidak diubah)</span>' : '<span class="text-red-500">*</span>' !!}</label>
                            <input wire:model="password" type="password" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm {{ $errors->has('password') ? 'border-red-400' : 'border-slate-300' }}" placeholder="{{ $userId ? 'Kosongkan jika tidak diubah' : 'Wajib diisi' }}">
                            @error('password') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Konfirmasi Password</label>
                            <input wire:model="password_confirmation" type="password" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm border-slate-300">
                        </div>

                        {{-- Field Opsional --}}
                        <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Data Tambahan (Opsional)</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-800">Nama Lengkap</label>
                            <input wire:model="name" type="text" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm border-slate-300" placeholder="Kosongkan untuk memakai username">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">NIP</label>
                            <input wire:model="nip" type="text" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm border-slate-300" placeholder="Opsional">
                            @error('nip') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Email</label>
                            <input wire:model="email" type="email" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm border-slate-300" placeholder="Opsional">
                            @error('email') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Role</label>
                            <select wire:model="role" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm border-slate-300">
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Divisi</label>
                            <select wire:model="divisi_id" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm border-slate-300">
                                <option value="">-- Pilih Divisi --</option>
                                @foreach($divisis as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Jabatan</label>
                            <select wire:model="jabatan_id" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm border-slate-300">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($jabatans as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Golongan</label>
                            <select wire:model="golongan_id" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm border-slate-300">
                                <option value="">-- Pilih Golongan --</option>
                                @foreach($golongans as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Pangkat</label>
                            <select wire:model="pangkat_id" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm border-slate-300">
                                <option value="">-- Pilih Pangkat --</option>
                                @foreach($pangkats as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                    <button type="button" wire:click="closeModal" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700">Batal</button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-5 py-2.5 text-sm font-semibold text-white">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>