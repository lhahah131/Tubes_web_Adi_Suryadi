<?php
// [ROUTE] Pengaturan Jalur URL & Navigasi Web

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;

Route::get('/', function () {
    return redirect('/login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Routes (Redirector)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Guru Routes
    Route::prefix('guru')->group(function () {
        Route::get('/', [GuruController::class, 'dashboard'])->name('guru.dashboard');
        Route::get('/absensi', [GuruController::class, 'absensi'])->name('guru.absensi');
        Route::get('/generate-qr', [GuruController::class, 'generateQR'])->name('guru.generate_qr');
        Route::post('/generate-qr', [GuruController::class, 'generateQR'])->name('guru.generate_qr.post'); // API
        Route::get('/pengajuan-izin', [GuruController::class, 'pengajuanIzin'])->name('guru.pengajuan_izin');
        Route::post('/pengajuan-izin/{id}/approve', [GuruController::class, 'pengajuanApprove'])->name('guru.pengajuan_izin.approve');
        Route::post('/pengajuan-izin/{id}/reject', [GuruController::class, 'pengajuanReject'])->name('guru.pengajuan_izin.reject');
        Route::delete('/pengajuan-izin/{id}', [GuruController::class, 'pengajuanDestroy'])->name('guru.pengajuan_izin.destroy');
        Route::get('/laporan', [GuruController::class, 'laporan'])->name('guru.laporan');
        Route::get('/laporan/{id}', [GuruController::class, 'laporanDetail'])->name('guru.laporan.detail');
        Route::delete('/absensi/{id}', [GuruController::class, 'absensiDestroy'])->name('guru.absensi.destroy');
        Route::delete('/absensi/clear/{id}', [GuruController::class, 'absensiClear'])->name('guru.absensi.clear');
    });

    // Siswa Routes
    Route::prefix('siswa')->group(function () {
        Route::get('/', [SiswaController::class, 'dashboard'])->name('siswa.dashboard');
        Route::get('/absensi', [SiswaController::class, 'absensi'])->name('siswa.absensi');
        Route::get('/scan-qr', [SiswaController::class, 'scanQR'])->name('siswa.scan_qr');
        Route::post('/scan-qr', [SiswaController::class, 'scanQRSubmit'])->name('siswa.scan_qr.submit'); // MOBILE API
        Route::get('/pengajuan-izin', [SiswaController::class, 'pengajuanIzin'])->name('siswa.pengajuan_izin');
        Route::post('/pengajuan-izin', [SiswaController::class, 'pengajuanIzinStore'])->name('siswa.pengajuan_izin.store');
        Route::get('/riwayat', [SiswaController::class, 'riwayat'])->name('siswa.riwayat');
    });
});
