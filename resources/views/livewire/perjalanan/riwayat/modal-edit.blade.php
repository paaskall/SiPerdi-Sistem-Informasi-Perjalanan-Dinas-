{{-- Modal Edit UMK (Livewire, 3 langkah). Variabel yang dipakai datang dari riwayat-index / RiwayatIndex.php:
     $showEditModal, $editStep, $stepTitles, $provinsis, $kotas, $coas, $rp, serta properti edit* milik komponen. --}}
<!-- ========================================================= -->
<!-- MODAL EDIT UMK - satu komponen, selalu di tengah layar     -->
<!-- ========================================================= -->
@if($showEditModal)
<div class="umk-ov" wire:click.self="closeEditModal"
     role="dialog" aria-modal="true" aria-labelledby="edit-umk-title"
     x-data @keydown.escape.window="$wire.closeEditModal()">

    <form wire:submit="updateUmk"
          @keydown.enter="if ($event.target.tagName !== 'BUTTON') $event.preventDefault()"
          class="umk-card umk-card-lg umk-anim">

        <!-- Header + stepper -->
        <div class="umk-head">
            <div class="umk-head-row">
                <div style="min-width:0">
                    <h3 id="edit-umk-title" class="umk-h">Edit UMK</h3>
                    <p class="umk-sub">Langkah {{ $editStep }} dari 3 &middot; {{ $stepTitles[$editStep] ?? '' }}</p>
                </div>
                <button type="button" wire:click="closeEditModal" class="umk-x" aria-label="Tutup form edit">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="umk-steps" role="progressbar" aria-valuemin="1" aria-valuemax="3" aria-valuenow="{{ $editStep }}">
                @foreach($stepTitles as $n => $t)
                    <i class="{{ $editStep >= $n ? 'on' : '' }}"></i>
                @endforeach
            </div>
        </div>

        <!-- Isi: hanya bagian ini yang scroll -->
        <div class="umk-body"
             x-ref="body"
             x-effect="$wire.editStep; $nextTick(() => $refs.body && $refs.body.scrollTo({ top: 0 }))">

            <!-- STEP 1 -->
            <div style="{{ $editStep === 1 ? '' : 'display:none' }}">
                <div class="umk-form">
                    <div class="umk-full">
                        <label for="editTempatBerangkat" class="umk-lb">Tempat Berangkat</label>
                        <input id="editTempatBerangkat" wire:model="editTempatBerangkat" type="text" autocomplete="off" class="umk-in">
                        @error('editTempatBerangkat') <p class="umk-er">{{ $message }}</p> @enderror
                    </div>
                    <div class="umk-full">
                        <label for="editMaksud" class="umk-lb">Maksud Perjalanan</label>
                        <input id="editMaksud" wire:model="editMaksud" type="text" autocomplete="off" class="umk-in">
                        @error('editMaksud') <p class="umk-er">{{ $message }}</p> @enderror
                    </div>
                    <div class="umk-full">
                        <label for="editProvinsi" class="umk-lb">Provinsi</label>
                        <select id="editProvinsi" wire:model.live="editProvinsi" class="umk-in">
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($provinsis as $prov)
                                <option value="{{ $prov }}">{{ $prov }}</option>
                            @endforeach
                        </select>
                        @error('editProvinsi') <p class="umk-er">{{ $message }}</p> @enderror
                    </div>
                    <div class="umk-full">
                        <label for="editKotaTujuanId" class="umk-lb">Kota Tujuan</label>
                        <select id="editKotaTujuanId" wire:model="editKotaTujuanId" wire:loading.attr="disabled" wire:target="editProvinsi" class="umk-in" @if(!$editProvinsi) disabled @endif>
                            <option value="">{{ $editProvinsi ? '-- Pilih Kota --' : 'Pilih provinsi dulu' }}</option>
                            @foreach($kotas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        @error('editKotaTujuanId') <p class="umk-er">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="editAlatAngkutan" class="umk-lb">Alat Angkutan</label>
                        <select id="editAlatAngkutan" wire:model="editAlatAngkutan" class="umk-in">
                            <option value="Pesawat">Pesawat</option>
                            <option value="Kereta">Kereta</option>
                            <option value="Bus">Bus</option>
                            <option value="Mobil">Mobil</option>
                        </select>
                        @error('editAlatAngkutan') <p class="umk-er">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="editTingkatBiaya" class="umk-lb">Tingkat Biaya</label>
                        <select id="editTingkatBiaya" wire:model="editTingkatBiaya" class="umk-in">
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                        @error('editTingkatBiaya') <p class="umk-er">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="editTanggalBerangkat" class="umk-lb">Tgl Berangkat</label>
                        <input id="editTanggalBerangkat" wire:model.live="editTanggalBerangkat" type="date" class="umk-in">
                        @error('editTanggalBerangkat') <p class="umk-er">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="editTanggalPulang" class="umk-lb">Tgl Pulang</label>
                        <input id="editTanggalPulang" wire:model.live="editTanggalPulang" type="date" @if($editTanggalBerangkat) min="{{ $editTanggalBerangkat }}" @endif class="umk-in">
                        @error('editTanggalPulang') <p class="umk-er">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- STEP 2 -->
            <div style="{{ $editStep === 2 ? '' : 'display:none' }}">
                @forelse($editRincian as $index => $item)
                    <div wire:key="edit-rincian-{{ $index }}" class="umk-item">
                        <div class="umk-item-top">
                            <span class="umk-chip">Item {{ $index + 1 }}</span>
                            <span class="umk-item-sub">{{ $rp($item['nominal'] ?? 0) }}</span>
                        </div>
                        <div class="umk-item-grid">
                            <div class="s2">
                                <label for="rincian-{{ $index }}-coa" class="umk-lb umk-lb-s">Kategori</label>
                                <select id="rincian-{{ $index }}-coa" wire:model="editRincian.{{ $index }}.coa_id" class="umk-in">
                                    <option value="">-- Pilih --</option>
                                    @foreach($coas as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="s2">
                                <label for="rincian-{{ $index }}-ket" class="umk-lb umk-lb-s">Keterangan</label>
                                <input id="rincian-{{ $index }}-ket" wire:model="editRincian.{{ $index }}.keterangan" type="text" autocomplete="off" class="umk-in">
                            </div>
                            <div>
                                <label for="rincian-{{ $index }}-qty" class="umk-lb umk-lb-s">Qty</label>
                                <input id="rincian-{{ $index }}-qty" wire:model.live.debounce.500ms="editRincian.{{ $index }}.qty" type="number" inputmode="numeric" min="0" class="umk-in">
                            </div>
                            <div>
                                <label for="rincian-{{ $index }}-sat" class="umk-lb umk-lb-s">Satuan</label>
                                <input id="rincian-{{ $index }}-sat" wire:model="editRincian.{{ $index }}.satuan" type="text" autocomplete="off" class="umk-in">
                            </div>
                            <div class="s2 s1-sm">
                                <label for="rincian-{{ $index }}-hrg" class="umk-lb umk-lb-s">Harga Satuan (Rp)</label>
                                <input id="rincian-{{ $index }}-hrg" wire:model.live.debounce.500ms="editRincian.{{ $index }}.harga_satuan" type="number" inputmode="numeric" min="0" class="umk-in">
                            </div>
                            <div class="umk-sm-only">
                                <span class="umk-lb umk-lb-s">Jumlah</span>
                                <div class="umk-readonly">{{ $rp($item['nominal'] ?? 0) }}</div>
                            </div>
                        </div>
                        @foreach(['coa_id', 'keterangan', 'qty', 'satuan', 'harga_satuan'] as $f)
                            @error("editRincian.$index.$f") <p class="umk-er">{{ $message }}</p> @enderror
                        @endforeach
                    </div>
                @empty
                    <div class="umk-empty">Belum ada rincian biaya pada UMK ini.</div>
                @endforelse
            </div>

            <!-- STEP 3 -->
            <div style="{{ $editStep === 3 ? '' : 'display:none' }}">
                @php
                    $kotaNama = collect($kotas)->firstWhere('id', $editKotaTujuanId)?->nama;
                    $tglB = $editTanggalBerangkat ? \Carbon\Carbon::parse($editTanggalBerangkat)->format('d/m/Y') : '-';
                    $tglP = $editTanggalPulang ? \Carbon\Carbon::parse($editTanggalPulang)->format('d/m/Y') : '-';
                    $ringkas = [
                        'Berangkat dari' => $editTempatBerangkat ?: '-',
                        'Tujuan' => trim(($kotaNama ?: '-') . ($editProvinsi ? ', ' . $editProvinsi : '')),
                        'Maksud' => $editMaksud ?: '-',
                        'Tanggal' => $tglB . ' s/d ' . $tglP,
                        'Angkutan / Tingkat' => ($editAlatAngkutan ?: '-') . ' / ' . ($editTingkatBiaya ?: '-'),
                    ];
                @endphp

                <div class="umk-sum">
                    <div class="umk-sum-head">
                        <h4>Info perjalanan</h4>
                        <button type="button" wire:click="prevEditStep" class="umk-link">Ubah</button>
                    </div>
                    @foreach($ringkas as $k => $v)
                        <div class="umk-sum-row"><span>{{ $k }}</span><strong>{{ $v }}</strong></div>
                    @endforeach
                </div>

                <div class="umk-sum">
                    <div class="umk-sum-head"><h4>Rincian biaya ({{ count($editRincian) }} item)</h4></div>
                    @foreach($editRincian as $item)
                        <div class="umk-sum-row umk-sum-item">
                            <div style="min-width:0">
                                <strong>{{ $item['keterangan'] ?: (collect($coas)->firstWhere('id', $item['coa_id'] ?? null)?->nama ?? '-') }}</strong>
                                <small>{{ $item['qty'] ?? 0 }} {{ $item['satuan'] ?? '' }} &times; {{ $rp($item['harga_satuan'] ?? 0) }}</small>
                            </div>
                            <strong style="flex:none">{{ $rp($item['nominal'] ?? 0) }}</strong>
                        </div>
                    @endforeach
                </div>

                <p class="umk-hint" style="text-align:center">Periksa kembali data di atas, lalu tekan <b>Simpan perubahan</b>.</p>
            </div>
        </div>

        <!-- Footer: total selalu terlihat + tombol navigasi (di dalam kartu) -->
        <div class="umk-foot">
            <div class="umk-foot-in">
                <div class="umk-total"><span>Total estimasi</span><strong>{{ $rp($editTotalEstimasi) }}</strong></div>
                <div class="umk-btns">
                    @if($editStep === 1)
                        <button type="button" wire:click="closeEditModal" class="umk-btn umk-btn-g">Batal</button>
                        <button type="button" wire:click="nextEditStep" wire:loading.attr="disabled" wire:target="nextEditStep" class="umk-btn umk-btn-p">Selanjutnya</button>
                    @elseif($editStep === 2)
                        <button type="button" wire:click="prevEditStep" class="umk-btn umk-btn-g">Kembali</button>
                        <button type="button" wire:click="nextEditStep" wire:loading.attr="disabled" wire:target="nextEditStep" class="umk-btn umk-btn-p">Lanjut konfirmasi</button>
                    @else
                        <button type="button" wire:click="prevEditStep" class="umk-btn umk-btn-g">Kembali</button>
                        <button type="button" onclick="confirmSave(event)" wire:loading.attr="disabled" wire:target="updateUmk" class="umk-btn umk-btn-p">
                            <span wire:loading.remove wire:target="updateUmk">Simpan perubahan</span>
                            <span wire:loading wire:target="updateUmk">Menyimpan...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
@endif