<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\SupplierController;


Route::get('/', function () {
    return redirect('/login');
});
// Rute untuk menampilkan form login dan memproses login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Dashboard
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    // Kategori CRUD
    Route::resource('kategori', KategoriController::class)->except(['create', 'edit']);
    
    // Supplier CRUD
    Route::resource('supplier', SupplierController::class)->except(['create', 'edit']);
    
    // Barang CRUD
    Route::resource('barang', BarangController::class)->except(['create', 'edit']);
    
    // Transaksi (Masuk / Keluar)
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi/masuk', [TransaksiController::class, 'storeMasuk'])->name('transaksi.storeMasuk');
    Route::post('/transaksi/keluar', [TransaksiController::class, 'storeKeluar'])->name('transaksi.storeKeluar');
    Route::put('/transaksi/masuk/{id}', [TransaksiController::class, 'updateMasuk'])->name('transaksi.updateMasuk');
    Route::put('/transaksi/keluar/{id}', [TransaksiController::class, 'updateKeluar'])->name('transaksi.updateKeluar');
    Route::delete('/transaksi/masuk/{id}', [TransaksiController::class, 'destroyMasuk'])->name('transaksi.destroyMasuk');
    Route::delete('/transaksi/keluar/{id}', [TransaksiController::class, 'destroyKeluar'])->name('transaksi.destroyKeluar');

    // Laporan Stok
    Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    
    // Kelola User (CRUD)
    Route::resource('users', App\Http\Controllers\UserController::class)->except(['create', 'edit']);
});