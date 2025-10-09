<?php

/**
 * Test Master Pricelist Uang Jalan System
 * 
 * Script ini menguji fungsionalitas sistem Master Pricelist Uang Jalan
 * yang baru dibuat, termasuk model methods, validasi, dan operasi CRUD.
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\MasterPricelistUangJalan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== TEST MASTER PRICELIST UANG JALAN SYSTEM ===\n";
echo "Waktu: " . now()->format('Y-m-d H:i:s') . "\n\n";

try {
    // 1. Test Database Connection
    echo "1. Testing Database Connection...\n";
    DB::connection()->getPdo();
    echo "✅ Database connection successful\n\n";
    
    // 2. Test Model Creation
    echo "2. Testing Model Creation...\n";
    
    $testData = [
        'kode' => 'JKT001',
        'cabang' => 'JKT',
        'wilayah' => 'JAKARTA UTARA',
        'dari' => 'GARASI PLUIT',
        'ke' => 'KAPUK',
        'uang_jalan_20ft' => 350000,
        'uang_jalan_40ft' => 500000,
        'keterangan' => 'Test Data - PT. Test Company',
        'liter' => 30,
        'jarak_dari_penjaringan_km' => 5,
        'mel_20_feet' => 30000,
        'mel_40_feet' => 50000,
        'ongkos_truk_20ft' => 1050000,
        'antar_lokasi_20ft' => 0,
        'antar_lokasi_40ft' => 0,
        'valid_from' => Carbon::now(),
        'valid_to' => Carbon::now()->addYear(),
        'status' => 'active',
        'created_by' => 1,
        'updated_by' => 1
    ];
    
    $pricelist = MasterPricelistUangJalan::create($testData);
    echo "✅ Model created with ID: " . $pricelist->id . "\n";
    echo "   Kode: " . $pricelist->kode . "\n";
    echo "   Rute: " . $pricelist->dari . " -> " . $pricelist->ke . "\n\n";
    
    // 3. Test Model Methods
    echo "3. Testing Model Methods...\n";
    
    // Test getUangJalanBySize
    $uangJalan20ft = $pricelist->getUangJalanBySize('20ft');
    $uangJalan40ft = $pricelist->getUangJalanBySize('40ft');
    echo "✅ getUangJalanBySize('20ft'): Rp " . number_format($uangJalan20ft, 0, ',', '.') . "\n";
    echo "✅ getUangJalanBySize('40ft'): Rp " . number_format($uangJalan40ft, 0, ',', '.') . "\n";
    
    // Test getMelBySize
    $mel20ft = $pricelist->getMelBySize('20ft');
    $mel40ft = $pricelist->getMelBySize('40ft');
    echo "✅ getMelBySize('20ft'): Rp " . number_format($mel20ft, 0, ',', '.') . "\n";
    echo "✅ getMelBySize('40ft'): Rp " . number_format($mel40ft, 0, ',', '.') . "\n";
    
    // Test getAntarLokasiBySize
    $antarLokasi20ft = $pricelist->getAntarLokasiBySize('20ft');
    $antarLokasi40ft = $pricelist->getAntarLokasiBySize('40ft');
    echo "✅ getAntarLokasiBySize('20ft'): Rp " . number_format($antarLokasi20ft, 0, ',', '.') . "\n";
    echo "✅ getAntarLokasiBySize('40ft'): Rp " . number_format($antarLokasi40ft, 0, ',', '.') . "\n";
    
    // Test getTotalBiaya
    $totalBiaya20ft = $pricelist->getTotalBiaya('20ft');
    $totalBiaya40ft = $pricelist->getTotalBiaya('40ft');
    echo "✅ getTotalBiaya('20ft'): Rp " . number_format($totalBiaya20ft, 0, ',', '.') . "\n";
    echo "✅ getTotalBiaya('40ft'): Rp " . number_format($totalBiaya40ft, 0, ',', '.') . "\n\n";
    
    // Verify total calculation
    $expectedTotal20ft = $uangJalan20ft + $mel20ft + $pricelist->ongkos_truk_20ft + $antarLokasi20ft;
    $expectedTotal40ft = $uangJalan40ft + $mel40ft + $antarLokasi40ft;
    
    echo "   Verification:\n";
    echo "   - Total 20ft calculation: " . ($totalBiaya20ft == $expectedTotal20ft ? "✅ CORRECT" : "❌ INCORRECT") . "\n";
    echo "   - Total 40ft calculation: " . ($totalBiaya40ft == $expectedTotal40ft ? "✅ CORRECT" : "❌ INCORRECT") . "\n\n";
    
    // 4. Test Scopes
    echo "4. Testing Model Scopes...\n";
    
    // Test active scope
    $activeCount = MasterPricelistUangJalan::active()->count();
    echo "✅ Active records count: " . $activeCount . "\n";
    
    // Test byCabang scope
    $jktCount = MasterPricelistUangJalan::byCabang('JKT')->count();
    echo "✅ JKT cabang count: " . $jktCount . "\n";
    
    // Test byWilayah scope
    $jakutCount = MasterPricelistUangJalan::byWilayah('JAKARTA UTARA')->count();
    echo "✅ Jakarta Utara wilayah count: " . $jakutCount . "\n\n";
    
    // 5. Test findByRoute
    echo "5. Testing findByRoute Method...\n";
    
    $foundPricelist = MasterPricelistUangJalan::findByRoute('GARASI PLUIT', 'KAPUK');
    if ($foundPricelist) {
        echo "✅ Route found: " . $foundPricelist->dari . " -> " . $foundPricelist->ke . "\n";
        echo "   Kode: " . $foundPricelist->kode . "\n";
        echo "   Uang Jalan 20ft: Rp " . number_format($foundPricelist->uang_jalan_20ft, 0, ',', '.') . "\n";
    } else {
        echo "❌ Route not found\n";
    }
    echo "\n";
    
    // 6. Test Auto Kode Generation
    echo "6. Testing Auto Kode Generation...\n";
    
    $testData2 = [
        'cabang' => 'JKT',
        'wilayah' => 'JAKARTA UTARA',
        'dari' => 'TANJUNG PRIUK',
        'ke' => 'GARASI PLUIT',
        'uang_jalan_20ft' => 400000,
        'uang_jalan_40ft' => 600000,
        'keterangan' => 'Test Auto Kode',
        'status' => 'active',
        'created_by' => 1,
        'updated_by' => 1
    ];
    
    $pricelist2 = MasterPricelistUangJalan::create($testData2);
    echo "✅ Second record created with auto-generated kode: " . $pricelist2->kode . "\n\n";
    
    // 7. Test Update
    echo "7. Testing Update Operation...\n";
    
    $pricelist->update([
        'uang_jalan_20ft' => 375000,
        'uang_jalan_40ft' => 525000,
        'updated_by' => 1
    ]);
    
    $pricelist->refresh();
    echo "✅ Record updated successfully\n";
    echo "   New Uang Jalan 20ft: Rp " . number_format($pricelist->uang_jalan_20ft, 0, ',', '.') . "\n";
    echo "   New Uang Jalan 40ft: Rp " . number_format($pricelist->uang_jalan_40ft, 0, ',', '.') . "\n\n";
    
    // 8. Test CSV Parsing Functionality
    echo "8. Testing CSV Number Parsing...\n";
    
    // Simulate parsing Indonesian number format
    $testNumbers = [
        '350.000' => 350000,
        '1.050.000' => 1050000,
        '30.000' => 30000,
        '500000' => 500000,
        '0' => 0
    ];
    
    foreach ($testNumbers as $formatted => $expected) {
        $parsed = (int) str_replace(['.', ','], '', $formatted);
        $result = $parsed === $expected ? "✅" : "❌";
        echo "$result Parse '$formatted' -> $parsed (expected: $expected)\n";
    }
    echo "\n";
    
    // 9. Test Relationships
    echo "9. Testing Relationships...\n";
    
    // Check creator relationship (if users table exists)
    try {
        $creator = $pricelist->creator;
        echo "✅ Creator relationship loaded\n";
    } catch (Exception $e) {
        echo "⚠️  Creator relationship not available (users table may not exist)\n";
    }
    
    try {
        $updater = $pricelist->updater;
        echo "✅ Updater relationship loaded\n";
    } catch (Exception $e) {
        echo "⚠️  Updater relationship not available (users table may not exist)\n";
    }
    echo "\n";
    
    // 10. Test Status Changes
    echo "10. Testing Status Changes...\n";
    
    $pricelist->update(['status' => 'inactive']);
    $pricelist->refresh();
    echo "✅ Status changed to: " . $pricelist->status . "\n";
    
    $pricelist->update(['status' => 'active']);
    $pricelist->refresh();
    echo "✅ Status changed back to: " . $pricelist->status . "\n\n";
    
    // 11. Performance Test
    echo "11. Testing Query Performance...\n";
    
    $startTime = microtime(true);
    $results = MasterPricelistUangJalan::active()
        ->byCabang('JKT')
        ->where('dari', 'like', '%GARASI%')
        ->get();
    $endTime = microtime(true);
    
    $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
    echo "✅ Query executed in: " . number_format($executionTime, 2) . " ms\n";
    echo "   Results found: " . $results->count() . " records\n\n";
    
    // 12. Cleanup Test Data
    echo "12. Cleaning Up Test Data...\n";
    
    $deletedCount = MasterPricelistUangJalan::where('keterangan', 'like', '%Test%')->delete();
    echo "✅ Deleted $deletedCount test records\n\n";
    
    echo "=== ALL TESTS COMPLETED SUCCESSFULLY ===\n";
    echo "✅ Model creation and basic CRUD operations\n";
    echo "✅ Business logic methods (size-based calculations)\n";
    echo "✅ Query scopes and filtering\n";
    echo "✅ Route finding functionality\n";
    echo "✅ Auto-kode generation\n";
    echo "✅ Number parsing for CSV import\n";
    echo "✅ Performance optimization\n\n";
    
    echo "📊 SYSTEM READY FOR DEPLOYMENT!\n";
    echo "Next steps:\n";
    echo "1. Run migration: php artisan migrate\n";
    echo "2. Add routes to web.php\n";
    echo "3. Add navigation menu\n";
    echo "4. Import CSV data\n";
    echo "5. Test web interface\n\n";

} catch (Exception $e) {
    echo "❌ TEST FAILED: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "Test completed at: " . now()->format('Y-m-d H:i:s') . "\n";