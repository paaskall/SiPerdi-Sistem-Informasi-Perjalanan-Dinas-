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
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Hapus session lokal Laravel dulu (WAJIB, biar user langsung ke-logout dari SIPERDI)
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 2. Cek apakah server SSO (port 8002) hidup atau nggak
        $ssoUrl = 'http://127.0.0.1:8002/sso-logout';
        $ssoAlive = false;

        try {
            // Coba konek ke SSO dengan timeout 2 detik
            // Pakai HEAD request biar ringan (cuma ngecek koneksi, nggak download halaman)
            $response = Http::timeout(2)->head($ssoUrl);
            
            // Kalau dapet response (apapun statusnya, 200/302/404), berarti server hidup
            $ssoAlive = true;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Server SSO mati / nggak bisa dihubungi
            $ssoAlive = false;
        } catch (\Exception $e) {
            // Error lain (misal DNS, timeout, dll)
            $ssoAlive = false;
        }

        // 3. Redirect sesuai kondisi SSO
        if ($ssoAlive) {
            // SSO hidup → redirect ke SSO logout (logout total)
            return redirect($ssoUrl);
        }

        // SSO mati → redirect ke halaman login SIPERDI
        // Session lokal udah kehapus di langkah 1, jadi user nggak bisa akses dashboard lagi
        return redirect('/')->with('status', 'Anda telah keluar. Server SSO tidak dapat dihubungi.');
    }
}