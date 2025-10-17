<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\SuratJalan;

echo "🔍 Checking SuratJalan table structure and data...\n\n";

try {
    // Check table structure
    $columns = DB::select("DESCRIBE surat_jalans");

    echo "📋 Table columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }

    echo "\n� Sample surat jalan data:\n";

    // Get sample data
    $suratJalans = DB::table('surat_jalans')
        ->select('id', 'no_surat_jalan', 'tanggal_surat_jalan', 'pengirim', 'jenis_barang', 'no_kontainer', 'supir', 'supir2')
        ->limit(3)
        ->get();

    foreach ($suratJalans as $sj) {
        echo "\n📄 ID: {$sj->id}\n";
        echo "  no_surat_jalan: " . var_export($sj->no_surat_jalan ?? 'NULL', true) . "\n";
        echo "  tanggal_surat_jalan: " . var_export($sj->tanggal_surat_jalan ?? 'NULL', true) . "\n";
        echo "  pengirim: " . var_export($sj->pengirim ?? 'NULL', true) . "\n";
        echo "  jenis_barang: " . var_export($sj->jenis_barang ?? 'NULL', true) . "\n";
        echo "  no_kontainer: " . var_export($sj->no_kontainer ?? 'NULL', true) . "\n";
        echo "  supir: " . var_export($sj->supir ?? 'NULL', true) . "\n";
        echo "  supir2: " . var_export($sj->supir2 ?? 'NULL', true) . "\n";
    }

    echo "\n🔍 Using Eloquent model:\n";
    $suratJalan = SuratJalan::first();
    if ($suratJalan) {
        echo "📄 Model data:\n";
        echo "  ID: {$suratJalan->id}\n";
        echo "  no_surat_jalan: " . var_export($suratJalan->no_surat_jalan, true) . "\n";
        echo "  pengirim: " . var_export($suratJalan->pengirim, true) . "\n";
        echo "  jenis_barang: " . var_export($suratJalan->jenis_barang, true) . "\n";
        echo "  no_kontainer: " . var_export($suratJalan->no_kontainer, true) . "\n";
        echo "  supir: " . var_export($suratJalan->supir, true) . "\n";
        echo "  supir2: " . var_export($suratJalan->supir2, true) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
