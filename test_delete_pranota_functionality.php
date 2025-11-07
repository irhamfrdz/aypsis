<?php

// Test file to verify delete pranota functionality
// This script tests that when pranota is deleted, the uang jalan status is restored to 'belum_masuk_pranota'

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\PranotaUangJalan;
use App\Models\UangJalan;

echo "\n=== TEST DELETE PRANOTA - RESTORE UANG JALAN STATUS ===\n\n";

try {
    // Create Laravel application instance for testing
    $app = new Application(__DIR__);
    
    // Bootstrap the application
    $app->singleton(
        Illuminate\Contracts\Http\Kernel::class,
        App\Http\Kernel::class
    );
    
    $app->singleton(
        Illuminate\Contracts\Console\Kernel::class,
        App\Console\Kernel::class
    );
    
    $app->singleton(
        Illuminate\Contracts\Debug\ExceptionHandler::class,
        App\Exceptions\Handler::class
    );
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();
    
    echo "✓ Laravel application bootstrapped successfully\n\n";
    
    // Step 1: Find existing uang jalans that are already in pranota status
    $existingUangJalans = UangJalan::where('status', 'sudah_masuk_pranota')
        ->limit(5)
        ->get();
    
    if ($existingUangJalans->isEmpty()) {
        echo "No existing uang jalan with 'sudah_masuk_pranota' status found.\n";
        echo "Creating test uang jalans...\n\n";
        
        // Create test uang jalans
        $testUangJalans = [];
        for ($i = 1; $i <= 3; $i++) {
            $uangJalan = UangJalan::create([
                'nomor_uang_jalan' => 'TEST-DELETE-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'tanggal_uang_jalan' => now()->subDays($i),
                'supir' => 'Test Supir ' . $i,
                'kenek' => 'Test Kenek ' . $i,
                'nomor_polisi' => 'TEST ' . $i,
                'rute_asal' => 'Jakarta',
                'rute_tujuan' => 'Bandung',
                'uang_jalan' => 1000000,
                'uang_makan' => 150000,
                'total_amount' => 1150000,
                'status' => 'belum_masuk_pranota',
                'created_by' => 1
            ]);
            $testUangJalans[] = $uangJalan;
        }
        
        echo "✓ Created " . count($testUangJalans) . " test uang jalans\n\n";
        
        // Step 2: Create a test pranota with these uang jalans
        $pranotaData = [
            'nomor_pranota' => 'TEST-PRANOTA-DELETE-' . time(),
            'tanggal_pranota' => now(),
            'periode_tagihan' => now()->format('Y-m'),
            'jumlah_uang_jalan' => count($testUangJalans),
            'total_amount' => array_sum(array_column($testUangJalans->toArray(), 'total_amount')),
            'status_pembayaran' => 'unpaid',
            'catatan' => 'Test pranota for delete functionality',
            'created_by' => 1,
        ];
        
        $testPranota = PranotaUangJalan::create($pranotaData);
        
        // Attach uang jalans and update their status
        $uangJalanIds = $testUangJalans->pluck('id')->toArray();
        $testPranota->uangJalans()->attach($uangJalanIds);
        UangJalan::whereIn('id', $uangJalanIds)->update(['status' => 'sudah_masuk_pranota']);
        
        echo "✓ Created test pranota: " . $testPranota->nomor_pranota . "\n";
        echo "✓ Updated " . count($uangJalanIds) . " uang jalan status to 'sudah_masuk_pranota'\n\n";
        
    } else {
        // Use existing pranota
        $testPranota = $existingUangJalans->first()->pranotaUangJalans->first();
        if (!$testPranota) {
            echo "❌ Could not find pranota for existing uang jalans\n";
            exit(1);
        }
        echo "✓ Using existing pranota: " . $testPranota->nomor_pranota . "\n\n";
    }
    
    // Step 3: Check current status before deletion
    $beforeDeletion = $testPranota->uangJalans()->get();
    echo "=== BEFORE DELETION ===\n";
    echo "Pranota: " . $testPranota->nomor_pranota . "\n";
    echo "Attached Uang Jalans:\n";
    foreach ($beforeDeletion as $uangJalan) {
        echo "  - ID: {$uangJalan->id}, Nomor: {$uangJalan->nomor_uang_jalan}, Status: {$uangJalan->status}\n";
    }
    echo "Total Uang Jalans: " . $beforeDeletion->count() . "\n\n";
    
    // Step 4: Simulate the delete process (what happens in destroy method)
    DB::beginTransaction();
    try {
        echo "=== SIMULATING DELETION PROCESS ===\n";
        
        // Check if pranota can be deleted (status must be 'unpaid')
        if ($testPranota->status_pembayaran !== 'unpaid') {
            echo "⚠️  Pranota status is '" . $testPranota->status_pembayaran . "', changing to 'unpaid' for test\n";
            $testPranota->update(['status_pembayaran' => 'unpaid']);
        }
        
        // Store uang jalan IDs for checking after deletion
        $uangJalanIds = $testPranota->uangJalans()->pluck('id')->toArray();
        
        // Restore uang jalan status back to 'belum_masuk_pranota'
        echo "Restoring uang jalan status to 'belum_masuk_pranota'...\n";
        $updatedCount = $testPranota->uangJalans()->update(['status' => 'belum_masuk_pranota']);
        echo "✓ Updated {$updatedCount} uang jalan status\n";
        
        // Detach uang jalans
        echo "Detaching uang jalans from pranota...\n";
        $detachedCount = $testPranota->uangJalans()->detach();
        echo "✓ Detached {$detachedCount} uang jalans\n";
        
        // Delete the pranota
        echo "Deleting pranota...\n";
        $deletedPranotaId = $testPranota->id;
        $testPranota->delete();
        echo "✓ Deleted pranota with ID: {$deletedPranotaId}\n\n";
        
        DB::commit();
        
        // Step 5: Verify the results
        echo "=== VERIFICATION AFTER DELETION ===\n";
        
        // Check if pranota is actually deleted
        $deletedPranota = PranotaUangJalan::find($deletedPranotaId);
        if ($deletedPranota) {
            echo "❌ Pranota still exists after deletion!\n";
        } else {
            echo "✓ Pranota successfully deleted\n";
        }
        
        // Check uang jalan status
        $restoredUangJalans = UangJalan::whereIn('id', $uangJalanIds)->get();
        echo "Restored Uang Jalans:\n";
        $allRestored = true;
        foreach ($restoredUangJalans as $uangJalan) {
            $statusSymbol = $uangJalan->status === 'belum_masuk_pranota' ? '✓' : '❌';
            echo "  {$statusSymbol} ID: {$uangJalan->id}, Nomor: {$uangJalan->nomor_uang_jalan}, Status: {$uangJalan->status}\n";
            if ($uangJalan->status !== 'belum_masuk_pranota') {
                $allRestored = false;
            }
        }
        
        echo "\n=== TEST RESULTS ===\n";
        if ($allRestored && !$deletedPranota) {
            echo "✅ SUCCESS: All uang jalan status restored to 'belum_masuk_pranota' and pranota deleted\n";
            echo "✅ These uang jalans are now available for new pranota creation\n";
        } else {
            echo "❌ FAILURE: Status restoration or pranota deletion failed\n";
        }
        
        // Test: Verify they can be selected for new pranota
        echo "\n=== TESTING AVAILABILITY FOR NEW PRANOTA ===\n";
        $availableUangJalans = UangJalan::whereIn('status', ['belum_dibayar', 'belum_masuk_pranota'])
            ->whereIn('id', $uangJalanIds)
            ->count();
        
        echo "Available for new pranota: {$availableUangJalans} / " . count($uangJalanIds) . "\n";
        
        if ($availableUangJalans === count($uangJalanIds)) {
            echo "✅ All restored uang jalans are available for new pranota\n";
        } else {
            echo "❌ Some uang jalans are not available for new pranota\n";
        }
        
    } catch (Exception $e) {
        DB::rollBack();
        echo "❌ Error during deletion process: " . $e->getMessage() . "\n";
        throw $e;
    }
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== TEST COMPLETED ===\n";
?>