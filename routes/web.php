<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenggunaController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index']);

    Route::get('barang/data', [BarangController::class, 'data'])->name('barang.data');
    Route::resource('barang', BarangController::class);
    Route::resource('pelanggan', PelangganController::class);
    Route::get('penjualan/data', [PenjualanController::class, 'data'])->name('penjualan.data');
    Route::resource('penjualan', PenjualanController::class);
    Route::resource('pengguna', PenggunaController::class);
    
    Route::post('/laporan/cetak', [PenjualanController::class, 'cetak'])->name('laporan.cetak');
});
