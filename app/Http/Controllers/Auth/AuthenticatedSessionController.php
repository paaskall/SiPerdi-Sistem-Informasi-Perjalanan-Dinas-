<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
        } catch (ValidationException $e) {
            // Jika error, redirect dengan SweetAlert
            $errorMessage = $e->validator->errors()->first('email');

            // Cek apakah pesan error adalah pesan rate limiting
            if (str_contains($errorMessage, 'Too many login attempts') || str_contains($errorMessage, 'minutes')) {
                return redirect()->back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => $errorMessage])
                    ->with('login_error', $errorMessage);
            }

            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $errorMessage]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        // 1. Hapus session lokal Laravel dulu
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 2. Cek apakah server SSO (port 8002) hidup
        $ssoUrl = 'http://127.0.0.1:8002/sso-logout';
        $ssoAlive = false;

        try {
            $response = Http::timeout(2)->head($ssoUrl);
            $ssoAlive = true;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $ssoAlive = false;
        } catch (\Exception $e) {
            $ssoAlive = false;
        }

        if ($ssoAlive) {
            return redirect($ssoUrl);
        }

        return redirect('/')->with('status', 'Anda telah keluar. Server SSO tidak dapat dihubungi.');
    }
}