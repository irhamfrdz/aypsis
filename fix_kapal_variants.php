<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Updating KM. SUMBER ABADI variants to 'KM. SUMBER ABADI 178'...\n";

// Target variants: 181 to 232
$count = 0;
try {
    // Gunakan REGEX untuk mencocokkan pola KM. SUMBER ABADI <angka>
    // Atau loop range angka jika regex tidak disupport DB tertentu
    
    // Opsi aman: Update semua yang mengandung 'KM. SUMBER ABADI' tapi BUKAN 'KM. SUMBER ABADI 178'
    $query = DB::table('bls')
        ->where('nama_kapal', 'like', 'KM. SUMBER ABADI %')
        ->where('nama_kapal', '!=', 'KM. SUMBER ABADI 178');
        
    $tosUpdate = $query->count();
    
    if ($tosUpdate > 0) {
        echo "Found $tosUpdate records to update.\n";
        
        $affected = $query->update(['nama_kapal' => 'KM. SUMBER ABADI 178']);
        echo "Successfully updated $affected records.\n";
    } else {
        echo "No variant records found to update.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
