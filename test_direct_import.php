<?php

// Direct test of import processing logic

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "=== Direct Import Test (Using Fixed Logic) ===\n\n";

$csvPath = 'C:\\Users\\amanda\\Downloads\\template_import_dpe_auto_group.csv';

if (!file_exists($csvPath)) {
    echo "ERROR: CSV file not found\n";
    exit(1);
}

echo "Processing file: {$csvPath}\n\n";

$imported = 0;
$errors = 0;
$skipped = 0;

try {
    DB::beginTransaction();

    $handle = fopen($csvPath, 'r');
    $headers = [];
    $rowNumber = 0;
    $delimiter = ';';

    // Helper functions
    $getValue = function($key) use (&$data) {
        if (isset($data[$key])) {
            return $data[$key];
        }
        return '';
    };

    $cleanSize = function($size) {
        $size = trim($size);
        if (in_array($size, ['20', '40'])) {
            return $size;
        }
        return '20';
    };

    while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
        $rowNumber++;

        if ($rowNumber === 1) {
            $headers = array_map('trim', $row);
            continue;
        }

        if (empty(array_filter($row))) {
            continue;
        }

        // Map data
        $data = [];
        foreach ($headers as $index => $header) {
            $data[$header] = isset($row[$index]) ? trim($row[$index]) : '';
        }

        try {
            // Clean data
            $vendor = $getValue('vendor') ?: 'DPE';
            $size = $cleanSize($getValue('size'));

            // Get tarif - use default if not numeric
            $tarifValue = $getValue('harga') ?: $getValue('Harga');
            if (empty($tarifValue) || !is_numeric($tarifValue)) {
                $tarifValue = ($vendor === 'DPE')
                    ? (($size == '20') ? 25000 : 35000)
                    : (($size == '20') ? 20000 : 30000);
            }

            $cleaned = [
                'vendor' => strtoupper(trim($vendor)),
                'nomor_kontainer' => strtoupper(trim($getValue('nomor_kontainer'))),
                'size' => $size,
                'tanggal_awal' => $getValue('tanggal_awal'),
                'tanggal_akhir' => $getValue('tanggal_akhir'),
                'tarif' => $tarifValue,
                'group' => trim($getValue('group')) ?: null,
                'status' => strtolower($getValue('status')) ?: 'ongoing',
            ];

            // Validate
            if (empty($cleaned['nomor_kontainer'])) {
                throw new \Exception("Nomor kontainer kosong");
            }

            if (empty($cleaned['tanggal_awal']) || empty($cleaned['tanggal_akhir'])) {
                throw new \Exception("Tanggal awal/akhir kosong");
            }

            // Calculate periode
            $startDate = Carbon::parse($cleaned['tanggal_awal']);
            $endDate = Carbon::parse($cleaned['tanggal_akhir']);
            $cleaned['periode'] = $startDate->diffInDays($endDate) + 1;
            $cleaned['masa'] = $cleaned['periode'] . ' Hari';

            // Calculate financial
            $hari = $cleaned['periode'];
            $tarif = $cleaned['tarif'];

            $dpp = round($hari * $tarif, 2);
            $dpp_nilai_lain = round($dpp * 11/12, 2);
            $ppn = round($dpp * 0.11, 2);
            $grand_total = $dpp + $ppn;

            // Check for duplicates
            $existing = DaftarTagihanKontainerSewa::where('nomor_kontainer', $cleaned['nomor_kontainer'])
                ->where('tanggal_awal', $cleaned['tanggal_awal'])
                ->where('tanggal_akhir', $cleaned['tanggal_akhir'])
                ->first();

            if ($existing) {
                $skipped++;
                if ($rowNumber <= 10) {
                    echo "Row {$rowNumber}: SKIPPED (duplicate) - {$cleaned['nomor_kontainer']}\n";
                }
                continue;
            }

            // Create record
            $record = DaftarTagihanKontainerSewa::create([
                'vendor' => $cleaned['vendor'],
                'nomor_kontainer' => $cleaned['nomor_kontainer'],
                'size' => $cleaned['size'],
                'tanggal_awal' => $cleaned['tanggal_awal'],
                'tanggal_akhir' => $cleaned['tanggal_akhir'],
                'periode' => $cleaned['periode'],
                'masa' => $cleaned['masa'],
                'tarif' => $cleaned['tarif'],
                'hari' => $hari,
                'dpp' => $dpp,
                'dpp_nilai_lain' => $dpp_nilai_lain,
                'adjustment' => 0,
                'ppn' => $ppn,
                'pph' => 0,
                'grand_total' => $grand_total,
                'status' => $cleaned['status'],
                'group' => $cleaned['group'],
                'status_pranota' => null,
                'pranota_id' => null,
            ]);

            $imported++;

            if ($imported <= 10) {
                echo "Row {$rowNumber}: ✓ IMPORTED - {$cleaned['nomor_kontainer']} ({$cleaned['periode']} hari, Rp " . number_format($grand_total) . ")\n";
            }

        } catch (\Exception $e) {
            $errors++;
            if ($errors <= 10) {
                echo "Row {$rowNumber}: ✗ ERROR - " . $e->getMessage() . "\n";
            }
        }
    }

    fclose($handle);

    echo "\n" . str_repeat("=", 80) . "\n";
    echo "SUMMARY:\n";
    echo "  Total rows processed: " . ($rowNumber - 1) . "\n";
    echo "  Imported: {$imported}\n";
    echo "  Skipped (duplicates): {$skipped}\n";
    echo "  Errors: {$errors}\n";
    echo str_repeat("=", 80) . "\n\n";

    if ($errors == 0) {
        echo "✓ ALL DATA PROCESSED SUCCESSFULLY!\n\n";
        echo "Rolling back transaction (this is just a test)...\n";
        DB::rollBack();
        echo "Data NOT saved to database (test mode).\n\n";
        echo "To import for real:\n";
        echo "1. Go to web interface: /daftar-tagihan-kontainer-sewa/import\n";
        echo "2. Upload your CSV file\n";
        echo "3. UNCHECK 'Hanya validasi'\n";
        echo "4. Click 'Import Data'\n";
    } else {
        DB::rollBack();
        echo "✗ There were errors. Please fix them first.\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
