<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProductOfferController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\BastController;
use App\Http\Controllers\SkpController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecapController;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES (Halaman Utama / Landing Page)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Jika user sudah login, arahkan langsung ke dashboard
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('front.landing'); // Pastikan file resources/views/landing.blade.php sudah ada
})->name('front.landing');

/*
|--------------------------------------------------------------------------
| 2. AUTH ROUTES (Login & Logout)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| 3. PROTECTED ROUTES (Hanya Bisa Diakses Setelah Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // --- MASTER DATA PRODUK (DAFTAR HARGA) ---
    Route::prefix('daftar-harga')->name('harga.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/tambah', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // --- MENU PENAWARAN ---
    Route::prefix('penawaran')->name('penawaran.')->group(function () {
        // Penawaran PROYEK
        Route::get('/create-project', [ProductController::class, 'createCombined'])->name('create_combined');
        Route::post('/store-project', [ProductController::class, 'storeCombined'])->name('store_combined');

        // Penawaran PRODUK
        Route::get('/create-product', [ProductOfferController::class, 'create'])->name('create_product');
        Route::post('/store-product', [ProductOfferController::class, 'store'])->name('store_product');
        Route::get('/edit-product/{offer}', [ProductOfferController::class, 'edit'])->name('edit_product');
        Route::put('/update-product/{offer}', [ProductOfferController::class, 'update'])->name('update_product');
    });

    // --- HISTORI & DETAIL PENAWARAN ---
    Route::prefix('histori-penawaran')->name('histori.')->group(function () {
        Route::get('/', [OfferController::class, 'index'])->name('index');
        Route::get('/{offer}', [OfferController::class, 'show'])->name('show');
        Route::get('/{offer}/edit', [OfferController::class, 'edit'])->name('edit');
        Route::put('/{offer}', [OfferController::class, 'update'])->name('update');
        Route::delete('/{offer}', [OfferController::class, 'destroy'])->name('destroy');
        Route::get('/{offer}/print', [OfferController::class, 'print'])->name('print');
    });

    // --- INVOICE ---
    Route::prefix('invoice')->name('invoice.')->group(function () {
        Route::get('/histori', [InvoiceController::class, 'index'])->name('histori');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::get('/create-from-offer/{offer}', [InvoiceController::class, 'createFromOffer'])->name('create_from_offer');
        Route::post('/store-from-offer', [InvoiceController::class, 'storeFromOffer'])->name('store_from_offer');
        Route::get('/show/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/edit/{invoice}', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/update/{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/print', [InvoiceController::class, 'print'])->name('print');
    });

    // --- BAST (Berita Acara Serah Terima) ---
    Route::prefix('bast')->name('bast.')->group(function () {
        Route::get('/histori', [BastController::class, 'index'])->name('index');
        Route::get('/show/{bast}', [BastController::class, 'show'])->name('show');
        Route::delete('/{bast}', [BastController::class, 'destroy'])->name('destroy');
        Route::get('/create/{offer}', [BastController::class, 'create'])->name('create');
        Route::post('/store/{offer}', [BastController::class, 'store'])->name('store');
        Route::get('/{id}/print', [BastController::class, 'print'])->name('print');
    });

    //--- Pembuatan REKAP
    Route::get('/recap/create/{offer}', [RecapController::class, 'create'])->name('histori.recap');
    Route::post('/recap/store', [RecapController::class, 'store'])->name('recap.store');
    Route::get('/recap/history', [RecapController::class, 'index'])->name('recap.index');
    Route::get('/recap/show/{id}', [RecapController::class, 'show'])->name('recap.show');
    Route::delete('/recap/delete/{id}', [RecapController::class, 'destroy'])->name('recap.destroy');
    // Route untuk Edit & Update
    Route::get('/recap/{id}/edit', [RecapController::class, 'edit'])->name('recap.edit');
    Route::put('/recap/{id}', [RecapController::class, 'update'])->name('recap.update');

    // Route untuk Delete
    Route::delete('/recap/{id}', [RecapController::class, 'destroy'])->name('recap.destroy');

    Route::get('/recap/export/excel/{id}', [RecapController::class, 'exportExcel'])->name('recap.export.excel');
    Route::get('/recap/export/word/{id}', [RecapController::class, 'exportWord'])->name('recap.export.word');

    // --- SPK (Surat Perintah Kerja) ---
    Route::prefix('spk')->name('skp.')->group(function () {
        Route::get('/histori', [SkpController::class, 'index'])->name('index');
        Route::get('/show/{skp}', [SkpController::class, 'show'])->name('show');
        Route::get('/edit/{skp}', [SkpController::class, 'edit'])->name('edit');
        Route::put('/update/{skp}', [SkpController::class, 'update'])->name('update');
        Route::delete('/{skp}', [SkpController::class, 'destroy'])->name('destroy');
        Route::get('/create/{offer}', [SkpController::class, 'create'])->name('create');
        Route::post('/store/{offer}', [SkpController::class, 'store'])->name('store');
        Route::get('/{id}/print', [SkpController::class, 'print'])->name('print');
    });
});


