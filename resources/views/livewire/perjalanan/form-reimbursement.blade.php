<div class="py-8 sm:py-10">
    @php
        $inp = fn ($field) => 'block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 '
            . ($errors->has($field) ? 'border-red-400' : 'border-slate-300');
    @endphp

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

            <!-- Header -->
            <div class="border-b border-slate-200 p-4 sm:p-6">
                <h2 class="text-lg font-bold tracking-tight text-slate-900">Laporan Reimbursement</h2>
                <p class="mt-1 text-sm text-slate-500">Perjalanan: {{ $perjalanan->nomor_pengajuan }} ke {{ $perjalanan->kotaTujuan->nama }}</p>
            </div>

            <form wire:submit="simpan">
                <div class="p-4 sm:p-6">
                    <h3 class="text-sm font-bold text-slate-900">Rincian Realisasi Biaya & Struk</h3>

                    <div class="mt-4 space-y-4 md:space-y-3">
                        @foreach($rincian as $index => $item)
                            <div wire:key="rincian-{{ $index }}" class="grid grid-cols-1 gap-4 rounded-xl border border-slate-200 p-4 md:grid-cols-12 md:items-start md:rounded-none md:border-0 md:p-0">
                                
                                <div class="md:col-span-3">
                                    <label for="rincian-coa-{{ $index }}" class="block text-sm font-semibold text-slate-800 {{ $index > 0 ? 'md:sr-only' : '' }}">Kategori</label>
                                    <select wire:model="rincian.{{ $index }}.coa_id" id="rincian-coa-{{ $index }}" class="mt-1.5 {{ $index > 0 ? 'md:mt-0' : '' }} {{ $inp('rincian.'.$index.'.coa_id') }}">
                                        <option value="">-- Pilih --</option>
                                        @foreach($coas as $c)
                                            <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('rincian.'.$index.'.coa_id')" class="mt-1" />
                                </div>

                                <div class="md:col-span-3">
                                    <label for="rincian-ket-{{ $index }}" class="block text-sm font-semibold text-slate-800 {{ $index > 0 ? 'md:sr-only' : '' }}">Keterangan</label>
                                    <input wire:model="rincian.{{ $index }}.keterangan" id="rincian-ket-{{ $index }}" type="text" class="mt-1.5 {{ $index > 0 ? 'md:mt-0' : '' }} {{ $inp('rincian.'.$index.'.keterangan') }}">
                                </div>

                                <div class="md:col-span-3">
                                    <label for="rincian-nom-{{ $index }}" class="block text-sm font-semibold text-slate-800 {{ $index > 0 ? 'md:sr-only' : '' }}">Nominal (Rp)</label>
                                    <input wire:model.live="rincian.{{ $index }}.nominal" id="rincian-nom-{{ $index }}" type="number" min="0" class="mt-1.5 {{ $index > 0 ? 'md:mt-0' : '' }} {{ $inp('rincian.'.$index.'.nominal') }}">
                                    <x-input-error :messages="$errors->get('rincian.'.$index.'.nominal')" class="mt-1" />
                                </div>

                                <div class="md:col-span-3">
                                    <label for="rincian-struk-{{ $index }}" class="block text-sm font-semibold text-slate-800 {{ $index > 0 ? 'md:sr-only' : '' }}">Upload Struk</label>
                                    <input type="file" wire:model="rincian.{{ $index }}.file_struk" id="rincian-struk-{{ $index }}" accept="image/*" class="mt-1.5 {{ $index > 0 ? 'md:mt-0' : '' }} block w-full text-sm file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
                                    <div wire:loading wire:target="rincian.{{ $index }}.file_struk" class="text-xs text-blue-600 mt-1">Mengunggah...</div>
                                    <x-input-error :messages="$errors->get('rincian.'.$index.'.file_struk')" class="mt-1" />
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" wire:click="tambahBaris" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 max-sm:w-full max-sm:justify-center">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
                        Tambah Baris Biaya
                    </button>
                </div>

                <!-- Total & Kirim -->
                <div class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div>
                        <div class="text-xs font-semibold text-slate-500">Total Realisasi</div>
                        <div class="mt-0.5 text-2xl font-extrabold tracking-tight text-slate-900">Rp {{ number_format($total_realisasi, 0, ',', '.') }}</div>
                    </div>
                    <button type="submit" wire:loading.attr="disabled" wire:target="simpan"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-7 py-3 text-sm font-semibold text-white transition-colors hover:bg-blue-800 disabled:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                        Kirim Laporan Reimbursement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>