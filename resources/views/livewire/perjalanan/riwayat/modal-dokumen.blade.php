{{-- Modal Dokumen UMK (murni Alpine). Harus di-include DI DALAM elemen x-data milik riwayat-index,
     karena memakai dokumenOpen, activeNomor, docs, dan url() dari sana. --}}
<!-- ========================================================= -->
<!-- MODAL DOKUMEN -->
<!-- ========================================================= -->
<div x-show="dokumenOpen" style="display: none;" class="umk-ov"
     @click.self="dokumenOpen = false"
     @keydown.escape.window="dokumenOpen = false"
     role="dialog" aria-modal="true" aria-labelledby="dokumen-title">

    <div class="umk-card umk-card-sm umk-anim">
        <div class="umk-head umk-head-row">
            <div style="min-width:0">
                <h3 id="dokumen-title" class="umk-h">Dokumen UMK</h3>
                <p class="umk-sub" x-text="activeNomor"></p>
            </div>
            <button type="button" class="umk-x" @click="dokumenOpen = false" aria-label="Tutup">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="umk-body">
            <p class="umk-hint">Pilih format file untuk setiap dokumen. PDF terbuka di tab baru, Excel langsung terunduh.</p>

            <template x-for="doc in docs" :key="doc.kode">
                <div class="umk-doc">
                    <div class="umk-doc-top">
                        <span class="umk-badge" x-text="doc.kode"></span>
                        <span class="umk-doc-name" x-text="doc.label"></span>
                    </div>
                    <div class="umk-doc-btns">
                        <a :href="url(doc.pdf)" target="_blank" rel="noopener" class="umk-dl umk-dl-pdf">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            PDF
                        </a>
                        <a :href="url(doc.xls)" class="umk-dl umk-dl-xls">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/></svg>
                            Excel
                        </a>
                    </div>
                </div>
            </template>
        </div>

        <div class="umk-foot">
            <button type="button" class="umk-btn umk-btn-g" style="width:100%" @click="dokumenOpen = false">Tutup</button>
        </div>
    </div>
</div>