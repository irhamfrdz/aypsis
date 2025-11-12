<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PricelistUangJalanBatamImport;

echo "=== Testing Import Pricelist Uang Jalan Batam ===\n\n";

// Path ke file CSV (sesuaikan dengan path file Anda)
$csvPath = 'C:\Users\User\Downloads\template_pricelist_uang_jalan_batam_20251112113532.csv';

if (!file_exists($csvPath)) {
    echo "❌ File tidak ditemukan: {$csvPath}\n";
    echo "Silakan sesuaikan path di script ini.\n";
    exit(1);
}

echo "✓ File ditemukan: {$csvPath}\n";
echo "Memulai import...\n\n";

try {
    $import = new PricelistUangJalanBatamImport();
    Excel::import($import, $csvPath);

    $successCount = $import->getSuccessCount();
    $errorCount = $import->getErrorCount();
    $errors = $import->getErrors();

    echo "\n=== Hasil Import ===\n";
    echo "✅ Berhasil: {$successCount} data\n";
    echo "❌ Gagal: {$errorCount} data\n";

    if (count($errors) > 0) {
        echo "\n=== Errors ===\n";
        foreach ($errors as $index => $error) {
            if ($index < 10) { // Only show first 10 errors
                echo "  • {$error}\n";
            }
        }
        if (count($errors) > 10) {
            echo "  ... dan " . (count($errors) - 10) . " error lainnya\n";
        }
    }

    if ($successCount > 0) {
        echo "\n✅ Import berhasil!\n";
        echo "Total data yang tersimpan: {$successCount}\n";
    } else {
        echo "\n⚠️  Tidak ada data yang berhasil diimport.\n";
        echo "Periksa format file atau error di atas.\n";
    }

} catch (\Exception $e) {
    echo "\n❌ Error saat import:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
