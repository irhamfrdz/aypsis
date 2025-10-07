<?php

// Test Import Kontainer
// File: test_kontainer_import_manual.php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Kontainer;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST IMPORT KONTAINER ===\n\n";

// 1. Test CSV file exists
$csvFile = 'test_kontainer_import.csv';
if (!file_exists($csvFile)) {
    echo "ERROR: File CSV test tidak ditemukan!\n";
    exit(1);
}

echo "1. File CSV test ditemukan: {$csvFile}\n";

// 2. Read and parse CSV
$handle = fopen($csvFile, 'r');
if (!$handle) {
    echo "ERROR: Tidak bisa membuka file CSV!\n";
    exit(1);
}

$header = fgetcsv($handle, 1000, ';');
echo "2. Header CSV: " . implode(', ', $header) . "\n";

// 3. Validate header
$expectedHeader = ['Awalan Kontainer', 'Nomor Seri', 'Akhiran', 'Ukuran', 'Vendor'];
if ($header !== $expectedHeader) {
    echo "ERROR: Header tidak sesuai!\n";
    echo "   Expected: " . implode(', ', $expectedHeader) . "\n";
    echo "   Actual: " . implode(', ', $header) . "\n";
    exit(1);
}

echo "3. Header valid ✓\n";

// 4. Process data
$importedCount = 0;
$errors = [];

DB::beginTransaction();

try {
    while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
        if (count($data) !== 5) {
            $errors[] = "Baris dengan data tidak lengkap: " . implode(';', $data);
            continue;
        }
        
        [$awalan, $seri, $akhiran, $ukuran, $vendor] = $data;
        
        // Validate data
        if (strlen($awalan) !== 4) {
            $errors[] = "Awalan kontainer harus 4 karakter: {$awalan}";
            continue;
        }
        
        if (strlen($seri) !== 6) {
            $errors[] = "Nomor seri harus 6 karakter: {$seri}";
            continue;
        }
        
        if (strlen($akhiran) !== 1) {
            $errors[] = "Akhiran harus 1 karakter: {$akhiran}";
            continue;
        }
        
        $nomorGabungan = $awalan . $seri . $akhiran;
        
        // Check if already exists
        if (Kontainer::where('nomor_seri_gabungan', $nomorGabungan)->exists()) {
            $errors[] = "Nomor kontainer sudah ada: {$nomorGabungan}";
            continue;
        }
        
        // Create kontainer
        $kontainer = Kontainer::create([
            'awalan_kontainer' => $awalan,
            'nomor_seri_kontainer' => $seri,
            'akhiran_kontainer' => $akhiran,
            'nomor_seri_gabungan' => $nomorGabungan,
            'ukuran' => $ukuran,
            'tipe_kontainer' => 'Dry Container', // Default value
            'vendor' => $vendor,
            'status' => 'Tersedia'
        ]);
        
        $currentNumber = $importedCount + 1;
        echo "4.{$currentNumber}. Import berhasil: {$nomorGabungan} - {$ukuran}ft - {$vendor}\n";
        $importedCount++;
    }
    
    DB::commit();
    echo "\n=== HASIL IMPORT ===\n";
    echo "Total data berhasil diimport: {$importedCount}\n";
    
    if (!empty($errors)) {
        echo "Error yang terjadi:\n";
        foreach ($errors as $error) {
            echo "- {$error}\n";
        }
    }
    
} catch (Exception $e) {
    DB::rollback();
    echo "ERROR saat import: " . $e->getMessage() . "\n";
}

fclose($handle);

// 5. Verify hasil import
echo "\n=== VERIFIKASI HASIL ===\n";
$totalKontainer = Kontainer::count();
echo "Total kontainer di database: {$totalKontainer}\n";

if ($totalKontainer > 0) {
    echo "Data kontainer yang berhasil diimport:\n";
    $kontainers = Kontainer::orderBy('created_at', 'desc')->take(5)->get();
    foreach ($kontainers as $k) {
        echo "- {$k->nomor_seri_gabungan} | {$k->ukuran}ft | {$k->vendor} | {$k->status}\n";
    }
}

echo "\n=== TEST SELESAI ===\n";