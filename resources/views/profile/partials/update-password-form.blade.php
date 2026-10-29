@php
    $fields = [
        ['id' => 'update_password_current_password',      'name' => 'current_password',      'label' => 'Kata sandi saat ini',      'auto' => 'current-password'],
        ['id' => 'update_password_password',              'name' => 'password',              'label' => 'Kata sandi baru',          'auto' => 'new-password'],
        ['id' => 'update_password_password_confirmation', 'name' => 'password_confirmation', 'label' => 'Konfirmasi kata sandi baru', 'auto' => 'new-password'],
    ];
@endphp

<section>
    <header>
        <h2 class="text-lg font-bold tracking-tight text-slate-900">Ubah Kata Sandi</h2>
        <p class="mt-1 text-sm text-slate-500">Gunakan kata sandi yang panjang dan acak agar akun Anda tetap aman.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        @foreach ($fields as $f)
            <div>
                <label for="{{ $f['id'] }}" class="block text-sm font-semibold text-slate-800">{{ $f['label'] }}</label>
                <div class="relative mt-1.5">
                    <input id="{{ $f['id'] }}" name="{{ $f['name'] }}" type="password" autocomplete="{{ $f['auto'] }}"
                           class="block w-full rounded-lg border bg-white py-2.5 pl-3.5 pr-11 text-sm text-slate-900 focus:border-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-900/20 {{ $errors->updatePassword->has($f['name']) ? 'border-red-400' : 'border-slate-300' }}">
                    <button type="button" data-toggle-password="{{ $f['id'] }}" aria-label="Tampilkan kata sandi" aria-pressed="false"
                            class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-lg text-slate-500 hover:text-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900">
                        <svg data-icon="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <svg data-icon="hide" class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->updatePassword->get($f['name'])" class="mt-2" />
            </div>
        @endforeach

        <div class="flex flex-wrap items-center gap-4 pt-1">
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-900 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-900 focus-visible:ring-offset-2 max-sm:w-full">
                Simpan
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.togglePassword);
                if (!input) return;
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.querySelector('[data-icon="show"]').classList.toggle('hidden', show);
                btn.querySelector('[data-icon="hide"]').classList.toggle('hidden', !show);
                btn.setAttribute('aria-pressed', show ? 'true' : 'false');
                btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            });
        });
    </script>
</section>