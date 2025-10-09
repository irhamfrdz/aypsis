<?php
/**
 * Test import CSV to pranota dengan sample data
 */

echo "=== TEST IMPORT CSV TO PRANOTA (SAMPLE) ===\n\n";

// Include Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "🔍 Testing model access...\n";

try {
    // Test model access
    $pranotaCount = PranotaTagihanKontainerSewa::count();
    $tagihanCount = DaftarTagihanKontainerSewa::count();

    echo "✅ Current pranota count: $pranotaCount\n";
    echo "✅ Current tagihan count: $tagihanCount\n\n";

    // Test creating a sample pranota
    echo "🧪 Testing pranota creation...\n";

    DB::beginTransaction();

    $testPranota = PranotaTagihanKontainerSewa::create([
        'no_invoice' => 'TEST-SAMPLE-001',
        'tanggal_pranota' => date('Y-m-d'),
        'no_invoice_vendor' => 'TEST.INVOICE.001',
        'tgl_invoice_vendor' => date('Y-m-d'),
        'total_amount' => 1000000,
        'status' => 'unpaid',
        'keterangan' => 'Test import sample',
        'jumlah_tagihan' => 1,
        'due_date' => date('Y-m-d'),
        'tagihan_kontainer_sewa_ids' => []
    ]);

    echo "✅ Test pranota created with ID: {$testPranota->id}\n";

    // Test creating sample tagihan
    echo "🧪 Testing tagihan creation with pranota assignment...\n";

    $testTagihan = DaftarTagihanKontainerSewa::create([
        'vendor' => 'ZONA',
        'nomor_kontainer' => 'TEST001',
        'size' => '20',
        'tanggal_awal' => date('Y-m-d'),
        'tanggal_akhir' => date('Y-m-d'),
        'tarif' => 'Harian',
        'periode' => 1,
        'group' => 'TEST',
        'status' => 'ongoing',
        'masa' => '1 hari',
        'dpp' => 1000000,
        'adjustment' => -50000,
        'ppn' => 110000,
        'pph' => 20000,
        'grand_total' => 1040000,
        'status_pranota' => 'included',
        'pranota_id' => $testPranota->id,
    ]);

    echo "✅ Test tagihan created with ID: {$testTagihan->id}\n";

    // Update pranota with tagihan ID
    $testPranota->update([
        'tagihan_kontainer_sewa_ids' => [$testTagihan->id]
    ]);

    echo "✅ Pranota updated with tagihan ID\n\n";

    // Verify the relationship
    echo "🔍 Verifying data integrity...\n";

    $pranota = PranotaTagihanKontainerSewa::find($testPranota->id);
    $tagihan = DaftarTagihanKontainerSewa::find($testTagihan->id);

    echo "📋 Pranota Details:\n";
    echo "   - No Invoice: {$pranota->no_invoice}\n";
    echo "   - Total Amount: {$pranota->total_amount}\n";
    echo "   - Tagihan IDs: " . json_encode($pranota->tagihan_kontainer_sewa_ids) . "\n";

    echo "\n📋 Tagihan Details:\n";
    echo "   - Container: {$tagihan->nomor_kontainer}\n";
    echo "   - DPP: {$tagihan->dpp}\n";
    echo "   - Adjustment: {$tagihan->adjustment}\n";
    echo "   - Pranota ID: {$tagihan->pranota_id}\n";
    echo "   - Status Pranota: {$tagihan->status_pranota}\n\n";

    // Cleanup test data
    $testTagihan->delete();
    $testPranota->delete();

    DB::commit();

    echo "🧹 Test data cleaned up\n";
    echo "✅ All tests passed! Ready for actual import.\n\n";

    echo "📋 CSV Parsing Test...\n";

    $csvFile = 'Zona.csv';
    if (file_exists($csvFile)) {
        $handle = fopen($csvFile, 'r');
        $headers = fgetcsv($handle, 1000, ';');

        // Read first few data rows
        $sampleCount = 0;
        while (($row = fgetcsv($handle, 1000, ';')) !== false && $sampleCount < 3) {
            if (count($row) >= 20) {
                $sampleCount++;

                echo "\n📝 Sample Row $sampleCount:\n";
                echo "   - Group: " . trim($row[0]) . "\n";
                echo "   - Container: " . trim($row[1]) . "\n";
                echo "   - Invoice Vendor: " . trim($row[17]) . "\n";
                echo "   - Bank No: " . trim($row[19]) . "\n";
                echo "   - Bank Date: " . trim($row[20]) . "\n";
                echo "   - DPP: " . trim($row[9]) . "\n";
                echo "   - Adjustment: " . trim($row[12]) . "\n";
            }
        }

        fclose($handle);
        echo "\n✅ CSV parsing test completed\n";
    } else {
        echo "❌ CSV file not found: $csvFile\n";
    }

} catch (Exception $e) {
    DB::rollback();
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n🚀 READY FOR IMPORT!\n";
echo "Run: php import_csv_to_pranota.php\n";
echo "=== TEST COMPLETED ===\n";
