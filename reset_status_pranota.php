<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Reset Status Pranota ===\n\n";

// Get current count
$includedCount = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('status_pranota', 'included')
    ->count();

echo "Total records dengan status 'included': {$includedCount}\n";

if ($includedCount == 0) {
    echo "✅ Tidak ada data yang perlu direset\n";
    exit(0);
}

echo "\n⚠️  PERINGATAN: Script ini akan reset status_pranota menjadi NULL\n";
echo "Apakah Anda yakin? (yes/no): ";

$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 'yes') {
    echo "\n❌ Dibatalkan\n";
    exit(0);
}

echo "\nMemproses reset...\n";

$updated = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('status_pranota', 'included')
    ->update([
        'status_pranota' => null,
        'pranota_id' => null,
        'pranota_tagihan_kontainer_sewa_id' => null,
        'updated_at' => now()
    ]);

echo "\n✅ SELESAI!\n";
echo "Total records direset: {$updated}\n";
echo "\nSekarang Anda bisa import pranota lagi.\n";
