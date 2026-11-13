<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\TransaksiServisController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MonitoringController;

/*
|--------------------------------------------------------------------------
| Web Routes - Bengkel Padang Motor
|--------------------------------------------------------------------------
*/

// Auth (Guest)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Kasir + Pemilik)
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Pelanggan CRUD
    Route::resource('pelanggan', PelangganController::class);
    Route::get('pelanggan/{pelanggan}/motors', [PelangganController::class, 'getMotors'])->name('pelanggan.motors');

    // Sparepart CRUD
    Route::resource('sparepart', SparepartController::class);
    Route::patch('sparepart/{sparepart}/stock', [SparepartController::class, 'updateStock'])->name('sparepart.update-stock');
    Route::get('api/sparepart/search', [SparepartController::class, 'search'])->name('api.sparepart.search');

    // Motor CRUD
    Route::resource('motor', MotorController::class);

    // Transaksi Servis CRUD
    Route::resource('transaksi', TransaksiServisController::class);
    Route::patch('transaksi/{transaksi}/status', [TransaksiServisController::class, 'updateStatus'])->name('transaksi.update-status');
    Route::get('transaksi/{transaksi}/nota', [TransaksiServisController::class, 'nota'])->name('transaksi.nota');

    // Prediksi API
    Route::post('api/prediksi', [PrediksiController::class, 'predict'])->name('api.prediksi');
    Route::get('/dashboard/pendapatan-chart', [DashboardController::class, 'getPendapatanChart'])->name('dashboard.pendapatan-chart');
    Route::get('api/motor/{motor}/last-service', [PrediksiController::class, 'getLastService'])->name('api.motor.last-service');
    Route::get('/servis/riwayat-motor/{motor_id}', [TransaksiServisController::class, 'getRiwayatMotor'])->name('api.motor.riwayat');

    // Prediksi Test Page
    Route::get('prediksi/test', [PrediksiController::class, 'testPage'])->name('prediksi.test');
    Route::post('prediksi/test', [PrediksiController::class, 'testPredict'])->name('prediksi.test.submit');
    Route::get('api/prediksi/status', [PrediksiController::class, 'apiStatus'])->name('api.prediksi.status');

    // Pemilik Only Routes
    Route::middleware('role:pemilik')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/detail/{tahun}/{bulan}', [LaporanController::class, 'detail'])->name('laporan.detail');
        Route::get('/laporan/chart-data', [LaporanController::class, 'chartData'])->name('laporan.chart-data');
        Route::get('/monitoring-model', [MonitoringController::class, 'index'])->name('monitoring.index');
    });
});
