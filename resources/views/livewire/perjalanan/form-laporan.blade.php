<div class="py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- HEADER (Konsisten dengan halaman lain) -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Laporan Hasil Perjalanan Dinas</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Laporkan hasil kegiatan dan biaya riil perjalanan dinas Anda.</p>
                </div>
            </div>
        </div>

        <!-- BREADCRUMB (Sejajar dengan Logo) -->
        <nav class="mb-6 flex" aria-label="Breadcrumb">
            <ol role="list" class="flex items-center space-x-2">
                @if(request()->query('from') === 'laporan')
                    <li>
                        <div>
                            <a href="{{ route('laporan.riwayat') }}" class="text-sm font-medium text-slate-500 hover:text-blue-900 transition-colors">
                                Laporan Saya
                            </a>
                        </div>
                    </li>
                @else
                    <li>
                        <div>
                            <a href="{{ route('perjalanan.riwayat') }}" class="text-sm font-medium text-slate-500 hover:text-blue-900 transition-colors">
                                Riwayat UMK
                            </a>
                        </div>
                    </li>
                @endif
                <li>
                    <div class="flex items-center">
                        <svg class="h-4 w-4 flex-shrink-0 text-slate-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-2 text-sm font-semibold text-slate-900" aria-current="page">Laporan Hasil</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Card Form -->
        <div class="mx-auto max-w-xl">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <!-- Header Card -->
                <div class="border-b border-slate-200 bg-slate-50 p-4 sm:p-6">
                    <p class="text-xs font-semibold text-slate-500">UMK</p>
                    <p class="mt-0.5 text-lg font-bold text-slate-900">{{ $perjalanan->nomor_pengajuan }}</p>
                    <p class="mt-0.5 text-sm text-slate-500">Tujuan: {{ $perjalanan->kotaTujuan->nama }}</p>
                </div>

                <form wire:submit="simpan">
                    <div class="p-4 sm:p-6 space-y-5">
                        
                        <!-- Ringkasan Kegiatan -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Ringkasan Kegiatan</label>
                            <textarea wire:model="ringkasan_kegiatan" rows="3" placeholder="Ceritakan secara singkat kegiatan yang dilakukan..." class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('ringkasan_kegiatan') ? 'border-red-400' : 'border-slate-300' }}"></textarea>
                            <x-input-error :messages="$errors->get('ringkasan_kegiatan')" class="mt-2" />
                        </div>

                        <!-- Hasil Perjalanan -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Hasil Perjalanan</label>
                            <textarea wire:model="hasil_perjalanan" rows="3" placeholder="Apa hasil atau kesimpulan dari perjalanan dinas ini?" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('hasil_perjalanan') ? 'border-red-400' : 'border-slate-300' }}"></textarea>
                            <x-input-error :messages="$errors->get('hasil_perjalanan')" class="mt-2" />
                        </div>

                        <!-- Kendala -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Kendala (Opsional)</label>
                            <textarea wire:model="kendala" rows="2" placeholder="Kendala yang dihadapi selama perjalanan (jika ada)..." class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20"></textarea>
                        </div>

                        <!-- Total Biaya Riil -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Total Biaya Riil (Rp)</label>
                            <input type="number" wire:model="total_biaya_riil" class="mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('total_biaya_riil') ? 'border-red-400' : 'border-slate-300' }}">
                            <p class="mt-1 text-xs text-slate-500">Estimasi awal: Rp {{ number_format($perjalanan->total_estimasi, 0, ',', '.') }}. Silakan ubah sesuai biaya riil.</p>
                            <x-input-error :messages="$errors->get('total_biaya_riil')" class="mt-2" />
                        </div>

                        <!-- Lampiran -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Lampiran (Opsional)</label>
                            <input type="file" wire:model="file_lampiran" accept=".pdf,.jpg,.jpeg,.png" class="mt-1.5 block w-full text-sm text-slate-600 file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-blue-900 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-slate-500">Bukti dokumentasi atau nota (PDF/Gambar, maks 10MB).</p>
                            <div wire:loading wire:target="file_lampiran" class="text-xs text-blue-600 mt-1">Mengunggah...</div>
                            <x-input-error :messages="$errors->get('file_lampiran')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end border-t border-slate-200 bg-slate-50 p-4 sm:p-6">
                        <button type="submit" wire:loading.attr="disabled" wire:target="simpan" class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-7 py-3 text-sm font-semibold text-white transition-colors hover:bg-blue-800 disabled:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                            Kirim Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>