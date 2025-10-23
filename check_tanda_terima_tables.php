<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== STRUKTUR TABEL TANDA_TERIMAS ===\n";
    $columns = DB::select('DESCRIBE tanda_terimas');

    foreach ($columns as $col) {
        echo "{$col->Field} - {$col->Type}\n";
    }

    echo "\n=== STRUKTUR TABEL TANDA_TERIMA_TANPA_SURAT_JALAN ===\n";
    $columns2 = DB::select('DESCRIBE tanda_terima_tanpa_surat_jalan');

    foreach ($columns2 as $col) {
        echo "{$col->Field} - {$col->Type}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
