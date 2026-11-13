<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = $this->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email'
            : (ctype_digit($login) ? 'nip' : 'username');

        if (! Auth::attempt(
            [$field => $login, 'password' => $this->input('password')],
            $this->boolean('remember')
        )) {
            // Tambah hitungan gagal login (block window 5 menit)
            RateLimiter::hit($this->throttleKey(), 300);

            // Catat ke Audit Trail
            activity()
                ->causedBy(null)
                ->withProperties([
                    'ip' => $this->ip(),
                    'username_attempted' => $login,
                    'field' => $field,
                    'user_agent' => $this->userAgent(),
                ])
                ->log('Percobaan login gagal (password salah atau user tidak ada)');

            // ✅ Pesan Bahasa Indonesia yang konsisten (mudah dideteksi di JS)
            throw ValidationException::withMessages([
                'email' => 'NIP / Email / Username atau password yang Anda masukkan salah.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        // Catat ke Audit Trail
        activity()
            ->causedBy(null)
            ->withProperties([
                'ip' => $this->ip(),
                'username_attempted' => $this->input('email'),
            ])
            ->log('Percobaan login diblokir (terlalu banyak gagal)');

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $minutes = ceil($seconds / 60);

        // ✅ Pesan konsisten mengandung "Terlalu banyak" & "detik"
        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$minutes} menit ({$seconds} detik).",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}