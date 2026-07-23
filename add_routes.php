<?php

$file = 'routes/web.php';
$content = file_get_contents($file);

$newRoutes = <<<PHP
    Route::get('bl/rekap-bongkaran-kontainer/select', [\App\Http\Controllers\BlController::class, 'rekapBongkaranKontainerSelect'])->name('bl.rekap-bongkaran-kontainer.select')
        ->middleware('can:bl-view');

    Route::get('bl/rekap-bongkaran-kontainer', [\App\Http\Controllers\BlController::class, 'rekapBongkaranKontainer'])->name('bl.rekap-bongkaran-kontainer')
        ->middleware('can:bl-view');

    Route::get('bl/rekap-bongkaran-kontainer/print', [\App\Http\Controllers\BlController::class, 'rekapBongkaranKontainerPrint'])->name('bl.rekap-bongkaran-kontainer.print')
        ->middleware('can:bl-view');

PHP;

$content = str_replace(
    "Route::get('bl/rekap-bongkaran/select'", 
    $newRoutes . "    Route::get('bl/rekap-bongkaran/select'", 
    $content
);

file_put_contents($file, $content);
echo "Added rekap-bongkaran-kontainer routes to web.php.";
