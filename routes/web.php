<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SsoController;
use Illuminate\Support\Facades\Route;
use App\Livewire\MasterData\DivisiIndex;
use App\Livewire\MasterData\GolonganIndex;
use App\Livewire\MasterData\CoaIndex;
use App\Livewire\MasterData\KotaIndex;
use App\Livewire\MasterData\TarifHariIndex;
use App\Livewire\Perjalanan\FormPengajuan;
use App\Livewire\Perjalanan\PersetujuanIndex;
use App\Livewire\Perjalanan\RiwayatIndex;
use App\Livewire\Perjalanan\PimpinanIndex;
use App\Http\Controllers\SppdController;
use App\Livewire\Perjalanan\FormReimbursement;
use App\Livewire\Perjalanan\ReimbursementIndex;
use App\Http\Controllers\Api\OcrController; // TAMBAHAN UNTUK OCR

// SSO CALLBACK
Route::get('/sso/callback', [SsoController::class, 'callback'])->name('sso.callback');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Master Data
    Route::get('/master/divisi', DivisiIndex::class)->name('master.divisi');
    Route::get('/master/golongan', GolonganIndex::class)->name('master.golongan');
    Route::get('/master/coa', CoaIndex::class)->name('master.coa');
    Route::get('/master/kota', KotaIndex::class)->name('master.kota');
    Route::get('/master/tarif', TarifHariIndex::class)->name('master.tarif');

    // Route Pegawai (Pengajuan & Riwayat)
    Route::get('/perjalanan/riwayat', RiwayatIndex::class)->name('perjalanan.riwayat');
    Route::get('/perjalanan/buat', FormPengajuan::class)->name('perjalanan.buat');

    // Route Persetujuan (Keuangan & Pimpinan)
    Route::get('/perjalanan/persetujuan', PersetujuanIndex::class)->name('perjalanan.persetujuan');
    Route::get('/perjalanan/pimpinan', PimpinanIndex::class)->name('perjalanan.pimpinan');
    Route::get('/perjalanan/sppd/{id}', [SppdController::class, 'generate'])->name('sppd.pdf');

    // Route Reimbursement (Sprint 4)
    Route::get('/perjalanan/reimb/{id}', FormReimbursement::class)->name('perjalanan.reimb');
    Route::get('/reimbursement/list', ReimbursementIndex::class)->name('reimbursement.index');

    // Route API OCR Gemini
    Route::post('/api/ocr', [OcrController::class, 'scan'])->name('api.ocr');

    // ==========================================
    // ROUTE LOGOUT LOKAL (JARING PENGAMAN SSO)
    // ==========================================
    Route::post('/logout', function (\Illuminate\Http\Request $request) {
        // 1. Hapus session lokal Laravel (biar user langsung ke-logout dari aplikasi ini)
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 2. Redirect ke SSO Logout
        // Kalau SSO mati, browser bakal error "Unable to connect", 
        // TAPI session lokal udah aman kehapus. User nggak bisa akses aplikasi lagi.
        return redirect('http://127.0.0.1:8002/sso-logout');
    })->name('logout.local');
});

require __DIR__.'/auth.php';