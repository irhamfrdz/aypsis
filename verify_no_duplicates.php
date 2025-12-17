<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Verifikasi Duplikat Setelah Cleanup ===\n\n";

$duplicates = DB::select("
    SELECT nomor_kontainer, periode, COUNT(*) as count
    FROM daftar_tagihan_kontainer_sewa
    GROUP BY nomor_kontainer, periode
    HAVING COUNT(*) > 1
");

if (count($duplicates) == 0) {
    echo "✅ TIDAK ADA DUPLIKAT!\n";
    echo "Semua kontainer memiliki maksimal 1 tagihan per periode.\n";
} else {
    echo "❌ Masih ada " . count($duplicates) . " duplikat:\n\n";
    foreach ($duplicates as $dup) {
        echo "{$dup->nomor_kontainer} - Periode {$dup->periode}: {$dup->count} records\n";
    }
}
