<div class="py-8 sm:py-10">
    @php
        $inp = fn ($field) => 'block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 '
            . ($errors->has($field) ? 'border-red-400' : 'border-slate-300');
    @endphp

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <!-- Header -->
            <div class="flex items-center gap-4 border-b border-slate-200 bg-slate-50 p-4 sm:p-6">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg></div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Laporan Reimbursement</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Perjalanan: {{ $perjalanan->nomor_pengajuan }} ke {{ $perjalanan->kotaTujuan->nama }}</p>
                </div>
            </div>

            <form wire:submit="simpan">
                <div class="p-4 sm:p-6">
                    <h3 class="flex items-center gap-2.5 text-sm font-bold text-slate-900"><span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-900 text-xs font-bold text-white">1</span>Rincian Realisasi Biaya & Struk</h3>

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
                                    <input type="file" wire:model="rincian.{{ $index }}.file_struk" id="rincian-struk-{{ $index }}" accept="image/*" class="mt-1.5 {{ $index > 0 ? 'md:mt-0' : '' }} block w-full cursor-pointer rounded-lg border border-slate-300 bg-white text-sm text-slate-600 file:mr-3 file:cursor-pointer file:rounded-l-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2.5 file:text-xs file:font-semibold file:text-blue-900 hover:file:bg-blue-100">
                                    <div wire:loading wire:target="rincian.{{ $index }}.file_struk" class="mt-1 text-xs font-medium text-blue-700">Mengunggah...</div>
                                    @if(isset($item['file_struk']) && is_object($item['file_struk']))
                                        <p wire:loading.remove wire:target="rincian.{{ $index }}.file_struk" class="mt-1 truncate text-xs font-medium text-emerald-700">&#10003; {{ $item['file_struk']->getClientOriginalName() }}</p>
                                    @endif
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