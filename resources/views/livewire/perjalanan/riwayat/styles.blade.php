{{-- Semua CSS khusus halaman Riwayat UMK (kartu statistik, overlay, modal dokumen, modal edit, responsif). --}}
<style>
    /* ====== Modal UMK: CSS biasa (tidak bergantung build Tailwind) ====== */
    .swal2-container { z-index: 20000 !important; }
    body.umk-lock { overflow: hidden; }

    /* Kartu statistik: mobile ditumpuk, desktop 3 kolom sejajar */
    .umk-stats { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin-bottom: 24px; }
    @media (min-width: 640px) { .umk-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; } }

    /* Overlay: modal SELALU di tengah. */
    .umk-ov {
        position: fixed; inset: 0; z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        box-sizing: border-box; padding: 16px;
        background: rgba(15, 23, 42, .6);
    }
    @media (max-width: 1023.98px) {
        .umk-ov {
            padding: calc(72px + env(safe-area-inset-top, 0px)) 12px calc(76px + env(safe-area-inset-bottom, 0px));
        }
    }

    .umk-card {
        position: relative; display: flex; flex-direction: column;
        width: 100%; max-height: 100%; box-sizing: border-box;
        background: #fff; border-radius: 16px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .35);
    }
    .umk-card-sm { max-width: 480px; }
    .umk-card-lg { max-width: 768px; }

    .umk-head { flex: none; padding: 14px 16px 12px; border-bottom: 1px solid #e2e8f0; background: #fff; }
    .umk-head-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .umk-h { margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; }
    .umk-sub { margin: 2px 0 0; font-size: 12px; color: #64748b; word-break: break-word; }
    .umk-x { flex: none; width: 36px; height: 36px; margin: -4px -6px 0 0; display: flex; align-items: center; justify-content: center; border: 0; border-radius: 9999px; background: none; color: #64748b; cursor: pointer; }
    .umk-x:hover { background: #f1f5f9; }
    .umk-steps { display: flex; gap: 6px; margin-top: 10px; }
    .umk-steps i { flex: 1; height: 6px; border-radius: 9999px; background: #e2e8f0; transition: background .2s; }
    .umk-steps i.on { background: #1e3a8a; }

    .umk-body { flex: 1 1 auto; min-height: 0; overflow-x: hidden; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch; padding: 16px; background: #f8fafc; }
    .umk-foot { flex: none; padding: 12px 16px; border-top: 1px solid #e2e8f0; background: #fff; }
    .umk-foot-in { display: flex; flex-direction: column; gap: 10px; }
    .umk-total { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; }
    .umk-total span { font-size: 12px; font-weight: 500; color: #64748b; }
    .umk-total strong { font-size: 18px; font-weight: 800; color: #0f172a; }
    .umk-btns { display: flex; gap: 10px; }

    .umk-btn { flex: 1 1 0%; min-height: 48px; padding: 0 16px; border: 0; border-radius: 12px; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; transition: background .15s, opacity .15s; }
    .umk-btn-g { background: #f1f5f9; color: #334155; }
    .umk-btn-g:hover { background: #e2e8f0; }
    .umk-btn-p { flex: 2 1 0%; background: #1e3a8a; color: #fff; }
    .umk-btn-p:hover { background: #1e40af; }
    .umk-btn:disabled { opacity: .6; cursor: wait; }

    .umk-form { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 14px 12px; padding: 14px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
    .umk-full { grid-column: 1 / -1; }
    .umk-form > div { min-width: 0; }
    .umk-lb { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 600; color: #1e293b; }
    .umk-lb-s { font-size: 12px; color: #334155; }
    .umk-in { display: block; width: 100%; min-width: 0; max-width: 100%; box-sizing: border-box; min-height: 44px; padding: 10px 14px; font: inherit; font-size: 16px; color: #0f172a; background: #fff; border: 1px solid #cbd5e1; border-radius: 12px; outline: none; }
    .umk-in::-webkit-date-and-time-value { text-align: left; }
    select.umk-in { text-overflow: ellipsis; }
    .umk-in:focus { border-color: #1d4ed8; box-shadow: 0 0 0 3px rgba(29, 78, 216, .15); }
    .umk-in:disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }
    .umk-er { margin: 6px 0 0; font-size: 12px; font-weight: 500; color: #dc2626; }
    .umk-hint { margin: 0 0 12px; font-size: 12px; color: #64748b; }

    .umk-item { margin-bottom: 12px; padding: 14px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
    .umk-item-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
    .umk-chip { padding: 3px 10px; border-radius: 9999px; background: #eff6ff; color: #1e40af; font-size: 12px; font-weight: 700; }
    .umk-item-sub { font-size: 14px; font-weight: 800; color: #0f172a; }
    .umk-item-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 12px; }
    .umk-item-grid > div { min-width: 0; }
    .umk-item-grid .s2 { grid-column: span 2; }
    .umk-sm-only { display: none; }
    .umk-readonly { display: flex; align-items: center; min-height: 40px; padding: 8px 12px; box-sizing: border-box; font-size: 14px; font-weight: 600; color: #0f172a; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; }
    .umk-empty { padding: 32px 16px; text-align: center; font-size: 14px; color: #64748b; background: #fff; border: 1px dashed #cbd5e1; border-radius: 16px; }

    .umk-sum { margin-bottom: 12px; padding: 14px 16px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
    .umk-sum-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
    .umk-sum-head h4 { margin: 0; font-size: 14px; font-weight: 700; color: #0f172a; }
    .umk-link { border: 0; background: none; padding: 0; font: inherit; font-size: 12px; font-weight: 600; color: #1e40af; cursor: pointer; }
    .umk-sum-row { display: flex; justify-content: space-between; gap: 16px; padding: 5px 0; font-size: 14px; }
    .umk-sum-row > span { flex: none; color: #64748b; }
    .umk-sum-row > strong { text-align: right; font-weight: 600; color: #0f172a; word-break: break-word; }
    .umk-sum-item { padding: 8px 0; border-top: 1px solid #f1f5f9; }
    .umk-sum-item:first-of-type { border-top: 0; padding-top: 0; }
    .umk-sum-item small { display: block; margin-top: 2px; font-size: 12px; font-weight: 400; color: #64748b; }
    .umk-sum-item strong { text-align: left; }

    .umk-doc { margin-bottom: 12px; padding: 14px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
    .umk-doc-top { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .umk-badge { flex: none; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: #eff6ff; color: #1e40af; font-size: 12px; font-weight: 700; }
    .umk-doc-name { font-size: 14px; font-weight: 600; line-height: 1.3; color: #1e293b; }
    .umk-doc-btns { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .umk-dl { display: flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; border-radius: 8px; color: #fff; font-size: 14px; font-weight: 700; text-decoration: none; }
    .umk-dl-pdf { background: #dc2626; } .umk-dl-pdf:hover { background: #b91c1c; }
    .umk-dl-xls { background: #059669; } .umk-dl-xls:hover { background: #047857; }

    .umk-anim { animation: umk-pop .18s ease-out; }
    @keyframes umk-pop { from { opacity: 0; transform: scale(.97); } to { opacity: 1; transform: scale(1); } }
    @media (prefers-reduced-motion: reduce) { .umk-anim { animation: none; } }

    @media (min-width: 640px) {
        .umk-head { padding: 20px 24px 16px; }
        .umk-h { font-size: 18px; }
        .umk-sub { font-size: 14px; }
        .umk-body { padding: 20px 24px; background: #fff; }
        .umk-foot { padding: 14px 24px; background: #f8fafc; }
        .umk-foot-in { flex-direction: row; align-items: center; justify-content: space-between; }
        .umk-btn { flex: none; min-height: 40px; padding: 0 20px; }
        .umk-form { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); padding: 0; border: 0; background: transparent; gap: 16px; }
        .umk-full { grid-column: auto; }
        .umk-in { min-height: 40px; font-size: 14px; }
        .umk-item { background: #f8fafc; }
        .umk-item-sub { display: none; }
        .umk-item-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .umk-item-grid .s2.s1-sm { grid-column: span 1; }
        .umk-sm-only { display: block; }
        .umk-card-sm .umk-body { background: #f8fafc; }

        .umk-card-sm { max-width: 540px; }
        .umk-card-sm .umk-body { background: #fff; padding: 12px 24px 20px; }
        .umk-card-sm .umk-foot { display: none; }
        .umk-card-sm .umk-hint { margin-bottom: 8px; }
        .umk-doc { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 0; padding: 10px 0; background: transparent; border: 0; border-radius: 0; border-top: 1px solid #f1f5f9; }
        .umk-doc-top { margin-bottom: 0; gap: 12px; min-width: 0; }
        .umk-badge { width: 32px; height: 32px; border-radius: 8px; font-size: 11px; }
        .umk-doc-btns { display: flex; flex: none; gap: 8px; }
        .umk-dl { min-height: 0; padding: 7px 14px; font-size: 12px; font-weight: 600; border-radius: 8px; gap: 6px; }
    }
</style>