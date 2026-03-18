<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\BillingSettingController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'billing.guard'])->group(function () {
    Route::get('/', [HomeController::class, 'index']);

    // Setting Aplikasi
    Route::middleware('permission:setting.view')->group(function () {
        Route::get('setting', [SettingController::class, 'index'])->name('setting.index');
        Route::put('setting', [SettingController::class, 'update'])->name('setting.update');
    });

    // Satuan Barang
    Route::middleware('permission:unit.create')->group(function () {
        Route::get('unit/create', [UnitController::class, 'create'])->name('unit.create');
        Route::post('unit', [UnitController::class, 'store'])->name('unit.store');
    });
    Route::middleware('permission:unit.view')->group(function () {
        Route::get('unit/data', [UnitController::class, 'data'])->name('unit.data');
        Route::get('unit', [UnitController::class, 'index'])->name('unit.index');
    });
    Route::middleware('permission:unit.update')->group(function () {
        Route::get('unit/{unit}/edit', [UnitController::class, 'edit'])->name('unit.edit');
        Route::put('unit/{unit}', [UnitController::class, 'update'])->name('unit.update');
    });
    Route::middleware('permission:unit.delete')->delete('unit/{unit}', [UnitController::class, 'destroy'])->name('unit.destroy');

    // Barang
    Route::middleware('permission:barang.create')->group(function () {
        Route::get('barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('barang', [BarangController::class, 'store'])->name('barang.store');
    });
    Route::middleware('permission:barang.view')->group(function () {
        Route::get('barang/data', [BarangController::class, 'data'])->name('barang.data');
        Route::get('barang/products-data', [BarangController::class, 'productsData'])->name('barang.productsData');
        Route::get('barang', [BarangController::class, 'index'])->name('barang.index');
        Route::get('barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
    });
    Route::middleware('permission:barang.update')->group(function () {
        Route::get('barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    });
    Route::middleware('permission:barang.delete')->delete('barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');

    // Pelanggan
    Route::middleware('permission:pelanggan.create')->group(function () {
        Route::get('pelanggan/create', [PelangganController::class, 'create'])->name('pelanggan.create');
        Route::post('pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
    });
    Route::middleware('permission:pelanggan.view')->group(function () {
        Route::get('pelanggan/data', [PelangganController::class, 'data'])->name('pelanggan.data');
        Route::get('pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    });
    Route::middleware('permission:pelanggan.update')->group(function () {
        Route::get('pelanggan/{pelanggan}/edit', [PelangganController::class, 'edit'])->name('pelanggan.edit');
        Route::put('pelanggan/{pelanggan}', [PelangganController::class, 'update'])->name('pelanggan.update');
    });
    Route::middleware('permission:pelanggan.delete')->delete('pelanggan/{pelanggan}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');

    // Cetak Struk & Laporan
    Route::middleware('permission:laporan.view')->get('penjualan/cetak-struk', [PenjualanController::class, 'cetakStruk'])->name('penjualan.cetakStruk');
    Route::middleware('permission:laporan.print')->post('/laporan/cetak', [PenjualanController::class, 'cetak'])->name('laporan.cetak');

    // Penjualan
    Route::middleware('permission:penjualan.create')->group(function () {
        Route::get('penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
        Route::post('penjualan/add-item', [PenjualanController::class, 'addItem'])->name('penjualan.addItem');
        Route::post('penjualan/add-from-modal', [PenjualanController::class, 'addFromModal'])->name('penjualan.addFromModal');
        Route::post('penjualan/update-item', [PenjualanController::class, 'updateItem'])->name('penjualan.updateItem');
        Route::post('penjualan/cancel', [PenjualanController::class, 'cancel'])->name('penjualan.cancel');
        Route::delete('penjualan/remove-item/{id}', [PenjualanController::class, 'removeItem'])->name('penjualan.removeItem');
        Route::post('penjualan/store-detail', [PenjualanController::class, 'storeDetail'])->name('penjualan.storeDetail');
    });
    Route::middleware('permission:penjualan.view')->group(function () {
        Route::get('penjualan/data', [PenjualanController::class, 'data'])->name('penjualan.data');
        Route::get('penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('penjualan/{penjualan}', [PenjualanController::class, 'show'])->name('penjualan.show');
    });
    Route::middleware('permission:penjualan.delete')->delete('penjualan/{penjualan}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');

    // Pengguna (Admin Only)
    Route::middleware('permission:pengguna.create')->group(function () {
        Route::get('pengguna/create', [PenggunaController::class, 'create'])->name('pengguna.create');
        Route::post('pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
    });
    Route::middleware('permission:pengguna.view')->group(function () {
        Route::get('pengguna/data', [PenggunaController::class, 'data'])->name('pengguna.data');
        Route::get('pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
    });
    Route::middleware('permission:pengguna.update')->group(function () {
        Route::get('pengguna/{pengguna}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit');
        Route::put('pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update');
    });
    Route::middleware('permission:pengguna.delete')->delete('pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');

    // Role Management
    Route::middleware('permission:pengguna.view')->group(function () {
        Route::get('role/data', [RoleController::class, 'data'])->name('role.data');
        Route::resource('role', RoleController::class);
    });

    // Menu Management
    Route::middleware('permission:pengguna.view')->group(function () {
        Route::get('menu/data', [MenuController::class, 'data'])->name('menu.data');
        Route::resource('menu', MenuController::class);
    });

    // Billing Settings (Super Admin only via permission)
    Route::middleware('permission:billing.manage')->group(function () {
        Route::get('billing-setting', [BillingSettingController::class, 'index'])->name('billing.index');
        Route::get('billing-setting/data', [BillingSettingController::class, 'data'])->name('billing.data');
        Route::get('billing-setting/create', [BillingSettingController::class, 'create'])->name('billing.create');
        Route::post('billing-setting', [BillingSettingController::class, 'store'])->name('billing.store');
        Route::get('billing-setting/{id}/edit', [BillingSettingController::class, 'editItem'])->name('billing.edit');
        Route::put('billing-setting/{id}', [BillingSettingController::class, 'updateItem'])->name('billing.update');
        Route::patch('billing-setting/{id}/activate', [BillingSettingController::class, 'activate'])->name('billing.activate');
        Route::patch('billing-setting/{id}/status/{status}', [BillingSettingController::class, 'setStatus'])->name('billing.status');
        Route::delete('billing-setting/{id}', [BillingSettingController::class, 'destroy'])->name('billing.destroy');
        // Legacy single-setting paths (optional)
        Route::get('billing-setting/legacy', [BillingSettingController::class, 'edit'])->name('billing.setting');
        Route::put('billing-setting/legacy', [BillingSettingController::class, 'update'])->name('billing.setting.update');
    });
});

// Billing restricted page
Route::middleware('billing.guard')->get('/billing/restricted', [\App\Http\Controllers\BillingController::class, 'restricted'])->name('billing.restricted');
