<div class="py-8 sm:py-10">
    @php
        $inp = fn ($field) => 'block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 '
            . ($errors->has($field) ? 'border-red-400' : 'border-slate-300');
    @endphp

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <!-- Header -->
            <div class="flex items-center gap-4 border-b border-slate-200 bg-slate-50 p-4 sm:p-6">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Formulir Pengajuan Perjalanan Dinas</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Isi tujuan, jadwal, dan estimasi biaya perjalanan.</p>
                </div>
            </div>

            <form wire:submit="simpan">
                <!-- Informasi perjalanan -->
                <div class="p-4 sm:p-6">
                    <h3 class="flex items-center gap-2.5 text-sm font-bold text-slate-900"><span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-900 text-xs font-bold text-white">1</span>Informasi Perjalanan</h3>

                    <div class="mt-4 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <!-- Kota Tujuan -->
                        <div>
                            <label for="kota_tujuan_id" class="block text-sm font-semibold text-slate-800">Kota Tujuan</label>
                            <select wire:model="kota_tujuan_id" id="kota_tujuan_id" class="mt-1.5 {{ $inp('kota_tujuan_id') }}">
                                <option value="">-- Pilih Kota --</option>
                                @foreach($kotas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kota_tujuan_id')" class="mt-2" />
                        </div>

                        <!-- Maksud Perjalanan -->
                        <div>
                            <label for="maksud_perjalanan" class="block text-sm font-semibold text-slate-800">Maksud / Tujuan Perjalanan</label>
                            <input wire:model="maksud_perjalanan" id="maksud_perjalanan" type="text" placeholder="Contoh: Rapat Koordinasi" class="mt-1.5 {{ $inp('maksud_perjalanan') }}">
                            <x-input-error :messages="$errors->get('maksud_perjalanan')" class="mt-2" />
                        </div>

                        <!-- Tanggal Berangkat -->
                        <div>
                            <label for="tanggal_berangkat" class="block text-sm font-semibold text-slate-800">Tanggal Berangkat</label>
                            <input wire:model.live="tanggal_berangkat" id="tanggal_berangkat" type="date" class="mt-1.5 {{ $inp('tanggal_berangkat') }}">
                            <x-input-error :messages="$errors->get('tanggal_berangkat')" class="mt-2" />
                        </div>

                        <!-- Tanggal Pulang -->
                        <div>
                            <label for="tanggal_pulang" class="block text-sm font-semibold text-slate-800">Tanggal Pulang</label>
                            <input wire:model.live="tanggal_pulang" id="tanggal_pulang" type="date" class="mt-1.5 {{ $inp('tanggal_pulang') }}">
                            <x-input-error :messages="$errors->get('tanggal_pulang')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        <span class="text-slate-600">Total hari</span>
                        <strong class="text-slate-900">{{ $jumlah_hari }} Hari</strong>
                    </div>
                </div>

                <!-- Rincian biaya -->
                <div class="border-t border-slate-200 p-4 sm:p-6">
                    <h3 class="flex items-center gap-2.5 text-sm font-bold text-slate-900"><span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-900 text-xs font-bold text-white">2</span>Rincian Estimasi Biaya</h3>

                    <div class="mt-4 space-y-4 md:space-y-3">
                        @foreach($rincian as $index => $item)
                            <div wire:key="rincian-{{ $index }}" class="grid grid-cols-1 gap-4 rounded-xl border border-slate-200 p-4 md:grid-cols-12 md:items-end md:rounded-none md:border-0 md:p-0">
                                <div class="md:col-span-4">
                                    <label for="rincian-coa-{{ $index }}" class="block text-sm font-semibold text-slate-800 {{ $index > 0 ? 'md:sr-only' : '' }}">Kategori Biaya</label>
                                    <select wire:model="rincian.{{ $index }}.coa_id" id="rincian-coa-{{ $index }}" class="mt-1.5 {{ $index > 0 ? 'md:mt-0' : '' }} {{ $inp('rincian.'.$index.'.coa_id') }}">
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach($coas as $c)
                                            <option value="{{ $c->id }}">{{ $c->kode }} - {{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('rincian.'.$index.'.coa_id')" class="mt-1" />
                                </div>

                                <div class="md:col-span-4">
                                    <label for="rincian-ket-{{ $index }}" class="block text-sm font-semibold text-slate-800 {{ $index > 0 ? 'md:sr-only' : '' }}">Keterangan</label>
                                    <input wire:model="rincian.{{ $index }}.keterangan" id="rincian-ket-{{ $index }}" type="text" placeholder="Misal: Tiket Pesawat" class="mt-1.5 {{ $index > 0 ? 'md:mt-0' : '' }} {{ $inp('rincian.'.$index.'.keterangan') }}">
                                </div>

                                <div class="md:col-span-3">
                                    <label for="rincian-nom-{{ $index }}" class="block text-sm font-semibold text-slate-800 {{ $index > 0 ? 'md:sr-only' : '' }}">Nominal (Rp)</label>
                                    <input wire:model.live="rincian.{{ $index }}.nominal" id="rincian-nom-{{ $index }}" type="number" min="0" placeholder="0" class="mt-1.5 {{ $index > 0 ? 'md:mt-0' : '' }} {{ $inp('rincian.'.$index.'.nominal') }}">
                                    <x-input-error :messages="$errors->get('rincian.'.$index.'.nominal')" class="mt-1" />
                                </div>

                                <div class="md:col-span-1 md:flex md:justify-end">
                                    <button type="button" wire:click="hapusBaris({{ $index }})" title="Hapus baris"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition-colors hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2 max-md:w-full md:border-0 md:px-2.5">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span class="md:sr-only">Hapus baris</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" wire:click="tambahBaris" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 max-sm:w-full max-sm:justify-center">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
                        Tambah Baris Biaya
                    </button>
                </div>

                <!-- Total & kirim -->
                <div class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div>
                        <div class="text-xs font-semibold text-slate-500">Total Estimasi</div>
                        <div class="mt-0.5 text-2xl font-extrabold tracking-tight text-slate-900">Rp {{ number_format($total_estimasi, 0, ',', '.') }}</div>
                    </div>
                    <button type="submit" wire:loading.attr="disabled" wire:target="simpan"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-7 py-3 text-sm font-semibold text-white transition-colors hover:bg-blue-800 disabled:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>