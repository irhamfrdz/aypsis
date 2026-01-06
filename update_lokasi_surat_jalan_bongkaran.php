<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $updated = DB::table('surat_jalan_bongkarans')
        ->whereNull('lokasi')
        ->update(['lokasi' => 'Jakarta']);
    
    echo "Berhasil update $updated record surat jalan bongkaran menjadi lokasi Jakarta\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
