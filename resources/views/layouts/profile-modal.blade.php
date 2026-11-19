{{-- resources/views/layouts/profile-modal.blade.php --}}
{{-- HANYA untuk role super-admin. Modal Profil: dibuka lewat openProfileModal(event). CSS & JS biasa, tidak butuh build Tailwind. --}}
@if (Auth::check() && Auth::user()->hasRole('super-admin'))
@php
    $pmUser          = Auth::user();
    $pmReopen        = old('_modal');          // 'profil' | 'sandi' kalau habis gagal validasi
    $showPasswordTab = true;                   // set false jika login 100% lewat SSO
    $pmInitials      = strtoupper(collect(preg_split('/\s+/', trim($pmUser->name)))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode(''));

    $pmPangkat  = \App\Models\Pangkat::orderBy('id')->get();
    $pmGolongan = \App\Models\Golongan::orderBy('nama')->get();
    $pmJabatan  = \App\Models\Jabatan::orderBy('level')->get();
    $pmDivisi   = \App\Models\Divisi::orderBy('nama')->get();

    $pmField = fn ($f) => 'pm-input' . ($errors->has($f) ? ' is-err' : '');
    $pmPw = [
        ['id' => 'pm_current_password',      'name' => 'current_password',      'label' => 'Kata sandi saat ini',         'auto' => 'current-password'],
        ['id' => 'pm_password',              'name' => 'password',              'label' => 'Kata sandi baru',             'auto' => 'new-password'],
        ['id' => 'pm_password_confirmation', 'name' => 'password_confirmation', 'label' => 'Konfirmasi kata sandi baru', 'auto' => 'new-password'],
    ];
@endphp

<style>
    .pm-overlay { position: fixed; inset: 0; z-index: 100; display: none; align-items: center; justify-content: center; padding: 16px; background: rgba(15, 23, 42, .5); backdrop-filter: blur(4px); }
    .pm-overlay.is-open { display: flex; }
    .pm-dialog { width: 100%; max-width: 44rem; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; background: #fff; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .25); }
    @media (max-width: 639.98px) {
        .pm-overlay { align-items: flex-end; padding: 0; }
        .pm-dialog { max-width: none; max-height: 92vh; border-radius: 16px 16px 0 0; padding-bottom: env(safe-area-inset-bottom, 0px); }
    }

    .pm-head { display: flex; align-items: center; gap: 12px; padding: 16px 20px; border-bottom: 1px solid #e2e8f0; }
    .pm-avatar { width: 40px; height: 40px; flex: none; display: flex; align-items: center; justify-content: center; border-radius: 9999px; background: #1e3a8a; color: #fff; font-size: 14px; font-weight: 800; }
    .pm-who { min-width: 0; }
    .pm-title { margin: 0; font-size: 16px; font-weight: 800; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .pm-sub { margin: 2px 0 0; font-size: 12px; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .pm-close { margin-left: auto; flex: none; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 0; border-radius: 8px; background: none; color: #64748b; cursor: pointer; }
    .pm-close:hover { background: #f1f5f9; color: #0f172a; }

    .pm-tabs { display: flex; padding: 0 20px; border-bottom: 1px solid #e2e8f0; }
    .pm-tab { margin-right: 20px; padding: 12px 2px; border: 0; border-bottom: 2px solid transparent; background: none; font: inherit; font-size: 14px; font-weight: 600; color: #64748b; cursor: pointer; }
    .pm-tab:hover { color: #1e293b; }
    .pm-tab.is-on { color: #0f172a; border-bottom-color: #1e3a8a; }

    .pm-scroll { flex: 1 1 auto; overflow-y: auto; padding: 20px; }
    .pm-pane { display: none; }
    .pm-pane.is-on { display: block; }
    .pm-desc { margin: 0 0 16px; font-size: 14px; color: #64748b; }

    .pm-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
    @media (min-width: 640px) { .pm-grid { grid-template-columns: 1fr 1fr; } }
    .pm-stack { display: grid; gap: 16px; }

    .pm-label { display: block; font-size: 14px; font-weight: 600; color: #1e293b; }
    .pm-input { display: block; width: 100%; box-sizing: border-box; margin-top: 6px; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; font: inherit; font-size: 14px; color: #0f172a; }
    .pm-input::placeholder { color: #94a3b8; }
    .pm-input:focus { outline: none; border-color: #1e3a8a; box-shadow: 0 0 0 3px rgba(30, 58, 138, .2); }
    .pm-input.is-err { border-color: #f87171; }
    .pm-err { margin: 6px 0 0; font-size: 12px; color: #dc2626; }
    .pm-hint { margin: 6px 0 0; font-size: 12px; color: #64748b; }

    .pm-pw { position: relative; }
    .pm-pw .pm-input { padding-right: 44px; }
    .pm-eye { position: absolute; top: 6px; right: 0; bottom: 0; width: 44px; display: flex; align-items: center; justify-content: center; border: 0; background: none; color: #64748b; cursor: pointer; }
    .pm-eye:hover { color: #1e3a8a; }
    .pm-eye svg { width: 20px; height: 20px; }
    .pm-eye [data-icon="hide"] { display: none; }
    .pm-eye.is-shown [data-icon="show"] { display: none; }
    .pm-eye.is-shown [data-icon="hide"] { display: block; }

    .pm-warn { margin-top: 10px; padding: 10px 14px; border: 1px solid #fde68a; border-radius: 8px; background: #fffbeb; font-size: 13px; color: #92400e; }
    .pm-warn button { padding: 0; border: 0; background: none; font: inherit; font-weight: 600; text-decoration: underline; color: inherit; cursor: pointer; }
    .pm-ttd { padding: 16px; border: 1px dashed #cbd5e1; border-radius: 12px; background: #f8fafc; }
    .pm-ttd img { display: block; max-height: 80px; width: auto; margin-bottom: 12px; padding: 8px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; }

    .pm-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 16px; border-top: 1px solid #f1f5f9; }
    .pm-btn { display: inline-flex; align-items: center; justify-content: center; padding: 10px 22px; border: 1px solid transparent; border-radius: 8px; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; transition: background-color .15s; }
    .pm-btn-primary { background: #1e3a8a; color: #fff; }
    .pm-btn-primary:hover { background: #1e40af; }
    .pm-btn-primary:disabled { opacity: .6; cursor: wait; }
    .pm-btn-ghost { background: #fff; border-color: #cbd5e1; color: #334155; }
    .pm-btn-ghost:hover { border-color: #1e3a8a; color: #1e3a8a; }
    @media (max-width: 639.98px) { .pm-actions { flex-direction: column-reverse; } .pm-btn { width: 100%; } }
</style>

<div id="pmOverlay" class="pm-overlay" role="dialog" aria-modal="true" aria-labelledby="pmTitle">
    <div class="pm-dialog">

        <div class="pm-head">
            <span class="pm-avatar">{{ $pmInitials }}</span>
            <div class="pm-who">
                <h2 id="pmTitle" class="pm-title">{{ $pmUser->name }}</h2>
                <p class="pm-sub">{{ $pmUser->email }}</p>
            </div>
            <button type="button" class="pm-close" onclick="closeProfileModal()" aria-label="Tutup">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="pm-tabs" role="tablist">
            <button type="button" class="pm-tab is-on" data-pm-tab="profil" onclick="switchProfileTab('profil')">Informasi Profil</button>
            @if ($showPasswordTab)
                <button type="button" class="pm-tab" data-pm-tab="sandi" onclick="switchProfileTab('sandi')">Kata Sandi</button>
            @endif
        </div>

        <div class="pm-scroll">

            {{-- Form kirim ulang verifikasi (tidak boleh nested di form lain) --}}
            <form id="pm-send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

            {{-- ========== TAB PROFIL ========== --}}
            <div class="pm-pane is-on" data-pm-pane="profil">
                <p class="pm-desc">Perbarui data diri, jabatan, dan tanda tangan akun Anda.</p>

                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" onsubmit="pmBusy(this)">
                    @csrf
                    @method('patch')
                    <input type="hidden" name="_modal" value="profil">

                    <div class="pm-grid">
                        <div>
                            <label class="pm-label" for="pm_nip">NIP</label>
                            <input id="pm_nip" name="nip" type="text" value="{{ old('nip', $pmUser->nip) }}" placeholder="Ketik NIP Anda" class="{{ $pmField('nip') }}" required>
                            @foreach ($errors->get('nip') as $m)<p class="pm-err">{{ $m }}</p>@endforeach
                        </div>

                        <div>
                            <label class="pm-label" for="pm_name">Nama Lengkap</label>
                            <input id="pm_name" name="name" type="text" value="{{ old('name', $pmUser->name) }}" autocomplete="name" class="{{ $pmField('name') }}" required>
                            @foreach ($errors->get('name') as $m)<p class="pm-err">{{ $m }}</p>@endforeach
                        </div>

                        <div>
                            <label class="pm-label" for="pm_nama_panggilan">Nama Panggilan</label>
                            <input id="pm_nama_panggilan" name="nama_panggilan" type="text" value="{{ old('nama_panggilan', $pmUser->nama_panggilan) }}" placeholder="Contoh: Budi" class="{{ $pmField('nama_panggilan') }}">
                            @foreach ($errors->get('nama_panggilan') as $m)<p class="pm-err">{{ $m }}</p>@endforeach
                        </div>

                        <div>
                            <label class="pm-label" for="pm_pangkat_id">Pangkat</label>
                            <select id="pm_pangkat_id" name="pangkat_id" class="{{ $pmField('pangkat_id') }}">
                                <option value="">Pilih Pangkat</option>
                                @foreach ($pmPangkat as $p)
                                    <option value="{{ $p->id }}" @selected(old('pangkat_id', $pmUser->pangkat_id) == $p->id)>{{ $p->nama }} ({{ $p->golongan_ruang }})</option>
                                @endforeach
                            </select>
                            @foreach ($errors->get('pangkat_id') as $m)<p class="pm-err">{{ $m }}</p>@endforeach
                        </div>

                        <div>
                            <label class="pm-label" for="pm_golongan_id">Golongan</label>
                            <select id="pm_golongan_id" name="golongan_id" class="{{ $pmField('golongan_id') }}">
                                <option value="">Pilih Golongan</option>
                                @foreach ($pmGolongan as $g)
                                    <option value="{{ $g->id }}" @selected(old('golongan_id', $pmUser->golongan_id) == $g->id)>{{ $g->nama }}</option>
                                @endforeach
                            </select>
                            @foreach ($errors->get('golongan_id') as $m)<p class="pm-err">{{ $m }}</p>@endforeach
                        </div>

                        <div>
                            <label class="pm-label" for="pm_jabatan_id">Jabatan</label>
                            <select id="pm_jabatan_id" name="jabatan_id" class="{{ $pmField('jabatan_id') }}">
                                <option value="">Pilih Jabatan</option>
                                @foreach ($pmJabatan as $j)
                                    <option value="{{ $j->id }}" @selected(old('jabatan_id', $pmUser->jabatan_id) == $j->id)>{{ $j->nama }}</option>
                                @endforeach
                            </select>
                            @foreach ($errors->get('jabatan_id') as $m)<p class="pm-err">{{ $m }}</p>@endforeach
                        </div>

                        <div>
                            <label class="pm-label" for="pm_divisi_id">Divisi</label>
                            <select id="pm_divisi_id" name="divisi_id" class="{{ $pmField('divisi_id') }}">
                                <option value="">Pilih Divisi</option>
                                @foreach ($pmDivisi as $d)
                                    <option value="{{ $d->id }}" @selected(old('divisi_id', $pmUser->divisi_id) == $d->id)>{{ $d->nama }}</option>
                                @endforeach
                            </select>
                            @foreach ($errors->get('divisi_id') as $m)<p class="pm-err">{{ $m }}</p>@endforeach
                        </div>

                        <div>
                            <label class="pm-label" for="pm_email">Email</label>
                            <input id="pm_email" name="email" type="email" value="{{ old('email', $pmUser->email) }}" autocomplete="username" class="{{ $pmField('email') }}" required>
                            @foreach ($errors->get('email') as $m)<p class="pm-err">{{ $m }}</p>@endforeach

                            @if ($pmUser instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $pmUser->hasVerifiedEmail())
                                <div class="pm-warn">
                                    Alamat email Anda belum diverifikasi.
                                    <button type="submit" form="pm-send-verification">Kirim ulang email verifikasi.</button>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($pmUser->hasRole('pimpinan'))
                        <div class="pm-ttd" style="margin-top: 16px;">
                            <label class="pm-label" for="pm_ttd_path">Gambar Tanda Tangan</label>
                            <p class="pm-hint" style="margin-bottom: 10px;">Unggah gambar tanda tangan Anda (background transparan disarankan).</p>
                            @if ($pmUser->ttd_path)
                                <img src="{{ asset('storage/' . $pmUser->ttd_path) }}" alt="Tanda tangan saat ini">
                            @endif
                            <input id="pm_ttd_path" name="ttd_path" type="file" accept="image/*" class="{{ $pmField('ttd_path') }}">
                            <p class="pm-hint">Format JPG atau PNG. Maksimal 2MB.</p>
                            @foreach ($errors->get('ttd_path') as $m)<p class="pm-err">{{ $m }}</p>@endforeach
                        </div>
                    @endif

                    <div class="pm-actions">
                        <button type="button" class="pm-btn pm-btn-ghost" onclick="closeProfileModal()">Batal</button>
                        <button type="submit" class="pm-btn pm-btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            {{-- ========== TAB KATA SANDI ========== --}}
            @if ($showPasswordTab)
                <div class="pm-pane" data-pm-pane="sandi">
                    <p class="pm-desc">Gunakan kata sandi yang panjang dan acak agar akun Anda tetap aman.</p>

                    <form method="post" action="{{ route('password.update') }}" onsubmit="pmBusy(this)">
                        @csrf
                        @method('put')
                        <input type="hidden" name="_modal" value="sandi">

                        <div class="pm-stack">
                            @foreach ($pmPw as $f)
                                <div>
                                    <label class="pm-label" for="{{ $f['id'] }}">{{ $f['label'] }}</label>
                                    <div class="pm-pw">
                                        <input id="{{ $f['id'] }}" name="{{ $f['name'] }}" type="password" autocomplete="{{ $f['auto'] }}"
                                               class="pm-input{{ $errors->updatePassword->has($f['name']) ? ' is-err' : '' }}">
                                        <button type="button" class="pm-eye" data-pm-eye="{{ $f['id'] }}" aria-label="Tampilkan kata sandi">
                                            <svg data-icon="show" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <svg data-icon="hide" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                        </button>
                                    </div>
                                    @foreach ($errors->updatePassword->get($f['name']) as $m)<p class="pm-err">{{ $m }}</p>@endforeach
                                </div>
                            @endforeach
                        </div>

                        <div class="pm-actions">
                            <button type="button" class="pm-btn pm-btn-ghost" onclick="closeProfileModal()">Batal</button>
                            <button type="submit" class="pm-btn pm-btn-primary">Simpan Kata Sandi</button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
(function () {
    var overlay = document.getElementById('pmOverlay');

    window.switchProfileTab = function (name) {
        document.querySelectorAll('[data-pm-tab]').forEach(function (t) {
            t.classList.toggle('is-on', t.dataset.pmTab === name);
        });
        document.querySelectorAll('[data-pm-pane]').forEach(function (p) {
            p.classList.toggle('is-on', p.dataset.pmPane === name);
        });
    };

    window.openProfileModal = function (event, tab) {
        if (event && event.preventDefault) event.preventDefault();
        switchProfileTab(tab || 'profil');
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    };

    window.closeProfileModal = function () {
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    };

    window.pmBusy = function (form) {
        var btn = form.querySelector('button[type="submit"].pm-btn-primary');
        if (btn) { btn.disabled = true; btn.textContent = 'Menyimpan...'; }
    };

    // Tutup: klik area gelap / tombol Escape
    overlay.addEventListener('mousedown', function (e) { if (e.target === overlay) closeProfileModal(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) closeProfileModal();
    });

    // Tampil / sembunyikan kata sandi
    overlay.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-pm-eye]');
        if (!btn) return;
        var input = document.getElementById(btn.dataset.pmEye);
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.classList.toggle('is-shown', show);
        btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });

    // Auto-fill dari NIP (sama seperti halaman profil)
    var nipTimer;
    document.getElementById('pm_nip').addEventListener('input', function (e) {
        clearTimeout(nipTimer);
        var nip = e.target.value.trim();
        if (!nip) return;
        nipTimer = setTimeout(function () {
            fetch('{{ url('/api/profile') }}/' + encodeURIComponent(nip), {
                headers: { 'Accept': 'application/json' }, credentials: 'same-origin'
            }).then(function (r) { return r.json(); }).then(function (d) {
                if (!d || !d.name) return;
                var set = function (id, v) { var el = document.getElementById(id); if (el) el.value = v || ''; };
                set('pm_name', d.name);
                set('pm_nama_panggilan', d.nama_panggilan);
                set('pm_golongan_id', d.golongan_id);
                set('pm_jabatan_id', d.jabatan_id);
                set('pm_divisi_id', d.divisi_id);
                set('pm_pangkat_id', d.pangkat_id);
            }).catch(function () {});
        }, 500);
    });

    @if ($pmReopen === 'profil' || $pmReopen === 'sandi')
        // Habis gagal validasi: buka lagi modal di tab yang sama supaya error terlihat
        document.addEventListener('DOMContentLoaded', function () {
            openProfileModal(null, @json($pmReopen));
        });
    @endif
})();
</script>
@endif