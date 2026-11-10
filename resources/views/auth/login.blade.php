<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Silakan masuk menggunakan NIP, Email, atau Username Anda.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- NIP / Email / Username -->
        <div>
            <x-input-label for="email" value="NIP / Email / Username" />
            <x-text-input id="email" class="block mt-1 w-full"
                          type="text"
                          name="email"
                          :value="old('email')"
                          required autofocus autocomplete="username"
                          placeholder="Masukkan NIP, email, atau username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- SweetAlert untuk Pesan Login -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Jika ada error login
            @if ($errors->has('email'))
                let errorMessage = "{{ $errors->first('email') }}";
                
                // Cek apakah ini pesan rate limiting
                if (errorMessage.includes('Terlalu banyak') || errorMessage.includes('detik')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Diblokir Sementara',
                        text: 'Anda telah gagal login 5 kali. Silakan coba lagi dalam beberapa menit.',
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Mengerti'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        text: 'NIP/Email/Username atau password yang Anda masukkan salah.',
                        confirmButtonColor: '#1e3a8a',
                        confirmButtonText: 'Coba Lagi'
                    });
                }
            @endif
        });
    </script>
</x-guest-layout>