<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GudangController;
use App\Http\Controllers\Api\PenawaranController;
use App\Http\Controllers\Api\InvoiceApiController;
use App\Http\Controllers\Api\ProductApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Base URL: /api
|
*/

// --- 1. Autentikasi Publik ---
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// --- 2. Endpoint Terproteksi (Laravel Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Profil
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Modul Gudang & Persiapan
    Route::prefix('gudang')->name('api.gudang.')->group(function () {
        Route::get('/persiapan', [GudangController::class, 'indexPersiapan'])->name('persiapan.index');
        Route::post('/persiapan/{invoice}/buat', [GudangController::class, 'buatPersiapan'])->name('persiapan.buat');
        Route::get('/persiapan/{invoice}', [GudangController::class, 'showPersiapan'])->name('persiapan.show');
        Route::post('/persiapan/{invoice}/konfirmasi', [GudangController::class, 'konfirmasiPersiapan'])->name('persiapan.konfirmasi');
        Route::get('/produk', [ProductApiController::class, 'index'])->name('produk.index');
    });

    // Master Data Produk & Stok
    Route::prefix('produk')->name('api.produk.')->group(function () {
        Route::get('/', [ProductApiController::class, 'index'])->name('index');
        Route::get('/{product}', [ProductApiController::class, 'show'])->name('show');
    });

    // Histori Penawaran
    Route::prefix('penawaran')->name('api.penawaran.')->group(function () {
        Route::get('/', [PenawaranController::class, 'index'])->name('index');
        Route::get('/{offer}', [PenawaranController::class, 'show'])->name('show');
    });

    // Histori Invoice
    Route::prefix('invoice')->name('api.invoice.')->group(function () {
        Route::get('/', [InvoiceApiController::class, 'index'])->name('index');
        Route::get('/{invoice}', [InvoiceApiController::class, 'show'])->name('show');
    });
});
