<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\PranotaUangRit;
use App\Models\NomorTerakhir;

echo "=== DETAILED CONTROLLER & MODEL TEST ===\n\n";

try {
    // 1. Cek kondisi awal database
    echo "1. Checking initial database state:\n";
    $pranotaCount = DB::table('pranota_uang_rits')->count();
    $nomorCount = DB::table('nomor_terakhir')->where('modul', 'PUR')->count();
    echo "   - pranota_uang_rits: {$pranotaCount} records\n";
    echo "   - PUR nomor_terakhir: {$nomorCount} records\n";
    
    // 2. Test generateNomorPranota logic secara manual
    echo "\n2. Testing nomor generation logic:\n";
    
    $date = now();
    $bulan = $date->format('m');
    $tahun = $date->format('y');
    echo "   - Current format: PUR-{$bulan}-{$tahun}-XXXXXX\n";
    
    // Cek existing records
    $lastExisting = PranotaUangRit::where('no_pranota', 'like', "PUR-{$bulan}-{$tahun}-%")
        ->orderBy('no_pranota', 'desc')
        ->first();
    
    if ($lastExisting) {
        echo "   - Last existing: {$lastExisting->no_pranota}\n";
    } else {
        echo "   - No existing records found\n";
    }
    
    // 3. Test nomor_terakhir creation/update
    echo "\n3. Testing nomor_terakhir logic:\n";
    
    DB::beginTransaction();
    
    $nomorTerakhir = NomorTerakhir::where('modul', 'PUR')->lockForUpdate()->first();
    
    if (!$nomorTerakhir) {
        echo "   - Creating new nomor_terakhir record\n";
        $nomorTerakhir = NomorTerakhir::create([
            'modul' => 'PUR',
            'nomor_terakhir' => 1,
            'keterangan' => 'Test creation'
        ]);
        $nomorBaru = 1;
    } else {
        echo "   - Updating existing nomor_terakhir: {$nomorTerakhir->nomor_terakhir}\n";
        $nomorBaru = $nomorTerakhir->nomor_terakhir + 1;
        $nomorTerakhir->update(['nomor_terakhir' => $nomorBaru]);
    }
    
    $sequence = str_pad($nomorBaru, 6, '0', STR_PAD_LEFT);
    $nomorPranota = "PUR-{$bulan}-{$tahun}-{$sequence}";
    echo "   - Generated number: {$nomorPranota}\n";
    
    // 4. Check if number already exists
    echo "\n4. Checking for duplicates:\n";
    $exists = PranotaUangRit::where('no_pranota', $nomorPranota)->exists();
    echo "   - Number exists in DB: " . ($exists ? 'YES' : 'NO') . "\n";
    
    if ($exists) {
        echo "   ❌ DUPLICATE DETECTED! This explains the error.\n";
        
        // Let's find all records with this pattern
        $allMatching = PranotaUangRit::where('no_pranota', 'like', "PUR-{$bulan}-{$tahun}-%")->get();
        echo "   - All matching records:\n";
        foreach ($allMatching as $record) {
            echo "     * {$record->no_pranota} (ID: {$record->id})\n";
        }
    } else {
        echo "   ✅ Number is unique, should work\n";
    }
    
    // 5. Test actual PranotaUangRit creation
    echo "\n5. Testing PranotaUangRit creation:\n";
    
    $testData = [
        'no_pranota' => $nomorPranota,
        'tanggal' => now()->format('Y-m-d'),
        'surat_jalan_id' => 999999, // Dummy ID
        'no_surat_jalan' => 'TEST123',
        'supir_nama' => 'TEST SUPIR',
        'kenek_nama' => null,
        'no_plat' => 'B 1234 TEST',
        'uang_rit_supir' => 85000.00,
        'total_rit' => 85000.00,
        'total_uang' => 85000.00,
        'total_hutang' => 0.00,
        'total_tabungan' => 0.00,
        'grand_total_bersih' => 85000.00,
        'keterangan' => 'Test insert',
        'status' => PranotaUangRit::STATUS_DRAFT,
        'created_by' => 1,
    ];
    
    echo "   - Attempting to create with data:\n";
    foreach ($testData as $key => $value) {
        echo "     * {$key}: " . ($value ?? 'NULL') . "\n";
    }
    
    try {
        $pranota = PranotaUangRit::create($testData);
        echo "   ✅ SUCCESS! Created pranota with ID: {$pranota->id}\n";
        
        // Clean up test data
        $pranota->delete();
        echo "   - Test record deleted\n";
        
    } catch (Exception $e) {
        echo "   ❌ FAILED: " . $e->getMessage() . "\n";
        
        // Check if it's a duplicate key error
        if (strpos($e->getMessage(), '1062') !== false) {
            echo "   - This is the duplicate key error we're seeing!\n";
            
            // Let's check what's really in the database
            echo "   - Searching for hidden records...\n";
            $hiddenRecords = DB::select("SELECT * FROM pranota_uang_rits WHERE no_pranota = ?", [$nomorPranota]);
            if (!empty($hiddenRecords)) {
                echo "   - Found hidden record(s):\n";
                foreach ($hiddenRecords as $record) {
                    echo "     * ID: {$record->id}, no_pranota: {$record->no_pranota}\n";
                }
            }
        }
    }
    
    DB::rollBack();
    echo "\n6. Transaction rolled back\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";