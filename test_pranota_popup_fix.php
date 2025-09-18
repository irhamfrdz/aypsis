<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\PranotaPerbaikanKontainer;
use App\Models\PerbaikanKontainer;

echo "=== Testing Pranota Perbaikan Kontainer Database Insertion ===\n\n";

try {
    // Get a sample perbaikan kontainer
    $perbaikan = PerbaikanKontainer::first();
    if (!$perbaikan) {
        echo "❌ No perbaikan kontainer found. Creating a test one...\n";

        $perbaikan = PerbaikanKontainer::create([
            'nomor_kontainer' => 'TEST001',
            'vendor_bengkel' => 'Test Vendor',
            'estimasi_kerusakan_kontainer' => 'Test damage',
            'estimasi_biaya_perbaikan' => 100000,
            'status_perbaikan' => 'belum_masuk_pranota',
            'tanggal_masuk' => now()->format('Y-m-d'),
        ]);
        echo "✅ Created test perbaikan kontainer with ID: {$perbaikan->id}\n";
    } else {
        echo "✅ Using existing perbaikan kontainer with ID: {$perbaikan->id}\n";
    }

    // Test data for pranota creation
    $testData = [
        'nomor_pranota' => 'TEST' . date('ymd') . '0001',
        'perbaikan_kontainer_id' => $perbaikan->id,
        'tanggal_pranota' => now()->format('Y-m-d'),
        'deskripsi_pekerjaan' => 'Test repair work',
        'nama_teknisi' => 'Test Technician',
        'estimasi_biaya' => 100000.00,
        'estimasi_waktu' => 0, // integer
        'catatan' => 'Test notes',
        'status' => 'draft', // valid enum value
        'created_by' => 1, // assuming user ID 1 exists
        'updated_by' => 1, // assuming user ID 1 exists
    ];

    echo "\n📝 Test Data:\n";
    foreach ($testData as $key => $value) {
        echo "   - $key: " . ($value ?? 'NULL') . "\n";
    }

    echo "\n🔄 Creating pranota...\n";

    $pranota = PranotaPerbaikanKontainer::create($testData);

    echo "✅ Pranota created successfully!\n";
    echo "   - ID: {$pranota->id}\n";
    echo "   - Nomor Pranota: {$pranota->nomor_pranota}\n";
    echo "   - Status: {$pranota->status}\n";
    echo "   - Estimasi Waktu: {$pranota->estimasi_waktu} (type: " . gettype($pranota->estimasi_waktu) . ")\n";

    // Verify data in database
    echo "\n🔍 Verifying data in database...\n";
    $dbRecord = DB::table('pranota_perbaikan_kontainers')->where('id', $pranota->id)->first();

    if ($dbRecord) {
        echo "✅ Record found in database:\n";
        echo "   - estimasi_waktu: {$dbRecord->estimasi_waktu} (type: " . gettype($dbRecord->estimasi_waktu) . ")\n";
        echo "   - status: {$dbRecord->status}\n";
    } else {
        echo "❌ Record not found in database!\n";
    }

    // Clean up test data
    echo "\n🧹 Cleaning up test data...\n";
    $pranota->delete();
    echo "✅ Test pranota deleted\n";

    if ($perbaikan->nomor_kontainer === 'TEST001') {
        $perbaikan->delete();
        echo "✅ Test perbaikan kontainer deleted\n";
    }

    echo "\n🎉 Test completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}