<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SsoController;
use Illuminate\Support\Facades\Route;
use App\Livewire\MasterData\DivisiIndex;
use App\Livewire\MasterData\GolonganIndex;
use App\Livewire\MasterData\PangkatIndex;
use App\Livewire\MasterData\CoaIndex;
use App\Livewire\MasterData\KotaIndex;
use App\Livewire\MasterData\TarifHariIndex;
use App\Livewire\MasterData\UserIndex;
use App\Livewire\Perjalanan\FormPengajuan;
use App\Livewire\Perjalanan\RiwayatIndex;
use App\Http\Controllers\Api\OcrController; 
use App\Livewire\AuditTrailIndex;
use App\Livewire\Perjalanan\FormLaporan;
use App\Livewire\Perjalanan\RiwayatLaporan;
use App\Livewire\Perjalanan\VerifikasiUmkIndex;
use App\Livewire\Perjalanan\VerifikasiLaporanIndex;
use App\Livewire\Perjalanan\DetailLaporan;
use Spatie\Permission\Middleware\RoleMiddleware;

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
    
    // API Auto-fill Profil
    Route::get('/api/profile/{nip}', function ($nip) {
        $user = \App\Models\User::where('nip', $nip)->first();
        if (!$user) return response()->json([]);
        return response()->json([
            'name' => $user->name,
            'nama_panggilan' => $user->nama_panggilan,
            'pangkat_id' => $user->pangkat_id,
            'golongan_id' => $user->golongan_id,
            'jabatan_id' => $user->jabatan_id,
            'divisi_id' => $user->divisi_id,
        ]);
    })->name('api.profile.nip');

    // Master Data
    Route::get('/master/divisi', DivisiIndex::class)->name('master.divisi');
    Route::get('/master/golongan', GolonganIndex::class)->name('master.golongan');
    Route::get('/master/pangkat', PangkatIndex::class)->name('master.pangkat');
    Route::get('/master/coa', CoaIndex::class)->name('master.coa');
    Route::get('/master/kota', KotaIndex::class)->name('master.kota');
    Route::get('/master/tarif', TarifHariIndex::class)->name('master.tarif');

    // Manajemen User (Super Admin)
    Route::middleware(RoleMiddleware::class . ':super-admin')->group(function () {
        Route::get('/master/users', UserIndex::class)->name('master.users');
    });

    // Perjalanan & UMK
    Route::get('/perjalanan/riwayat', RiwayatIndex::class)->name('perjalanan.riwayat');
    Route::get('/perjalanan/buat', FormPengajuan::class)->name('perjalanan.buat');
    Route::get('/perjalanan/umk/{id}', [\App\Http\Controllers\UmkController::class, 'cetak'])->name('umk.cetak');
    Route::get('/perjalanan/umk/{id}/excel', [\App\Http\Controllers\UmkController::class, 'downloadExcel'])->name('umk.excel');

    // Verifikasi UMK (Keuangan)
    Route::get('/perjalanan/verifikasi-umk', VerifikasiUmkIndex::class)->name('perjalanan.verifikasi-umk');

    // Laporan & Verifikasi Laporan
    Route::get('/perjalanan/lapor/{id}', FormLaporan::class)->name('perjalanan.lapor');
    Route::get('/laporan/riwayat', RiwayatLaporan::class)->name('laporan.riwayat');
    Route::get('/perjalanan/verifikasi-laporan', VerifikasiLaporanIndex::class)->name('perjalanan.verifikasi-laporan');
    Route::get('/laporan/{id}', DetailLaporan::class)->name('laporan.detail');
    Route::get('/perjalanan/umk/{id}/rincian-excel', [\App\Http\Controllers\UmkController::class, 'downloadRincianExcel'])->name('umk.rincian-excel');
    Route::get('/perjalanan/umk/{id}/rincian-pdf', [\App\Http\Controllers\UmkController::class, 'cetakRincian'])->name('umk.rincian-pdf');
    Route::get('/perjalanan/umk/{id}/kwitansi-excel', [\App\Http\Controllers\UmkController::class, 'downloadKwitansiExcel'])->name('umk.kwitansi-excel');
    Route::get('/perjalanan/umk/{id}/kwitansi-pdf', [\App\Http\Controllers\UmkController::class, 'cetakKwitansi'])->name('umk.kwitansi-pdf');
    Route::get('/perjalanan/umk/{id}/spd-excel', [\App\Http\Controllers\UmkController::class, 'downloadSpdExcel'])->name('umk.spd-excel');
    Route::get('/perjalanan/umk/{id}/spd-pdf', [\App\Http\Controllers\UmkController::class, 'cetakSpd'])->name('umk.spd-pdf');
    Route::get('/perjalanan/umk/{id}/dpr-excel', [\App\Http\Controllers\UmkController::class, 'downloadDprExcel'])->name('umk.dpr-excel');
    Route::get('/perjalanan/umk/{id}/dpr-pdf', [\App\Http\Controllers\UmkController::class, 'cetakDpr'])->name('umk.dpr-pdf');
    Route::get('/perjalanan/umk/{id}/amplop-excel', [\App\Http\Controllers\UmkController::class, 'downloadAmplopExcel'])->name('umk.amplop-excel');
    Route::get('/perjalanan/umk/{id}/amplop-pdf', [\App\Http\Controllers\UmkController::class, 'cetakAmplop'])->name('umk.amplop-pdf');

    // API OCR
    Route::post('/api/ocr', [OcrController::class, 'scan'])->name('api.ocr');

    // Audit Trail
    Route::get('/audit-trail', AuditTrailIndex::class)
        ->middleware(RoleMiddleware::class . ':pimpinan|keuangan|super-admin')
        ->name('audit.index');

    // Logout
    Route::post('/logout', function (\Illuminate\Http\Request $request) {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('http://127.0.0.1:8002/sso-logout');
    })->name('logout.local');
});

require __DIR__.'/auth.php';