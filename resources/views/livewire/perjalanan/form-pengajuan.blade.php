<div class="py-8 sm:py-10">
    @php
        $inp = fn ($field) => 'block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 '
            . ($errors->has($field) ? 'border-red-400' : 'border-slate-300');
        $user = auth()->user();
    @endphp

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- HEADER -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Form UMK Baru</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Isi data perjalanan dinas Anda untuk pengajuan Uang Muka Kerja.</p>
                </div>
            </div>
        </div>

        <!-- CARD FORM -->
        <div class="mx-auto max-w-4xl">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <!-- DATA DIRI -->
                <div class="border-b border-slate-100 bg-blue-50/50 p-4 sm:p-6">
                    <h3 class="flex items-center gap-2.5 text-sm font-bold text-blue-900 mb-4">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Data Pemohon (Otomatis)
                    </h3>
                    <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                        <div><span class="text-slate-500">Nama Lengkap</span><p class="font-semibold text-slate-900">{{ $user->name }}</p></div>
                        <div><span class="text-slate-500">NIP</span><p class="font-semibold text-slate-900">{{ $user->nip ?? '-' }}</p></div>
                        <div><span class="text-slate-500">Jabatan</span><p class="font-semibold text-slate-900">{{ $user->jabatan->nama ?? '-' }}</p></div>
                        <div><span class="text-slate-500">Divisi</span><p class="font-semibold text-slate-900">{{ $user->divisi->nama ?? '-' }}</p></div>
                        <div><span class="text-slate-500">Golongan</span><p class="font-semibold text-slate-900">{{ $user->golongan->nama ?? '-' }}</p></div>
                        <div><span class="text-slate-500">Pangkat</span><p class="font-semibold text-slate-900">{{ $user->pangkat->nama ?? '-' }} {{ $user->pangkat ? '(' . $user->pangkat->golongan_ruang . ')' : '' }}</p></div>
                    </div>
                </div>

                <form wire:submit="simpan">
                    <fieldset @disabled(!$profilLengkap) class="m-0 min-w-0 border-0 p-0 {{ !$profilLengkap ? 'opacity-60' : '' }}">

                        <!-- INFORMASI PERJALANAN -->
                        <div class="p-4 sm:p-6">
                            <h3 class="flex items-center gap-2.5 text-sm font-bold text-slate-900 mb-4">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Informasi Perjalanan
                            </h3>

                            <!-- Baris 1 -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800">No. Pembukuan</label>
                                    <input wire:model="no_pembukuan" type="text" readonly class="mt-1.5 block w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-sm font-medium text-slate-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800">Akun</label>
                                    <input wire:model="akun" type="text" readonly class="mt-1.5 block w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-sm font-medium text-slate-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800">Kode SPD</label>
                                    <input wire:model="no_spd" type="text" readonly class="mt-1.5 block w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-sm font-medium text-slate-500 focus:outline-none">
                                </div>
                            </div>

                            <!-- Baris 2 -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-4 mb-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800">Tempat Berangkat</label>
                                    <input wire:model="tempat_berangkat" type="text" class="{{ $inp('tempat_berangkat') }} mt-1.5">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800">Provinsi Tujuan</label>
                                    <select wire:model.live="provinsi" class="{{ $inp('provinsi') }} mt-1.5">
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach($provinsis as $prov)
                                            <option value="{{ $prov }}">{{ $prov }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800">Kota / Kabupaten</label>
                                    <select wire:model="kota_tujuan_id" class="{{ $inp('kota_tujuan_id') }} mt-1.5" @if(!$provinsi) disabled @endif>
                                        <option value="">-- Pilih Kota --</option>
                                        @foreach($kotas as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800">Maksud Perjalanan</label>
                                    <input wire:model="maksud_perjalanan" type="text" placeholder="Contoh: Rapat" class="{{ $inp('maksud_perjalanan') }} mt-1.5">
                                </div>
                            </div>

                            <!-- Baris 3 -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800">Alat Angkutan</label>
                                    <select wire:model="alat_angkutan" class="{{ $inp('alat_angkutan') }} mt-1.5">
                                        <option value="Pesawat">Pesawat</option>
                                        <option value="Kereta">Kereta</option>
                                        <option value="Bus">Bus</option>
                                        <option value="Mobil">Mobil</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800">Tingkat Biaya</label>
                                    <select wire:model="tingkat_biaya" class="{{ $inp('tingkat_biaya') }} mt-1.5">
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800">Lama Perjalanan</label>
                                    <div class="mt-1.5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm">
                                        <strong class="text-blue-900">{{ $jumlah_hari }} Hari</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Baris 4 -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800">Tanggal Berangkat</label>
                                    <input wire:model.live="tanggal_berangkat" type="date" class="{{ $inp('tanggal_berangkat') }} mt-1.5">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800">Tanggal Pulang</label>
                                    <input wire:model.live="tanggal_pulang" type="date" class="{{ $inp('tanggal_pulang') }} mt-1.5">
                                </div>
                            </div>
                        </div>

                        <!-- RINCIAN BIAYA -->
                        <div class="border-t border-slate-200 p-4 sm:p-6">
                            <h3 class="flex items-center gap-2.5 text-sm font-bold text-slate-900 mb-4">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Rincian Estimasi Biaya
                            </h3>

                            <div class="space-y-4">
                                @foreach($rincian as $index => $item)
                                    @php
                                        $selectedCoa = collect($coas)->firstWhere('id', $item['coa_id']);
                                        $namaCoa = $selectedCoa ? strtolower($selectedCoa->nama) : '';
                                        $teksKet = strtolower($item['keterangan'] ?? '');
                                        
                                        $isPenginapan = $selectedCoa && (str_contains($namaCoa, 'penginapan') || str_contains($namaCoa, 'hotel') || str_contains($namaCoa, 'akomodasi'));
                                        $isUangHarian = $selectedCoa && (str_contains($namaCoa, 'uang harian') || str_contains($namaCoa, 'uang saku') || str_contains($namaCoa, 'harian'));
                                        $isTaksi = str_contains($namaCoa, 'taksi') || str_contains($namaCoa, 'taxi') || str_contains($namaCoa, 'transport') || str_contains($teksKet, 'taksi') || str_contains($teksKet, 'ojek');

                                        $borderColor = 'border-slate-200 bg-slate-50/50';
                                        if ($isPenginapan) $borderColor = 'border-blue-300 bg-blue-50/40';
                                        if ($isUangHarian) $borderColor = 'border-amber-300 bg-amber-50/40';
                                        if ($isTaksi) $borderColor = 'border-emerald-300 bg-emerald-50/40';

                                        // LOGIKA PLACEHOLDER DINAMIS
                                        $placeholder = 'Biaya Lainnya';
                                        if ($index === 0) $placeholder = 'Tiket Pesawat';
                                        if ($index === 1) $placeholder = 'Transport Lokal / Taksi';
                                    @endphp
                                    
                                    <div wire:key="rincian-{{ $index }}" class="relative w-full rounded-xl border {{ $borderColor }} p-4 pr-12 mt-2">
                                        
                                        <button type="button" wire:click="hapusBaris({{ $index }})" 
                                                style="position: absolute; top: 12px; right: 12px; z-index: 50;"
                                                class="flex h-7 w-7 items-center justify-center rounded-full bg-white/80 text-slate-400 shadow-sm transition-colors hover:bg-red-100 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500/50"
                                                title="Hapus baris ini">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>

                                        @if($isPenginapan)
                                            <div class="mb-2 flex items-center gap-1.5 text-xs font-semibold text-blue-800">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                Baris penginapan - Qty otomatis
                                            </div>
                                        @elseif($isUangHarian)
                                            <div class="mb-2 flex items-center gap-1.5 text-xs font-semibold text-amber-800">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Uang harian - Qty otomatis
                                            </div>
                                        @endif

                                        <div class="grid grid-cols-12 gap-3 items-start">
                                            <div class="col-span-12 md:col-span-3 mt-8">
                                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori</label>
                                                <select wire:model.live="rincian.{{ $index }}.coa_id" class="block w-full rounded-lg border bg-white px-2.5 py-2 text-sm border-slate-300">
                                                    <option value="">-- Pilih --</option>
                                                    @foreach($coas as $c)
                                                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-12 md:col-span-3 mt-8">
                                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Keterangan</label>
                                                <input wire:model="rincian.{{ $index }}.keterangan" type="text" class="block w-full rounded-lg border bg-white px-2.5 py-2 text-sm border-slate-300" placeholder="{{ $placeholder }}">
                                            </div>
                                            <div class="col-span-3 md:col-span-1 mt-8">
                                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Qty</label>
                                                <input wire:model.live="rincian.{{ $index }}.qty" type="number" min="1" class="block w-full rounded-lg border bg-white px-2.5 py-2 text-sm border-slate-300 {{ $isPenginapan ? 'bg-blue-50 font-semibold' : ($isUangHarian ? 'bg-amber-50 font-semibold' : ($isTaksi ? 'bg-emerald-50 font-semibold' : '')) }}">
                                            </div>
                                            <div class="col-span-3 md:col-span-1 mt-8">
                                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Satuan</label>
                                                <input wire:model="rincian.{{ $index }}.satuan" type="text" class="block w-full rounded-lg border bg-white px-2.5 py-2 text-sm border-slate-300 {{ $isPenginapan ? 'bg-blue-50' : ($isUangHarian ? 'bg-amber-50' : ($isTaksi ? 'bg-emerald-50' : '')) }}">
                                            </div>
                                            <div class="col-span-6 md:col-span-2 mt-8">
                                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Harga Satuan</label>
                                                <input wire:model.live="rincian.{{ $index }}.harga_satuan" type="number" min="0" class="block w-full rounded-lg border bg-white px-2.5 py-2 text-sm border-slate-300">
                                            </div>
                                            <div class="col-span-6 md:col-span-2 mt-8">
                                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jumlah</label>
                                                <div class="rounded-lg border border-slate-200 bg-white px-2 py-2 text-sm font-semibold text-slate-900">
                                                    {{ number_format($item['nominal'] ?? 0, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" wire:click="tambahBaris" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-blue-900 hover:text-blue-900">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
                                Tambah Baris Biaya
                            </button>
                        </div>
                    </fieldset>

                    <!-- TOTAL & KIRIM -->
                    <div class="flex flex-col gap-4 border-t border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                        <div>
                            <div class="text-xs font-semibold text-slate-500">Total Estimasi</div>
                            <div class="mt-0.5 text-2xl font-extrabold tracking-tight text-slate-900">Rp {{ number_format($total_estimasi, 0, ',', '.') }}</div>
                        </div>
                        <div class="flex flex-col gap-3 sm:items-end">
                            @unless($profilLengkap)
                                <div role="alert" class="flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800 sm:max-w-xs">
                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                    <span><strong class="font-semibold">Data belum lengkap.</strong> Lengkapi profil Anda.</span>
                                </div>
                            @endunless

                            <button type="submit" wire:loading.attr="disabled" wire:target="simpan" @disabled(!$profilLengkap) class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-7 py-3 text-sm font-semibold text-white transition-colors hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60">
                                Simpan &amp; Lanjut Cetak UMK
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- SCRIPT SWEETALERT PROFIL LENGKAP -->
    @if(!$profilLengkap)
    <div style="display: none;">
        <script>
            (function () {
                const missingFields = @json($missing);
                const profileUrl = @json(route('profile.edit'));

                function showProfileAlert() {
                    if (window.__profileAlertShown) return;
                    window.__profileAlertShown = true;

                    const items = missingFields.map(f => `<li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>${f}</li>`).join('');

                    Swal.fire({
                        icon: 'warning',
                        title: 'Profil Belum Lengkap',
                        html: `<div class="text-left"><p class="text-sm text-slate-600 mb-3">Lengkapi data berikut:</p><ul class="text-sm text-slate-800 space-y-1.5 pl-1">${items}</ul></div>`,
                        showCancelButton: true,
                        confirmButtonText: 'Lengkapi Profil',
                        cancelButtonText: 'Tutup',
                        confirmButtonColor: '#1e3a8a',
                        cancelButtonColor: '#64748b',
                        allowOutsideClick: false
                    }).then((result) => {
                        window.__profileAlertShown = false;
                        if (result.isConfirmed) window.location.href = profileUrl;
                    });
                }

                if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', showProfileAlert, { once: true });
                else showProfileAlert();
            })();
        </script>
    </div>
    @endif

</div>