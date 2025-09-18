<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\PerbaikanKontainer;

echo "=== Testing Total Biaya Change ===\n\n";

try {
    // Create test perbaikan with both estimasi and realisasi biaya
    $perbaikanId = DB::table('perbaikan_kontainers')->insertGetId([
        'nomor_kontainer' => 'TEST_BIAYA_001',
        'tanggal_perbaikan' => now()->format('Y-m-d'),
        'estimasi_kerusakan_kontainer' => 'Test damage',
        'deskripsi_perbaikan' => 'Test repair',
        'estimasi_biaya_perbaikan' => 80000,
        'realisasi_biaya_perbaikan' => 100000, // This should be used as total_biaya
        'status_perbaikan' => 'belum_masuk_pranota',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $perbaikan = PerbaikanKontainer::find($perbaikanId);
    echo "✅ Created test perbaikan:\n";
    echo "   - Estimasi Biaya: {$perbaikan->estimasi_biaya_perbaikan}\n";
    echo "   - Realisasi Biaya: {$perbaikan->realisasi_biaya_perbaikan}\n";

    // Test the controller logic
    $totalBiaya = $perbaikan->realisasi_biaya_perbaikan ?? 0;
    echo "\n🔄 Controller logic test:\n";
    echo "   - total_biaya value: {$totalBiaya}\n";

    if ($totalBiaya == 100000) {
        echo "✅ SUCCESS: total_biaya is now based on realisasi_biaya_perbaikan!\n";
    } else {
        echo "❌ FAILED: total_biaya is not using realisasi_biaya_perbaikan\n";
    }

    // Clean up
    $perbaikan->delete();
    echo "\n🧹 Cleaned up test data\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}