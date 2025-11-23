<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\NomorTerakhir;
use Illuminate\Support\Facades\DB;

echo "Testing invoice number generation...\n";
echo "====================================\n\n";

try {
    DB::transaction(function () {
        // Get or create nomor_terakhir record for MS module
        $nomorTerakhir = NomorTerakhir::where('modul', 'MS')->lockForUpdate()->first();
        
        if (!$nomorTerakhir) {
            echo "Creating new MS record...\n";
            $nomorTerakhir = NomorTerakhir::create([
                'modul' => 'MS',
                'nomor_terakhir' => 1,
                'keterangan' => 'Nomor Invoice Vendor Kontainer Sewa'
            ]);
            $runningNumber = 1;
        } else {
            echo "Found existing MS record, current number: {$nomorTerakhir->nomor_terakhir}\n";
            $runningNumber = $nomorTerakhir->nomor_terakhir + 1;
            $nomorTerakhir->update(['nomor_terakhir' => $runningNumber]);
        }

        // Format: MS-MMYY-0000001
        $month = date('m'); // 2 digit month
        $year = date('y');  // 2 digit year
        $invoiceNumber = sprintf('MS-%s%s-%07d', $month, $year, $runningNumber);

        echo "\nGenerated invoice number: {$invoiceNumber}\n";
        echo "Running number: {$runningNumber}\n";
        echo "Month: {$month}, Year: {$year}\n";
        
        // Rollback for testing
        throw new Exception("Test complete - rolling back");
    });
} catch (Exception $e) {
    if ($e->getMessage() !== "Test complete - rolling back") {
        echo "\n❌ Error: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    } else {
        echo "\n✅ Test completed successfully (transaction rolled back)\n";
    }
}
