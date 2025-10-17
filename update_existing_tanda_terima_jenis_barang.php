<?php
/**
 * Update existing Tanda Terima records with jenis_barang from Surat Jalan
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Updating Existing Tanda Terima Records ===\n\n";

$tandaTerimas = DB::table('tanda_terimas')
    ->whereNotNull('surat_jalan_id')
    ->get();

echo "Found {$tandaTerimas->count()} tanda terima records to update\n\n";

$updated = 0;
$skipped = 0;

foreach ($tandaTerimas as $tt) {
    // Get jenis_barang from surat jalan
    $suratJalan = DB::table('surat_jalans')
        ->where('id', $tt->surat_jalan_id)
        ->first();

    if ($suratJalan && $suratJalan->jenis_barang) {
        DB::table('tanda_terimas')
            ->where('id', $tt->id)
            ->update(['jenis_barang' => $suratJalan->jenis_barang]);

        echo "✓ Updated TT {$tt->no_surat_jalan}: {$suratJalan->jenis_barang}\n";
        $updated++;
    } else {
        echo "⊘ Skipped TT {$tt->no_surat_jalan}: No jenis_barang in surat jalan\n";
        $skipped++;
    }
}

echo "\n=== Summary ===\n";
echo "Updated: {$updated} records\n";
echo "Skipped: {$skipped} records\n";
echo "\n✅ Update complete!\n";
