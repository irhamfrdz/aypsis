<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Database Schema ===\n\n";

$columns = DB::select('DESCRIBE surat_jalans');

foreach ($columns as $col) {
    if (strpos($col->Field, 'tanggal_tanda_terima') !== false) {
        echo "Field: {$col->Field}\n";
        echo "Type: {$col->Type}\n"; 
        echo "Null: {$col->Null}\n";
        echo "Default: {$col->Default}\n";
        echo "Extra: {$col->Extra}\n\n";
    }
}

// Test raw update
echo "=== Testing Raw Database Update ===\n\n";

$suratJalan = \App\Models\SuratJalan::latest()->first();
if ($suratJalan) {
    $currentTime = now();
    echo "Current time: {$currentTime}\n";
    echo "Current time formatted: " . $currentTime->format('Y-m-d H:i:s') . "\n";
    
    // Raw database update
    DB::table('surat_jalans')
        ->where('id', $suratJalan->id)
        ->update(['tanggal_tanda_terima' => $currentTime->format('Y-m-d H:i:s')]);
    
    // Read back
    $updated = \App\Models\SuratJalan::find($suratJalan->id);
    echo "Updated tanggal_tanda_terima: {$updated->tanggal_tanda_terima}\n";
    echo "Type: " . gettype($updated->tanggal_tanda_terima) . "\n";
    echo "Instance of Carbon: " . ($updated->tanggal_tanda_terima instanceof \Carbon\Carbon ? 'Yes' : 'No') . "\n";
    
    if ($updated->tanggal_tanda_terima instanceof \Carbon\Carbon) {
        echo "Formatted: " . $updated->tanggal_tanda_terima->format('Y-m-d H:i:s') . "\n";
    }
}