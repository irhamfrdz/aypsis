<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SuratJalan;

echo "=== TEST STATUS PEMBAYARAN WORKFLOW ===\n\n";

// Test 1: Check existing surat jalan statuses
echo "1. Status existing surat jalans:\n";
$existingSuratJalans = SuratJalan::limit(5)->get();
foreach ($existingSuratJalans as $sj) {
    echo "   SJ #{$sj->id}: {$sj->status_pembayaran} (pranota_id: " . ($sj->pranota_surat_jalan_id ?? 'null') . ")\n";
}

// Test 2: Create new surat jalan to check default status
echo "\n2. Creating new surat jalan to test default status...\n";
try {
    $testSuratJalan = SuratJalan::create([
        'nomor_surat_jalan' => 'TEST-' . time(),
        'tanggal_surat_jalan' => now(),
        'pengirim_id' => 1,
        'penerima' => 'Test Penerima',
        'alamat_penerima' => 'Test Alamat',
        'supir' => 'Test Supir',
        'kenek' => 'Test Kenek',
        'no_plat' => 'TEST123',
        'kegiatan' => 'Test',
        'status' => 'draft',
        'created_by' => 1,
    ]);
    
    echo "   New SJ created with ID: {$testSuratJalan->id}\n";
    echo "   Default status_pembayaran: {$testSuratJalan->status_pembayaran}\n";
    
    // Clean up test data
    $testSuratJalan->delete();
    echo "   Test data cleaned up.\n";
    
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

// Test 3: Check enum values
echo "\n3. Available status_pembayaran enum values:\n";
try {
    $enumValues = DB::select("SHOW COLUMNS FROM surat_jalans LIKE 'status_pembayaran'");
    if (!empty($enumValues)) {
        $type = $enumValues[0]->Type;
        echo "   Column type: {$type}\n";
    }
} catch (Exception $e) {
    echo "   Error checking enum: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";