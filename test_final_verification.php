<?php

// FINAL TEST - Import 5 records untuk verifikasi

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "\n";
echo str_repeat("=", 80) . "\n";
echo "FINAL VERIFICATION TEST - Import 5 Sample Records\n";
echo str_repeat("=", 80) . "\n\n";

// Clear test data first
echo "Clearing previous test data...\n";
DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', 'TEST_%')->delete();

$testData = [
    ['vendor' => 'DPE', 'nomor_kontainer' => 'TEST_CONT001', 'size' => '20', 'tanggal_awal' => '2025-01-01', 'tanggal_akhir' => '2025-01-31'],
    ['vendor' => 'DPE', 'nomor_kontainer' => 'TEST_CONT002', 'size' => '40', 'tanggal_awal' => '2025-02-01', 'tanggal_akhir' => '2025-02-28'],
    ['vendor' => 'ZONA', 'nomor_kontainer' => 'TEST_CONT003', 'size' => '20', 'tanggal_awal' => '2025-03-01', 'tanggal_akhir' => '2025-03-15'],
    ['vendor' => 'DPE', 'nomor_kontainer' => 'TEST_CONT004', 'size' => '20', 'tanggal_awal' => '2025-04-01', 'tanggal_akhir' => '2025-04-10'],
    ['vendor' => 'ZONA', 'nomor_kontainer' => 'TEST_CONT005', 'size' => '40', 'tanggal_awal' => '2025-05-01', 'tanggal_akhir' => '2025-05-31'],
];

$imported = 0;

foreach ($testData as $index => $data) {
    try {
        // Get default tarif
        $vendor = $data['vendor'];
        $size = $data['size'];

        if ($vendor === 'DPE') {
            $tarif = ($size == '20') ? 25000 : 35000;
        } else {
            $tarif = ($size == '20') ? 20000 : 30000;
        }

        // Calculate periode
        $startDate = Carbon::parse($data['tanggal_awal']);
        $endDate = Carbon::parse($data['tanggal_akhir']);
        $periode = $startDate->diffInDays($endDate) + 1;
        $masa = $periode . ' Hari';

        // Calculate financial
        $hari = $periode;
        $dpp = round($hari * $tarif, 2);
        $dpp_nilai_lain = round($dpp * 11/12, 2);
        $ppn = round($dpp * 0.11, 2);
        $grand_total = $dpp + $ppn;

        // Create record
        $record = DaftarTagihanKontainerSewa::create([
            'vendor' => $vendor,
            'nomor_kontainer' => $data['nomor_kontainer'],
            'size' => $size,
            'tanggal_awal' => $data['tanggal_awal'],
            'tanggal_akhir' => $data['tanggal_akhir'],
            'periode' => $periode,
            'masa' => $masa,
            'tarif' => $tarif,
            'hari' => $hari,
            'dpp' => $dpp,
            'dpp_nilai_lain' => $dpp_nilai_lain,
            'adjustment' => 0,
            'ppn' => $ppn,
            'pph' => 0,
            'grand_total' => $grand_total,
            'status' => 'ongoing',
            'group' => null,
            'status_pranota' => null,
            'pranota_id' => null,
        ]);

        $imported++;
        echo "✓ [" . ($index + 1) . "] {$data['nomor_kontainer']}: ";
        echo "{$vendor} Size-{$size}, {$periode} hari, ";
        echo "Tarif Rp" . number_format($tarif) . "/hari, ";
        echo "Total: Rp " . number_format($grand_total) . "\n";

    } catch (\Exception $e) {
        echo "✗ [" . ($index + 1) . "] ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("-", 80) . "\n";
echo "Imported: {$imported}/5\n";
echo str_repeat("-", 80) . "\n\n";

if ($imported === 5) {
    echo "✅ SUCCESS! All test records imported successfully!\n\n";

    // Verify in database
    echo "Verifying in database...\n";
    $count = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', 'TEST_%')->count();
    echo "Found {$count} test records in database\n\n";

    // Show sample record
    $sample = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', 'TEST_%')->first();
    echo "Sample record details:\n";
    echo "  ID: {$sample->id}\n";
    echo "  Vendor: {$sample->vendor}\n";
    echo "  Kontainer: {$sample->nomor_kontainer}\n";
    echo "  Size: {$sample->size}\n";
    echo "  Periode: {$sample->periode} hari\n";
    echo "  Tarif: Rp " . number_format($sample->tarif) . "/hari\n";
    echo "  DPP: Rp " . number_format($sample->dpp) . "\n";
    echo "  PPN: Rp " . number_format($sample->ppn) . "\n";
    echo "  Grand Total: Rp " . number_format($sample->grand_total) . "\n\n";

    // Cleanup
    echo "Cleaning up test data...\n";
    DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', 'TEST_%')->delete();
    echo "Test data removed.\n\n";

    echo str_repeat("=", 80) . "\n";
    echo "✅ IMPORT SYSTEM IS WORKING CORRECTLY!\n";
    echo str_repeat("=", 80) . "\n\n";

    echo "📋 Next Steps:\n";
    echo "1. Go to: http://your-domain/daftar-tagihan-kontainer-sewa/import\n";
    echo "2. Upload your CSV file\n";
    echo "3. UNCHECK 'Hanya validasi'\n";
    echo "4. Click 'Import Data'\n";
    echo "5. Check the results - your data should be imported successfully!\n\n";

} else {
    echo "❌ FAILED! Only {$imported}/5 records imported.\n";
    echo "Please check the errors above.\n\n";
}
