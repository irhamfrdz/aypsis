<?php

// Tambahkan di routes/web.php

// Master Pricelist Uang Jalan Routes
Route::prefix('master-pricelist-uang-jalan')->name('master-pricelist-uang-jalan.')->group(function () {
    Route::get('/', [App\Http\Controllers\MasterPricelistUangJalanController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\MasterPricelistUangJalanController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\MasterPricelistUangJalanController::class, 'store'])->name('store');
    Route::get('/{masterPricelistUangJalan}', [App\Http\Controllers\MasterPricelistUangJalanController::class, 'show'])->name('show');
    Route::get('/{masterPricelistUangJalan}/edit', [App\Http\Controllers\MasterPricelistUangJalanController::class, 'edit'])->name('edit');
    Route::put('/{masterPricelistUangJalan}', [App\Http\Controllers\MasterPricelistUangJalanController::class, 'update'])->name('update');
    Route::delete('/{masterPricelistUangJalan}', [App\Http\Controllers\MasterPricelistUangJalanController::class, 'destroy'])->name('destroy');
    
    // Import & API Routes
    Route::post('/import', [App\Http\Controllers\MasterPricelistUangJalanController::class, 'importCsv'])->name('import');
    Route::get('/api/find-by-route', [App\Http\Controllers\MasterPricelistUangJalanController::class, 'findByRoute'])->name('api.find-by-route');
});