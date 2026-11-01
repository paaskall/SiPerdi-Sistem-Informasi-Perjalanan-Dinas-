@php
    $inp = fn ($field) => 'mt-1.5 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 '
        . ($errors->has($field) ? 'border-red-400' : 'border-slate-300');
@endphp

<section>
    <header>
        <h2 class="text-lg font-bold tracking-tight text-slate-900">Informasi Profil</h2>
        <p class="mt-1 text-sm text-slate-500">Perbarui nama, alamat email, dan tanda tangan akun Anda.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- enctype diperlukan untuk upload file -->
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-semibold text-slate-800">Nama</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="{{ $inp('name') }}">
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-800">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="{{ $inp('email') }}">
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    <p>
                        Alamat email Anda belum diverifikasi.
                        <button form="send-verification" class="font-semibold underline hover:text-amber-900 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2">
                            Kirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-emerald-700">Tautan verifikasi baru telah dikirim ke alamat email Anda.</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- FORM UPLOAD TTD (HANYA UNTUK ROLE PIMPINAN) -->
        <!-- ========================================== -->
        @if (auth()->user()->hasRole('pimpinan'))
        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5">
            <label for="ttd_path" class="block text-sm font-bold text-slate-800">Gambar Tanda Tangan</label>
            <p class="mt-1 text-xs text-slate-500 mb-4">Unggah gambar tanda tangan Anda (Background transparan disarankan). Tanda tangan ini akan otomatis tercetak pada dokumen SPPD.</p>

            @if($user->ttd_path)
                <div class="mb-4 flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-3 shadow-sm">
                    <img src="{{ asset('storage/' . $user->ttd_path) }}" alt="TTD" class="h-20 w-auto object-contain">
                    <div class="text-sm">
                        <span class="inline-flex items-center gap-1 font-semibold text-emerald-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Tanda tangan tersimpan
                        </span>
                    </div>
                </div>
            @endif

            <input type="file" name="ttd_path" id="ttd_path" accept="image/*"
                   class="mt-2 block w-full cursor-pointer rounded-lg border bg-white text-sm text-slate-600 file:mr-4 file:cursor-pointer file:border-0 file:bg-blue-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-blue-900 hover:file:bg-blue-100 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->has('ttd_path') ? 'border-red-400' : 'border-slate-300' }}">
            <p class="mt-1.5 text-xs text-slate-500">Format JPG atau PNG. Maksimal 2MB.</p>
            <x-input-error class="mt-2" :messages="$errors->get('ttd_path')" />
        </div>
        @endif
        <!-- ========================================== -->

        <div class="flex flex-wrap items-center gap-4 pt-1">
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 max-sm:w-full">
                Simpan
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>