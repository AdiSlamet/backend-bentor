<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Driver\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\TiketBantuanController;
use App\Http\Controllers\Admin\TiketResponController;
use App\Http\Controllers\Admin\DokumenVerifikasiController;
use App\Http\Controllers\Driver\DokumenDriverController as DriverDokumenDriverController;
use App\Http\Controllers\Driver\KendaraanController;
use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\Admin\AreaOperasionalController;
use App\Http\Controllers\Admin\PenarikanDanaController;
use App\Http\Controllers\Admin\PromosiController;
use App\Http\Controllers\Admin\TarifController;
use App\Http\Controllers\User\AlamatFavoritController;
use App\Http\Controllers\User\MetodePembayaranController;
use App\Http\Controllers\User\PenumpangController;
use App\Http\Controllers\Shared\PembayaranController;
use App\Http\Controllers\Shared\PerjalananController;
use App\Http\Controllers\Shared\RatingController;
use App\Http\Controllers\User\TopupSaldoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Admin Auth Routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth:admin');
    Route::get('/me', [AdminAuthController::class, 'me'])->middleware('auth:admin');
    
});

// ini bagian untuk admin

// crud admin
Route::get('/admins', [AdminController::class, 'index']);
Route::get('/admins/{id}', [AdminController::class, 'show']);
Route::post('/admins', [AdminController::class, 'store']);
Route::put('/admins/{id}', [AdminController::class, 'update']);
Route::delete('/admins/{id}', [AdminController::class, 'destroy']);

// Admin CRUD Routes (Hanya bisa diakses oleh admin)
// Route::middleware('auth:admin')->prefix('admin')->group(function () {

// });
// Route::apiResource('admins', AdminController::class);

// CRUD Tiket Bantuan
Route::get('/tiket-bantuan', [TiketBantuanController::class, 'index']);
Route::get('/tiket-bantuan/{id}', [TiketBantuanController::class, 'show']);
Route::post('/tiket-bantuan', [TiketBantuanController::class, 'store']);
Route::put('/tiket-bantuan/{id}', [TiketBantuanController::class, 'update']);
Route::delete('/tiket-bantuan/{id}', [TiketBantuanController::class, 'destroy']);

// CRUD Tiket Respon
Route::get('/tiket-bantuan/{tiket_id}/respon', [TiketResponController::class, 'index']);
Route::post('/tiket-bantuan/{tiket_id}/respon', [TiketResponController::class, 'store']);
Route::put('/tiket-bantuan/{tiket_id}/respon/{id}', [TiketResponController::class, 'update']);
Route::delete('/tiket-bantuan/{tiket_id}/respon/{id}', [TiketResponController::class, 'destroy']);

// CRUD area operasional
Route::get('/area-operasional', [AreaOperasionalController::class, 'index']);
Route::get('/area-operasional/{id}', [AreaOperasionalController::class, 'show']);
Route::post('/area-operasional', [AreaOperasionalController::class, 'store']);
Route::put('/area-operasional/{id}', [AreaOperasionalController::class, 'update'])  ;
Route::delete('/area-operasional/{id}', [AreaOperasionalController::class, 'destroy']);

// CRUD Dokumen Verifikasi
Route::get('/dokumen', [DokumenVerifikasiController::class, 'index']);
Route::get('/dokumen/{id}', [DokumenVerifikasiController::class, 'show']);
// Route::post('/dokumen', [DokumenVerifikasiController::class, 'store']);
Route::put('/verify/{id}', [DokumenVerifikasiController::class, 'verify'])->middleware('admin');
// Route::put('/dokumen/{id}', [DokumenVerifikasiController::class, 'update']);
// Route::delete('/dokumen/{id}', [DokumenVerifikasiController::class, 'destroy']);

// CRUD Penarikan Dana
Route::get('/penarikan-dana', [PenarikanDanaController::class, 'index']);
Route::get('/penarikan-dana/{id}', [PenarikanDanaController::class, 'show']);
Route::post('/penarikan-dana', [PenarikanDanaController::class, 'store']);
Route::put('/penarikan-dana/{id}', [PenarikanDanaController::class, 'update']);
Route::delete('/penarikan-dana/{id}', [PenarikanDanaController::class, 'destroy']);

// CRUD Promosi
Route::get('/promosi', [PromosiController::class, 'index']);
Route::get('/promosi/{id}', [PromosiController::class, 'show']);
Route::post('/promosi', [PromosiController::class, 'store']);
Route::put('/promosi/{id}', [PromosiController::class, 'updateStatus']);
Route::delete('/promosi/{id}', [PromosiController::class, 'destroy']);

// CRUD Tarif
Route::get('/tarif', [TarifController::class, 'index']);
Route::get('/tarif/{id}', [TarifController::class, 'show']);
Route::post('/tarif', [TarifController::class, 'store']);
Route::put('/tarif/{id}', [TarifController::class, 'update']);
Route::delete('/tarif/{id}', [TarifController::class, 'destroy']);

// CRUD Tiket Bantuan
Route::get('/tiket-bantuan', [TiketBantuanController::class, 'index']);
Route::get('/tiket-bantuan/{id}', [TiketBantuanController::class, 'show']);
Route::post('/tiket-bantuan', [TiketBantuanController::class, 'store']);
Route::put('/tiket-bantuan/{id}', [TiketBantuanController::class, 'update']);
Route::delete('/tiket-bantuan/{id}', [TiketBantuanController::class, 'destroy']);


// Admin Verification Routes
// Route::middleware('auth:admin')->prefix('admin')->group(function () {
//     Route::get('dokumen', [DokumenVerifikasiController::class, 'index']);
//     Route::put('dokumen/{id}/verify', [DokumenVerifikasiController::class, 'verify']);
// });

// bagian untuk admin end


// bagain hanya untuk driver_________________________________________________________________
// Driver Auth Routes
Route::middleware('auth:driver')->prefix('driver')->group(function () {
    // Route::apiResource('dokumen', DokumenDriverController::class)->except(['update']);
});

Route::prefix('driver')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:driver');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:driver');
});

// CRUD Driver
Route::middleware('auth:driver')->prefix('driver')->group(function () {

});
Route::get('/drivers', [DriverController::class, 'index']);
Route::get('/drivers/{id}', [DriverController::class, 'show']);
Route::post('/drivers', [DriverController::class, 'store']);
Route::put('/drivers/{id}', [DriverController::class, 'update']);
Route::delete('/drivers/{id}', [DriverController::class, 'destroy']);

// CRUD Dokumen Driver
Route::get('/dokumen-driver', [DriverDokumenDriverController::class, 'index']);
Route::get('/dokumen-driver/{id}', [DriverDokumenDriverController::class, 'show']);
Route::post('/dokumen-driver', [DriverDokumenDriverController::class, 'store']);
Route::put('/dokumen-driver/{id}', [DriverDokumenDriverController::class, 'update']);
Route::delete('/dokumen-driver/{id}', [DriverDokumenDriverController::class, 'destroy']);

// CRUD Kendaraan
Route::get('/kendaraan', [KendaraanController::class, 'index'])->middleware('auth:driver');
Route::get('/kendaraan/{id}', [KendaraanController::class, 'show'])->middleware('auth:driver');
Route::post('/kendaraan', [KendaraanController::class, 'store'])->middleware('auth:driver');
Route::put('/kendaraan/{id}', [KendaraanController::class, 'update'])->middleware('auth:driver');
Route::delete('/kendaraan/{id}', [KendaraanController::class, 'destroy'])->middleware('auth:driver');

// CRUD Penarikan Dana
Route::get('/penarikan-dana', [PenarikanDanaController::class, 'index'])->middleware('auth:driver');
Route::get('/penarikan-dana/{id}', [PenarikanDanaController::class, 'show'])->middleware('auth:driver');
Route::post('/penarikan-dana', [PenarikanDanaController::class, 'store'])->middleware('auth:driver');
Route::put('/penarikan-dana/{id}', [PenarikanDanaController::class, 'update'])->middleware('auth:driver');
Route::delete('/penarikan-dana/{id}', [PenarikanDanaController::class, 'destroy'])->middleware('auth:driver');




// bagian untuk driver end

// bagian Sherd______________________________________________________________________________________________

// CRUD Pembayaran
Route::get('/pembayaran', [PembayaranController::class, 'index'])->middleware('auth:driver');
Route::get('/pembayaran/{id}', [PembayaranController::class, 'show'])->middleware('auth:driver');
Route::post('/pembayaran', [PembayaranController::class, 'store'])->middleware('auth:driver');
Route::put('/pembayaran/{id}', [PembayaranController::class, 'update'])->middleware('auth:driver');
Route::delete('/pembayaran/{id}', [PembayaranController::class, 'destroy'])->middleware('auth:driver');

// CRUD perjalanan
Route::get('/perjalanan', [PerjalananController::class, 'index'])->middleware('auth:driver');
Route::get('/perjalanan/{id}', [PerjalananController::class, 'show'])->middleware('auth:driver');
Route::post('/perjalanan', [PerjalananController::class, 'store'])->middleware('auth:driver');
Route::put('/perjalanan/{id}', [PerjalananController::class, 'update'])->middleware('auth:driver');
Route::delete('/perjalanan/{id}', [PerjalananController::class, 'destroy'])->middleware('auth:driver');

// CRUD Rating
Route::get('/rating', [RatingController::class, 'index'])->middleware('auth:driver');
Route::get('/rating/{id}', [RatingController::class, 'show'])->middleware('auth:driver');
Route::post('/rating', [RatingController::class, 'store'])->middleware('auth:driver');
Route::put('/rating/{id}', [RatingController::class, 'update'])->middleware('auth:driver');
Route::delete('/rating/{id}', [RatingController::class, 'destroy'])->middleware('auth:driver');

// bagian Sherd end

// bagian untuk user _____________________________________________________________________________________________
// User Auth Routes
// Route::middleware('auth:user')->prefix('user')->group(function () {
//     Route::apiResource('alamat-favorit', AlamatFavoritController::class);
//     Route::apiResource('pembayaran', PembayaranController::class);
//     Route::apiResource('perjalanan', PerjalananController::class);
//     Route::apiResource('rating', RatingController::class);
// });

// CRUD Alamat Favorit
Route::get('/alamat-favorit', [AlamatFavoritController::class, 'index'])->middleware('auth:user');
Route::get('/alamat-favorit/{id}', [AlamatFavoritController::class, 'show'])->middleware('auth:user');
Route::post('/alamat-favorit', [AlamatFavoritController::class, 'store'])->middleware('auth:user');
Route::put('/alamat-favorit/{id}', [AlamatFavoritController::class, 'update'])->middleware('auth:user');
Route::delete('/alamat-favorit/{id}', [AlamatFavoritController::class, 'destroy'])->middleware('auth:user');

// CRUD Metode Pembayaran
Route::get('/metode-pembayaran', [MetodePembayaranController::class, 'index'])->middleware('auth:user');
Route::get('/metode-pembayaran/{id}', [MetodePembayaranController::class, 'show'])->middleware('auth:user');
Route::post('/metode-pembayaran', [MetodePembayaranController::class, 'store'])->middleware('auth:user');
Route::put('/metode-pembayaran/{id}', [MetodePembayaranController::class, 'update'])->middleware('auth:user');
Route::delete('/metode-pembayaran/{id}', [MetodePembayaranController::class, 'destroy'])->middleware('auth:user');

// CRUD Penumpang
Route::get('/penumpang', [PenumpangController::class, 'index'])->middleware('auth:user');
Route::get('/penumpang/{id}', [PenumpangController::class, 'show'])->middleware('auth:user');
Route::post('/penumpang', [PenumpangController::class, 'store'])->middleware('auth:user');
Route::put('/penumpang/{id}', [PenumpangController::class, 'update'])->middleware('auth:user');
Route::delete('/penumpang/{id}', [PenumpangController::class, 'destroy'])->middleware('auth:user');

// CRUD Top Up Saldo
Route::get('/topup', [TopupSaldoController::class, 'index'])->middleware('auth:user');
Route::get('/topup/{id}', [TopupSaldoController::class, 'show'])->middleware('auth:user');
Route::post('/topup', [TopupSaldoController::class, 'store'])->middleware('auth:user');
Route::put('/topup/{id}', [TopupSaldoController::class, 'update'])->middleware('auth:user');
Route::delete('/topup/{id}', [TopupSaldoController::class, 'destroy'])->middleware('auth:user');

