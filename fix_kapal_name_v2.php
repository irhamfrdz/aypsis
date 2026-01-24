<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = DB::table('bls')->where('nama_kapal', 'KM SUMBER ABADI')->count();
echo "Found $count records with 'KM SUMBER ABADI'. Updating to 'KM. SUMBER ABADI 178'...\n";

if ($count > 0) {
    try {
        $affected = DB::table('bls')
            ->where('nama_kapal', 'KM SUMBER ABADI')
            ->update(['nama_kapal' => 'KM. SUMBER ABADI 178']);
        echo "Successfully updated $affected records.\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Nothing to update.\n";
}
