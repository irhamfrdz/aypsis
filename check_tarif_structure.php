<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;

echo "=== Checking Current Data Structure ===\n\n";

// Get sample record
$sample = DaftarTagihanKontainerSewa::first();

if ($sample) {
    echo "Sample Record (ID: {$sample->id}):\n";
    echo "  vendor: {$sample->vendor}\n";
    echo "  nomor_kontainer: {$sample->nomor_kontainer}\n";
    echo "  size: {$sample->size}\n";
    echo "  tarif: {$sample->tarif} (Type: " . gettype($sample->tarif) . ")\n";

    // Check if tarif_nominal exists
    if (isset($sample->tarif_nominal)) {
        echo "  tarif_nominal: {$sample->tarif_nominal}\n";
    } else {
        echo "  tarif_nominal: COLUMN NOT EXISTS\n";
    }

    echo "  periode: {$sample->periode}\n";
    echo "  dpp: {$sample->dpp}\n";
}

// Check table structure
echo "\n=== Table Columns ===\n";
$columns = DB::getSchemaBuilder()->getColumnListing('daftar_tagihan_kontainer_sewa');
echo implode(', ', $columns) . "\n";

// Check if tarif column is numeric or text
echo "\n=== Column Details ===\n";
$columnType = DB::select("SHOW COLUMNS FROM daftar_tagihan_kontainer_sewa WHERE Field = 'tarif'");
if (!empty($columnType)) {
    $col = $columnType[0];
    echo "tarif column:\n";
    echo "  Type: {$col->Type}\n";
    echo "  Null: {$col->Null}\n";
    echo "  Default: {$col->Default}\n";
}

// Check if tarif_nominal exists
$columnTypeNominal = DB::select("SHOW COLUMNS FROM daftar_tagihan_kontainer_sewa WHERE Field = 'tarif_nominal'");
if (!empty($columnTypeNominal)) {
    $col = $columnTypeNominal[0];
    echo "\ntarif_nominal column:\n";
    echo "  Type: {$col->Type}\n";
    echo "  Null: {$col->Null}\n";
    echo "  Default: {$col->Default}\n";
} else {
    echo "\ntarif_nominal: COLUMN DOES NOT EXIST\n";
}

echo "\n=== Selesai ===\n";
