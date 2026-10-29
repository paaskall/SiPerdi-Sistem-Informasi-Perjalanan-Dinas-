<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    private string $ssoServerUrl = 'http://127.0.0.1:8002';

    public function callback(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'Token SSO tidak ditemukan.');
        }

        try {
            // 1. Verifikasi token ke SSO Server
            $response = Http::timeout(10)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get($this->ssoServerUrl . '/api/user');

            if (!$response->successful()) {
                return redirect()->route('login')->with('error', 'Token SSO tidak valid atau kadaluarsa.');
            }

            $data = $response->json();
            if (!isset($data['user'])) {
                return redirect()->route('login')->with('error', 'Data user tidak lengkap dari SSO.');
            }

            $ssoUser = $data['user'];

            // 2. Cari user di SIPERDI berdasarkan SSO ID (paling akurat)
            $user = User::where('sso_id', (string) $ssoUser['id'])->first();

            // Fallback: Cari berdasarkan email atau username (jika sso_id belum terisi)
            if (!$user && !empty($ssoUser['email'])) {
                $user = User::where('email', $ssoUser['email'])->first();
            }
            if (!$user && !empty($ssoUser['username'])) {
                $user = User::where('username', $ssoUser['username'])->first();
            }

            // ========================================================
            // AUTO-REGISTER & UPDATE (Menyesuaikan struktur tabel SIPERDI)
            // ========================================================
            if ($user) {
                // Jika user sudah ada tapi belum punya sso_id, update sso_id-nya
                if (empty($user->sso_id)) {
                    $user->update([
                        'sso_id' => (string) $ssoUser['id'],
                        'auth_provider' => 'sso'
                    ]);
                }
            } else {
                // Jika user belum ada, buat baru.
                // Perhatikan: NIP dan Username WAJIB di SIPERDI.
                if (empty($ssoUser['nip']) || empty($ssoUser['username'])) {
                    Log::error('SSO SIPERDI: Gagal auto-register. NIP atau Username kosong di SSO.');
                    return redirect()->route('login')->with('error', 'Akun Anda di SSO belum memiliki NIP/Username. Hubungi Admin.');
                }

                $user = User::create([
                    'name'          => $ssoUser['name'] ?? 'User SSO',
                    'sso_id'        => (string) $ssoUser['id'],
                    'auth_provider' => 'sso',
                    'nip'           => $ssoUser['nip'],
                    'username'      => $ssoUser['username'],
                    'email'         => $ssoUser['email'] ?? $ssoUser['username'] . '@instansi.go.id', // email fallback jika kosong
                    'password'      => null, // Boleh null karena kolom SIPERDI mengizinkan NULL
                    'aktif'         => 1,    // Default aktif
                    // 'divisi_id'  => 1,    // Opsional: Set default divisi jika wajib
                    // 'jabatan_id' => 1,    // Opsional: Set default jabatan
                    // 'golongan_id'=> 1,    // Opsional: Set default golongan
                ]);
                Log::info('SSO SIPERDI: User baru berhasil didaftarkan otomatis', ['sso_id' => $ssoUser['id']]);
            }

            // 3. Auto-login
            Auth::login($user, true);
            $request->session()->regenerate();

            // 4. Redirect ke dashboard SIPERDI
            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            Log::error('SSO SIPERDI Callback Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Terjadi kesalahan sistem saat verifikasi SSO.');
        }
    }
}