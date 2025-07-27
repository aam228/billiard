<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PesananMakananController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SettingsController; // Make sure this is imported

Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard (auth)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Group route yang butuh login
Route::middleware(['auth'])->group(function () {

    // Profile (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Meja
    Route::resource('meja', MejaController::class);
    Route::post('meja/update-status', [MejaController::class, 'updateStatus'])->name('meja.updateStatus');
    Route::post('meja/{meja}/reset-status', [MejaController::class, 'resetStatus'])->name('meja.reset-status');

    // Transaksi
    Route::prefix('transaksi')->controller(TransaksiController::class)->group(function () {
        Route::get('create/{meja_id}', 'create')->name('transaksi.create');
        Route::post('/', 'store')->name('transaksi.store');
        Route::get('selesai/{transaksi_id}', 'selesai')->name('transaksi.selesai');
        Route::delete('{transaksi}', 'hapus')->name('transaksi.hapus');
        Route::get('histori', 'histori')->name('transaksi.histori');

        // Laporan dan Cetak Laporan
        Route::get('laporan', 'laporan')->name('transaksi.laporan');
        Route::get('laporan/cetak', 'cetakLaporan')->name('transaksi.cetak');
        
        // Auto Selesai - Consider if this should be a web route or an API route/scheduled task
        Route::post('auto-selesai/{id}', 'autoSelesai')->name('transaksi.autoSelesai');
    });

    Route::get('transaksi/{transaksi}', [PesananMakananController::class, 'redirectFromTransaksi'])->name('transaksi.show');
    Route::get('pesanan/create/{transaksi_id}', [PesananMakananController::class, 'create'])->name('pesanan.create');
    Route::post('pesanan', [PesananMakananController::class, 'store'])->name('pesanan.store');
    Route::get('api/pesanan-makanan/{transaksi_id}', [PesananMakananController::class, 'getPesananByTransaksi'])->name('api.pesanan.byTransaksi');

    // Produk
    Route::resource('produk', ProdukController::class);
    Route::get('produk/{id}/edit-data', [ProdukController::class, 'editData'])->name('produk.edit-data');
    Route::get('produk-json/{produk}', [ProdukController::class, 'editJson'])->name('produk.edit.json');

    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Settings
    Route::prefix('settings')->controller(SettingsController::class)->group(function () {
        Route::get('/', 'index')->name('settings.index');
        Route::patch('/profile', 'updateProfile')->name('settings.updateProfile');
        Route::patch('/password', 'updatePassword')->name('settings.updatePassword');
        Route::patch('/theme', 'updateTheme')->name('settings.updateTheme');
        // Add the new route for profile image upload
        Route::patch('/profile-image', 'updateProfileImage')->name('settings.updateProfileImage'); 
    });

});

require __DIR__.'/auth.php';