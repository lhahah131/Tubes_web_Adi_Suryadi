<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Guru Routes
    Route::prefix('guru')->group(function () {
        Route::get('/', [DashboardController::class, 'guruDashboard'])->name('guru.dashboard');
        Route::get('/absensi', [DashboardController::class, 'guruAbsensi'])->name('guru.absensi');
        Route::get('/generate-qr', [DashboardController::class, 'guruGenerateQR'])->name('guru.generate_qr');
        Route::get('/pengajuan-izin', [DashboardController::class, 'guruPengajuanIzin'])->name('guru.pengajuan_izin');
        Route::post('/pengajuan-izin/{id}/approve', [DashboardController::class, 'guruPengajuanApprove'])->name('guru.pengajuan_izin.approve');
        Route::post('/pengajuan-izin/{id}/reject', [DashboardController::class, 'guruPengajuanReject'])->name('guru.pengajuan_izin.reject');
        Route::get('/laporan', [DashboardController::class, 'guruLaporan'])->name('guru.laporan');
    });

    // Siswa Routes
    Route::prefix('siswa')->group(function () {
        Route::get('/', [DashboardController::class, 'siswaDashboard'])->name('siswa.dashboard');
        Route::get('/absensi', [DashboardController::class, 'siswaAbsensi'])->name('siswa.absensi');
        Route::get('/scan-qr', [DashboardController::class, 'siswaScanQR'])->name('siswa.scan_qr');
        Route::get('/pengajuan-izin', [DashboardController::class, 'siswaPengajuanIzin'])->name('siswa.pengajuan_izin');
        Route::post('/pengajuan-izin', [DashboardController::class, 'siswaPengajuanIzinStore'])->name('siswa.pengajuan_izin.store');
        Route::get('/riwayat', [DashboardController::class, 'siswaRiwayat'])->name('siswa.riwayat');
    });
});
