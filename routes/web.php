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
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\PembelianController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'billing.guard'])->group(function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/summary-data', [HomeController::class, 'summaryData'])->name('summary.data');
    Route::get('/chart-data', [HomeController::class, 'chartData'])->name('chart.data');
    Route::get('/transaction-data', [HomeController::class, 'transactionData'])->name('transaction.data');

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

    // Distributor
    Route::middleware('permission:distributor.create')->group(function () {
        Route::get('distributor/create', [DistributorController::class, 'create'])->name('distributor.create');
        Route::post('distributor', [DistributorController::class, 'store'])->name('distributor.store');
    });
    Route::middleware('permission:distributor.view')->group(function () {
        Route::get('distributor/data', [DistributorController::class, 'data'])->name('distributor.data');
        Route::get('distributor', [DistributorController::class, 'index'])->name('distributor.index');
    });
    Route::middleware('permission:distributor.update')->group(function () {
        Route::get('distributor/{distributor}/edit', [DistributorController::class, 'edit'])->name('distributor.edit');
        Route::put('distributor/{distributor}', [DistributorController::class, 'update'])->name('distributor.update');
    });
    Route::middleware('permission:distributor.delete')->delete('distributor/{distributor}', [DistributorController::class, 'destroy'])->name('distributor.destroy');

    // Pembelian (Barang Masuk)
    Route::middleware('permission:pembelian.create')->group(function () {
        Route::get('pembelian/create', [PembelianController::class, 'create'])->name('pembelian.create');
        Route::post('pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
    });
    Route::middleware('permission:pembelian.view')->group(function () {
        Route::get('pembelian/data', [PembelianController::class, 'data'])->name('pembelian.data');
        Route::get('pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
        Route::get('pembelian/{pembelian}', [PembelianController::class, 'show'])->name('pembelian.show');
    });
    Route::middleware('permission:pembelian.update')->post('pembelian/{pembelian}/bayar', [PembelianController::class, 'bayar'])->name('pembelian.bayar');
    Route::middleware('permission:pembelian.delete')->delete('pembelian/{pembelian}', [PembelianController::class, 'destroy'])->name('pembelian.destroy');

    // Opname Stok
    Route::middleware('permission:opname.create')->group(function () {
        Route::get('opname/create', [StockOpnameController::class, 'create'])->name('opname.create');
        Route::post('opname', [StockOpnameController::class, 'store'])->name('opname.store');
        Route::post('opname/{opname}/add-item', [StockOpnameController::class, 'addItem'])->name('opname.addItem');
    });
    Route::middleware('permission:opname.view')->group(function () {
        Route::get('opname/data', [StockOpnameController::class, 'data'])->name('opname.data');
        Route::get('opname', [StockOpnameController::class, 'index'])->name('opname.index');
        Route::get('opname/{opname}', [StockOpnameController::class, 'show'])->name('opname.show');
        Route::get('opname/{opname}/items', [StockOpnameController::class, 'dataItems'])->name('opname.dataItems');
    });
    Route::middleware('permission:opname.update')->group(function () {
        Route::get('opname/{opname}/edit', [StockOpnameController::class, 'edit'])->name('opname.edit');
        Route::put('opname/{opname}', [StockOpnameController::class, 'update'])->name('opname.update');
        Route::put('opname/{opname}/item/{item}', [StockOpnameController::class, 'updateItem'])->name('opname.updateItem');
        Route::delete('opname/{opname}/item/{item}', [StockOpnameController::class, 'removeItem'])->name('opname.removeItem');
        Route::post('opname/{opname}/finish', [StockOpnameController::class, 'finish'])->name('opname.finish');
        Route::post('opname/{opname}/cancel', [StockOpnameController::class, 'cancel'])->name('opname.cancel');
    });
    Route::middleware('permission:opname.approve')->group(function () {
        Route::post('opname/{opname}/approve', [StockOpnameController::class, 'approve'])->name('opname.approve');
        Route::post('opname/{opname}/reject', [StockOpnameController::class, 'reject'])->name('opname.reject');
    });
    Route::middleware('permission:opname.delete')->delete('opname/{opname}', [StockOpnameController::class, 'destroy'])->name('opname.destroy');

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
