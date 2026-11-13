<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * ValidationException dari LoginRequest akan otomatis ditangkap Laravel
     * dan di-redirect back dengan $errors->has('email') = true.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ✅ Tambah flash message untuk SweetAlert "Selamat datang"
        return redirect()
            ->intended(route('dashboard', absolute: false))
            ->with('message', 'Selamat datang, ' . Auth::user()->name . '!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Logout lokal Laravel dulu
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 2. Cek apakah server SSO (port 8002) hidup
        $ssoUrl = 'http://127.0.0.1:8002/sso-logout';
        $ssoAlive = false;

        try {
            Http::timeout(2)->head($ssoUrl);
            $ssoAlive = true;
        } catch (\Throwable $e) {
            $ssoAlive = false;
        }

        // 3. Kalau SSO hidup, redirect ke SSO logout
        if ($ssoAlive) {
            return redirect()->away($ssoUrl);
        }

        // 4. Fallback: kembali ke login dengan SweetAlert
        return redirect()
            ->route('login')
            ->with('message', 'Anda telah berhasil logout.');
    }
}