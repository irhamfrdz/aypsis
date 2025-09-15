<?php
// Test script to verify automatic perbaikan kontainer creation on approval
require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\Permohonan;
use App\Models\PerbaikanKontainer;
use App\Models\Kontainer;
use App\Models\MasterKegiatan;
use App\Models\Checkpoint;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== TESTING AUTOMATIC PERBAIKAN KONTAINER CREATION ===\n\n";

// Test setup
try {
    // Check if PERBAIKAN kegiatan exists
    $perbaikanKegiatan = MasterKegiatan::where('kode_kegiatan', 'PERBAIKAN')->first();
    if (!$perbaikanKegiatan) {
        echo "❌ PERBAIKAN kegiatan not found. Please run: php artisan db:seed --class=MasterKegiatanSeeder\n";
        exit(1);
    }
    echo "✅ PERBAIKAN kegiatan found: {$perbaikanKegiatan->nama_kegiatan}\n";

    // Create test permohonan with PERBAIKAN kegiatan
    $testPermohonan = Permohonan::create([
        'kegiatan' => 'PERBAIKAN',
        'vendor_perusahaan' => 'TEST_VENDOR',
        'status' => 'Selesai',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✅ Test permohonan created with ID: {$testPermohonan->id}\n";

    // Create test kontainer
    $testKontainer = Kontainer::create([
        'nomor_kontainer' => 'TEST' . rand(1000, 9999),
        'size' => '20ft',
        'status' => 'Tersedia',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✅ Test kontainer created with ID: {$testKontainer->id}\n";

    // Attach kontainer to permohonan
    $testPermohonan->kontainers()->attach($testKontainer->id);
    echo "✅ Kontainer attached to permohonan\n";

    // Create test checkpoint
    $checkpointDate = Carbon::now()->subDays(1)->toDateString();
    $testCheckpoint = Checkpoint::create([
        'permohonan_id' => $testPermohonan->id,
        'tanggal_checkpoint' => $checkpointDate,
        'lokasi_checkpoint' => 'Test Location',
        'status_checkpoint' => 'Completed',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✅ Test checkpoint created with date: {$checkpointDate}\n";

    // Test the createPerbaikanKontainer method
    $controller = new \App\Http\Controllers\PenyelesaianController();

    // Use reflection to access protected method
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('createPerbaikanKontainer');
    $method->setAccessible(true);

    echo "\n🔄 Testing createPerbaikanKontainer method...\n";
    $result = $method->invoke($controller, $testPermohonan, $checkpointDate);

    if ($result > 0) {
        echo "✅ Perbaikan kontainer record created successfully!\n";
        echo "📊 Records created: {$result}\n";

        // Verify the record was created
        $perbaikanRecord = PerbaikanKontainer::where('kontainer_id', $testKontainer->id)
            ->whereDate('tanggal_perbaikan', $checkpointDate)
            ->first();

        if ($perbaikanRecord) {
            echo "✅ Verification successful:\n";
            echo "   - Perbaikan ID: {$perbaikanRecord->id}\n";
            echo "   - Kontainer ID: {$perbaikanRecord->kontainer_id}\n";
            echo "   - Tanggal Perbaikan: {$perbaikanRecord->tanggal_perbaikan}\n";
            echo "   - Status: {$perbaikanRecord->status_perbaikan}\n";
            echo "   - Jenis Perbaikan: {$perbaikanRecord->jenis_perbaikan}\n";
        } else {
            echo "❌ Verification failed: Perbaikan record not found\n";
        }
    } else {
        echo "❌ No perbaikan kontainer records were created\n";
    }

    // Cleanup test data
    echo "\n🧹 Cleaning up test data...\n";
    PerbaikanKontainer::where('kontainer_id', $testKontainer->id)->delete();
    Checkpoint::where('permohonan_id', $testPermohonan->id)->delete();
    $testPermohonan->kontainers()->detach();
    $testPermohonan->delete();
    $testKontainer->delete();
    echo "✅ Test data cleaned up\n";

} catch (\Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
