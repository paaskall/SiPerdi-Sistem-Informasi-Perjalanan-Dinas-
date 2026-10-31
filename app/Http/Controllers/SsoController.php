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
                Log::warning('SSO SIPERDI: Token tidak valid', ['status' => $response->status()]);
                return redirect()->route('login')->with('error', 'Token SSO tidak valid atau kadaluarsa.');
            }

            $data = $response->json();
            if (!isset($data['user'])) {
                return redirect()->route('login')->with('error', 'Data user tidak lengkap dari SSO.');
            }

            $ssoUser = $data['user'];

            // ========================================================
            // LOG 1: DATA MENTAH DARI SSO
            // ========================================================
            Log::info('SSO SIPERDI: [1] Data User dari SSO', [
                'username' => $ssoUser['username'] ?? 'null',
                'role_global' => $ssoUser['role'] ?? 'null',
                'apps' => $ssoUser['apps'] ?? [],
            ]);

            // ========================================================
            // EKSTRAK ROLE DARI SSO UNTUK SIPERDI
            // ========================================================
            $roleFromSso = 'pegawai'; // Default
            
            // Jika Super Admin di SSO, paksa jadi pimpinan
            if (isset($ssoUser['role']) && $ssoUser['role'] === 'super_admin') {
                $roleFromSso = 'pegawai'; 
            } 
            elseif (isset($ssoUser['apps']) && is_array($ssoUser['apps'])) {
                foreach ($ssoUser['apps'] as $app) {
                    if (isset($app['slug']) && $app['slug'] === 'siperdi') {
                        $roleFromSso = $app['role'] ?? 'pegawai';
                        break;
                    }
                }
            }

            // ========================================================
            // LOG 2: ROLE HASIL EKSTRAKSI
            // ========================================================
            Log::info('SSO SIPERDI: [2] Role yang akan diterapkan', ['role' => $roleFromSso]);

            // 2. Cari user di SIPERDI
            $user = User::where('sso_id', (string) $ssoUser['id'])->first();

            if (!$user && !empty($ssoUser['email'])) {
                $user = User::where('email', $ssoUser['email'])->first();
            }
            if (!$user && !empty($ssoUser['username'])) {
                $user = User::where('username', $ssoUser['username'])->first();
            }

            // ========================================================
            // AUTO-REGISTER & SINKRONISASI ROLE
            // ========================================================
            if ($user) {
                if (empty($user->sso_id)) {
                    $user->update(['sso_id' => (string) $ssoUser['id']]);
                }
                
                // ====================================================
                // LOG 3: SEBELUM SYNC
                // ====================================================
                Log::info('SSO SIPERDI: [3] Sebelum SyncRoles', [
                    'user_id' => $user->id,
                    'role_lama' => $user->getRoleNames()->toArray(),
                    'role_baru' => $roleFromSso
                ]);

                // PAKSA SYNC ROLE
                $user->syncRoles([$roleFromSso]);

                // ====================================================
                // LOG 4: SETELAH SYNC
                // ====================================================
                // Refresh model untuk memastikan data terbaru
                $user->refresh();
                Log::info('SSO SIPERDI: [4] Setelah SyncRoles', [
                    'user_id' => $user->id,
                    'role_sekarang' => $user->getRoleNames()->toArray(),
                ]);

            } else {
                $username = $ssoUser['username'] ?? explode('@', $ssoUser['email'] ?? 'user')[0] . $ssoUser['id'];
                $nip = $ssoUser['nip'] ?? '999' . str_pad($ssoUser['id'], 12, '0', STR_PAD_LEFT);
                $email = $ssoUser['email'] ?? $username . '@sso.local';

                $user = User::create([
                    'name'          => $ssoUser['name'] ?? 'User SSO',
                    'sso_id'        => (string) $ssoUser['id'],
                    'auth_provider' => 'sso',
                    'nip'           => $nip,
                    'username'      => $username,
                    'email'         => $email,
                    'password'      => null,
                    'aktif'         => 1,
                    'divisi_id'     => 1, 
                    'jabatan_id'    => 1,
                    'golongan_id'   => 1,
                ]);

                $user->assignRole($roleFromSso);

                Log::info('SSO SIPERDI: User baru didaftarkan', [
                    'sso_id' => $ssoUser['id'],
                    'role' => $roleFromSso
                ]);
            }

            // ========================================================
            // BERSIHKAN SESI LAMA & LOGIN BARU
            // ========================================================
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Auth::login($user, true);
            $request->session()->regenerate();

            // Redirect ke dashboard SIPERDI
            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            Log::error('SSO SIPERDI Callback Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Terjadi kesalahan sistem saat verifikasi SSO.');
        }
    }
}