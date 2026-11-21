<?php

use App\Http\Controllers\MmoStoreController;
use App\Http\Controllers\MmoServiceController;
use App\Http\Controllers\Admin\MmoDashboardController;
use App\Http\Controllers\Admin\MmoInventoryController;
use Illuminate\Support\Facades\Route;

// Cửa hàng MMO (https://vanhung.xyz)
Route::get('/', [MmoStoreController::class, 'index'])->name('mmo.store.index');
Route::get('/services', [MmoServiceController::class, 'index'])->name('mmo.services.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/products/{product}/purchase', [MmoStoreController::class, 'purchase'])->name('mmo.store.purchase');
    Route::post('/services/orders', [MmoServiceController::class, 'store'])->name('mmo.services.order');
    Route::get('/history/purchases', [MmoStoreController::class, 'purchaseHistory'])->name('mmo.history.purchases');
    Route::get('/history/purchases/{purchase}/download', [MmoStoreController::class, 'downloadTxt'])->name('mmo.history.download');
});

// Khu vực Quản trị (bảo vệ bởi AdminMiddleware)
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [MmoDashboardController::class, 'index'])->name('dashboard');
    Route::post('/service-orders/{order}', [MmoDashboardController::class, 'updateServiceOrder'])->name('service-orders.update');
    Route::get('/inventory/import', [MmoInventoryController::class, 'index'])->name('inventory.import');
    Route::post('/inventory/import', [MmoInventoryController::class, 'bulkImport'])->name('inventory.import.store');
});
