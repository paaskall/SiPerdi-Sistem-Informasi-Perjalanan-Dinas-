<div class="py-8 sm:py-10">
    @php
        $dokumen = [
            ['UMK', 'Uang Muka Kerja (UMK)', 'umk.cetak', 'umk.excel'],
            ['RIN', 'Rincian Biaya', 'umk.rincian-pdf', 'umk.rincian-excel'],
            ['KWT', 'Kwitansi', 'umk.kwitansi-pdf', 'umk.kwitansi-excel'],
            ['SPD', 'Surat Perjalanan Dinas (SPD)', 'umk.spd-pdf', 'umk.spd-excel'],
            ['DPR', 'Daftar Pengeluaran Riil (DPR)', 'umk.dpr-pdf', 'umk.dpr-excel'],
            ['AMP', 'Amplop UMK', 'umk.amplop-pdf', 'umk.amplop-excel'],
        ];
    @endphp
    
    <!-- Wrapper Modal Dokumen -->
    <div x-data="{ openModal: false, activeId: null, activeNomor: '' }" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white shadow-lg shadow-blue-900/20">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Riwayat UMK Saya</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Pantau status form Uang Muka Kerja Anda.</p>
                </div>
            </div>
            <a href="{{ route('perjalanan.buat') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14M5 12h14"/></svg>
                Buat UMK Baru
            </a>
        </div>

        <!-- Statistik -->
        <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-medium text-slate-500">Total UMK</p>
                <p class="mt-1 text-2xl font-extrabold text-blue-900 sm:text-3xl">{{ $riwayats->total() }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-medium text-slate-500">Perlu Dilaporkan</p>
                <p class="mt-1 text-2xl font-extrabold text-amber-600 sm:text-3xl">{{ $riwayats->getCollection()->whereIn('status', ['PRINTED', 'RECEIVED_BY_FINANCE', 'PIMPINAN_SIGNED', 'COMPLETED'])->filter(fn($r) => !$r->laporan)->count() }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-medium text-slate-500">Selesai</p>
                <p class="mt-1 text-2xl font-extrabold text-emerald-600 sm:text-3xl">{{ $riwayats->getCollection()->filter(fn($r) => $r->laporan)->count() }}</p>
            </div>
        </div>

        <!-- Tabel Desktop -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500 border-b border-slate-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">No. UMK</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Tujuan & Tanggal</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($riwayats as $r)
                            @php
                                [$label, $badge] = match ($r->status) {
                                    'DRAFT'                => ['Draft', 'bg-slate-100 text-slate-700 ring-slate-200'],
                                    'PRINTED'              => ['Sudah Dicetak', 'bg-amber-50 text-amber-700 ring-amber-200'],
                                    'RECEIVED_BY_FINANCE'  => ['Diterima Keuangan', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
                                    'REJECTED'             => ['Ditolak', 'bg-red-50 text-red-700 ring-red-200'],
                                    'PIMPINAN_SIGNED'      => ['Disetujui Pimpinan', 'bg-blue-50 text-blue-700 ring-blue-200'],
                                    'COMPLETED'            => ['Selesai', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
                                    default                => [$r->status, 'bg-slate-100 text-slate-700 ring-slate-200'],
                                };
                            @endphp
                            <tr wire:key="row-{{ $r->id }}" class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $r->nomor_pengajuan }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800">{{ $r->kotaTujuan->nama }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500">{{ $r->tanggal_berangkat->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide ring-1 ring-inset {{ $badge }}">{{ $label }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($r->status === 'DRAFT')
                                            <button type="button" wire:click="openEditModal({{ $r->id }})" class="rounded-lg border border-amber-300 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700 hover:bg-amber-100">Edit</button>
                                        @elseif(in_array($r->status, ['PRINTED', 'RECEIVED_BY_FINANCE', 'PIMPINAN_SIGNED', 'COMPLETED']) && !$r->laporan)
                                            <a href="{{ route('perjalanan.lapor', ['id' => $r->id, 'from' => 'riwayat']) }}" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100">Lapor</a>
                                        @elseif($r->laporan)
                                            <span class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-200">Tuntas</span>
                                        @endif
                                        <button type="button" @click="openModal = true; activeId = {{ $r->id }}; activeNomor = '{{ $r->nomor_pengajuan }}'" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-900 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-800">Dokumen</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-14 text-center font-semibold text-slate-800">Belum ada riwayat UMK</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tabel Mobile -->
            <div class="divide-y divide-slate-100 sm:hidden">
                @forelse ($riwayats as $r)
                    @php
                        [$label, $badge] = match ($r->status) {
                            'DRAFT'                => ['Draft', 'bg-slate-100 text-slate-700 ring-slate-200'],
                            'PRINTED'              => ['Sudah Dicetak', 'bg-amber-50 text-amber-700 ring-amber-200'],
                            'RECEIVED_BY_FINANCE'  => ['Diterima Keuangan', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
                            'REJECTED'             => ['Ditolak', 'bg-red-50 text-red-700 ring-red-200'],
                            'PIMPINAN_SIGNED'      => ['Disetujui Pimpinan', 'bg-blue-50 text-blue-700 ring-blue-200'],
                            'COMPLETED'            => ['Selesai', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
                            default                => [$r->status, 'bg-slate-100 text-slate-700 ring-slate-200'],
                        };
                    @endphp
                    <div wire:key="card-{{ $r->id }}" class="p-4">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <div class="font-bold text-slate-900">{{ $r->nomor_pengajuan }}</div>
                                <div class="text-xs text-slate-500 mt-1">Ke: {{ $r->kotaTujuan->nama }} ({{ $r->tanggal_berangkat->format('d/m/Y') }})</div>
                            </div>
                            <span class="inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 ring-inset {{ $badge }}">{{ $label }}</span>
                        </div>
                        <div class="flex gap-2 mt-4">
                            @if($r->status === 'DRAFT')
                                <button type="button" wire:click="openEditModal({{ $r->id }})" class="flex-1 rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700">Edit</button>
                            @elseif(in_array($r->status, ['PRINTED', 'RECEIVED_BY_FINANCE', 'PIMPINAN_SIGNED', 'COMPLETED']) && !$r->laporan)
                                <a href="{{ route('perjalanan.lapor', ['id' => $r->id, 'from' => 'riwayat']) }}" class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 text-center">Lapor</a>
                            @endif
                            <button type="button" @click="openModal = true; activeId = {{ $r->id }}; activeNomor = '{{ $r->nomor_pengajuan }}'" class="flex-1 rounded-lg bg-blue-900 px-3 py-2 text-xs font-bold text-white">Dokumen</button>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center font-semibold text-slate-800">Belum ada riwayat UMK</div>
                @endforelse
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL POPUP DOKUMEN -->
        <!-- ========================================== -->
        <div x-show="openModal" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto">
            <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="openModal = false"></div>
            <div class="flex min-h-screen items-center justify-center p-4 sm:p-6">
                <div x-show="openModal" x-transition class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl flex flex-col max-h-[90vh]">
                    <div class="bg-white px-5 py-4 border-b border-slate-100 flex justify-between shrink-0">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Dokumen Perjalanan</h3>
                            <p class="mt-1 text-sm text-slate-500">No. UMK: <span class="font-semibold text-slate-800" x-text="activeNomor"></span></p>
                        </div>
                        <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-500 p-1">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="bg-slate-50 px-5 py-6 sm:px-6 overflow-y-auto">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($dokumen as [$singkatan, $labelLengkap, $rutePdf, $ruteXls])
                                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col justify-between">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700 font-bold text-xs">{{ $singkatan }}</div>
                                        <div class="font-semibold text-slate-800 text-sm leading-tight">{{ $labelLengkap }}</div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 mt-auto">
                                        <a :href="'{{ route($rutePdf, 'ID_PLACEHOLDER') }}'.replace('ID_PLACEHOLDER', activeId)" target="_blank" class="flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-red-50 hover:text-red-700">PDF</a>
                                        <a :href="'{{ route($ruteXls, 'ID_PLACEHOLDER') }}'.replace('ID_PLACEHOLDER', activeId)" class="flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Excel</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL EDIT UMK -->
        <!-- ========================================== -->
        @if($showEditModal)
        <div class="fixed inset-0 z-[110] overflow-y-auto">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeEditModal"></div>

            <!-- Wrapper Center: PAKAI min-h-screen & flex -->
            <div class="flex min-h-screen items-center justify-center p-4 sm:p-6">
                <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl flex flex-col max-h-[85vh]">
                    
                    <!-- Header -->
                    <div class="bg-white px-5 py-4 border-b border-slate-100 flex items-start justify-between shrink-0">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Edit UMK</h3>
                            <p class="text-sm text-slate-500">
                                <span class="md:hidden">Langkah {{ $editStep }} dari 2</span>
                                <span class="hidden md:inline">Ubah data perjalanan</span>
                            </p>
                        </div>
                        <button type="button" wire:click="closeEditModal" class="text-slate-400 hover:text-slate-500 p-1">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Body (Scrollable) -->
                    <div class="p-5 sm:p-6 overflow-y-auto flex-1 bg-white">
                        
                        <!-- STEP 1 -->
                        <div class="{{ $editStep === 1 ? 'block' : 'hidden' }} space-y-4">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800 mb-1">Tempat Berangkat</label>
                                    <input wire:model="editTempatBerangkat" type="text" class="block w-full rounded-lg border bg-white px-3 py-2 text-sm border-slate-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800 mb-1">Maksud Perjalanan</label>
                                    <input wire:model="editMaksud" type="text" class="block w-full rounded-lg border bg-white px-3 py-2 text-sm border-slate-300">
                                    @error('editMaksud') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800 mb-1">Provinsi</label>
                                    <select wire:model.live="editProvinsi" class="block w-full rounded-lg border bg-white px-3 py-2 text-sm border-slate-300">
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach($provinsis as $prov)
                                            <option value="{{ $prov }}">{{ $prov }}</option>
                                        @endforeach
                                    </select>
                                    @error('editProvinsi') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-800 mb-1">Kota Tujuan</label>
                                    <select wire:model="editKotaTujuanId" class="block w-full rounded-lg border bg-white px-3 py-2 text-sm border-slate-300" @if(!$editProvinsi) disabled @endif>
                                        <option value="">-- Pilih Kota --</option>
                                        @foreach($kotas as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('editKotaTujuanId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-800 mb-1">Alat Angkutan</label>
                                        <select wire:model="editAlatAngkutan" class="block w-full rounded-lg border bg-white px-3 py-2 text-sm border-slate-300">
                                            <option value="Pesawat">Pesawat</option>
                                            <option value="Kereta">Kereta</option>
                                            <option value="Bus">Bus</option>
                                            <option value="Mobil">Mobil</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-800 mb-1">Tingkat Biaya</label>
                                        <select wire:model="editTingkatBiaya" class="block w-full rounded-lg border bg-white px-3 py-2 text-sm border-slate-300">
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-800 mb-1">Tgl Berangkat</label>
                                        <input wire:model.live="editTanggalBerangkat" type="date" class="block w-full rounded-lg border bg-white px-3 py-2 text-sm border-slate-300">
                                        @error('editTanggalBerangkat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-800 mb-1">Tgl Pulang</label>
                                        <input wire:model.live="editTanggalPulang" type="date" class="block w-full rounded-lg border bg-white px-3 py-2 text-sm border-slate-300">
                                        @error('editTanggalPulang') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2 -->
                        <div class="{{ $editStep === 2 ? 'block' : 'hidden' }}">
                            <div class="space-y-3">
                                @foreach($editRincian as $index => $item)
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 space-y-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                                            <select wire:model="editRincian.{{ $index }}.coa_id" class="block w-full rounded-lg border bg-white px-2.5 py-1.5 text-sm border-slate-300">
                                                @foreach($coas as $c)
                                                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan</label>
                                            <input wire:model="editRincian.{{ $index }}.keterangan" type="text" class="block w-full rounded-lg border bg-white px-2.5 py-1.5 text-sm border-slate-300">
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-700 mb-1">Qty</label>
                                                <input wire:model.live="editRincian.{{ $index }}.qty" type="number" class="block w-full rounded-lg border bg-white px-2.5 py-1.5 text-sm border-slate-300">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-700 mb-1">Harga</label>
                                                <input wire:model.live="editRincian.{{ $index }}.harga_satuan" type="number" class="block w-full rounded-lg border bg-white px-2.5 py-1.5 text-sm border-slate-300">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah</label>
                                            <div class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-sm font-semibold text-slate-900">
                                                {{ number_format($item['nominal'] ?? 0, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="bg-slate-50 px-5 py-4 border-t border-slate-200 rounded-b-2xl shrink-0 flex items-center justify-between">
                        <div class="text-left">
                            <span class="block text-xs font-semibold text-slate-500">Total Estimasi:</span>
                            <span class="text-base font-extrabold text-slate-900">Rp {{ number_format($editTotalEstimasi, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex gap-2">
                            @if($editStep === 1)
                                <button type="button" wire:click="nextEditStep" class="rounded-lg px-4 py-2 text-sm font-semibold text-white bg-blue-900 hover:bg-blue-800">Selanjutnya</button>
                            @else
                                <button type="button" wire:click="prevEditStep" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-200 hover:bg-slate-300">Kembali</button>
                                <button type="submit" wire:click="updateUmk" class="rounded-lg px-4 py-2 text-sm font-semibold text-white bg-blue-900 hover:bg-blue-800">Simpan</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!-- End Modal Edit -->

    </div>
</div>