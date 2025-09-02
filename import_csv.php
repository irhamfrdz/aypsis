<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== IMPORT CSV KE DATABASE ===\n\n";

$csvFile = 'yang_bener8_new.csv';

if (!file_exists($csvFile)) {
    echo "❌ File CSV tidak ditemukan: $csvFile\n";
    exit(1);
}

echo "📂 Membaca file: $csvFile\n";

// Open CSV file
$handle = fopen($csvFile, 'r');
if (!$handle) {
    echo "❌ Tidak bisa membuka file CSV\n";
    exit(1);
}

// Read header
$header = fgetcsv($handle, 0, ';');
echo "📋 Header CSV: " . implode(', ', $header) . "\n\n";

$imported = 0;
$errors = 0;
$batch = [];
$batchSize = 100;

echo "🔄 Memulai import data...\n\n";

while (($row = fgetcsv($handle, 0, ';')) !== false) {
    try {
        // Skip empty rows
        if (empty(array_filter($row))) continue;

        // Map CSV columns to database fields
        $data = array_combine($header, $row);

        // Parse tanggal dengan format dd/mm/yyyy
        $tanggal_awal = null;
        $tanggal_akhir = null;

        if (!empty($data['tanggal_awal'])) {
            $tanggal_awal = Carbon::createFromFormat('d/m/Y', $data['tanggal_awal'])->format('Y-m-d');
        }

        if (!empty($data['tanggal_akhir'])) {
            $tanggal_akhir = Carbon::createFromFormat('d/m/Y', $data['tanggal_akhir'])->format('Y-m-d');
        }

        // Prepare data for database
        $record = [
            'vendor' => $data['vendor'] ?? '',
            'nomor_kontainer' => $data['nomor_kontainer'] ?? '',
            'size' => $data['size'] ?? '',
            'group' => $data['group'] ?? '',
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'periode' => (int)($data['periode'] ?? 1),
            'masa' => $data['masa'] ?? '',
            'tarif' => $data['tarif'] ?? '',
            'dpp' => (float)($data['dpp'] ?? 0),
            'dpp_nilai_lain' => (float)($data['dpp_nilai_lain'] ?? 0),
            'ppn' => (float)($data['ppn'] ?? 0),
            'pph' => (float)($data['pph'] ?? 0),
            'grand_total' => (float)($data['grand_total'] ?? 0),
            'status' => $data['status'] ?? '',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $batch[] = $record;

        // Insert batch when reaches batch size
        if (count($batch) >= $batchSize) {
            DB::table('daftar_tagihan_kontainer_sewa')->insert($batch);
            $imported += count($batch);
            echo "✅ Imported batch: $imported records\n";
            $batch = [];
        }

    } catch (Exception $e) {
        $errors++;
        echo "❌ Error on row " . ($imported + $errors) . ": " . $e->getMessage() . "\n";
        continue;
    }
}

// Insert remaining batch
if (!empty($batch)) {
    DB::table('daftar_tagihan_kontainer_sewa')->insert($batch);
    $imported += count($batch);
    echo "✅ Imported final batch: $imported total records\n";
}

fclose($handle);

echo "\n=== IMPORT SELESAI ===\n";
echo "✅ Total records imported: $imported\n";
echo "❌ Total errors: $errors\n\n";

// Verify import
$totalInDb = DB::table('daftar_tagihan_kontainer_sewa')->count();
echo "📊 Total records in database: $totalInDb\n\n";

// Show periode distribution
echo "📈 DISTRIBUSI PERIODE:\n";
$periodeDistribution = DB::table('daftar_tagihan_kontainer_sewa')
    ->select('periode', DB::raw('COUNT(*) as count'))
    ->groupBy('periode')
    ->orderBy('periode')
    ->get();

foreach ($periodeDistribution as $p) {
    echo "Periode {$p->periode}: {$p->count} records\n";
}

echo "\n✅ Import CSV berhasil!\n";
echo "🎯 Database siap dengan data CSV yang benar\n";
