<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;

echo "=== Test Import dengan Tarif TEXT ===\n\n";

// Path ke file CSV
$csvPath = 'C:/Users/amanda/Downloads/template_import_dpe_auto_group.csv';

if (!file_exists($csvPath)) {
    echo "ERROR: File tidak ditemukan: $csvPath\n";
    exit(1);
}

// Simulate the import process
$handle = fopen($csvPath, 'r');
if (!$handle) {
    echo "ERROR: Tidak dapat membaca file\n";
    exit(1);
}

// Detect delimiter
$firstLine = fgets($handle);
rewind($handle);
$delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

echo "Delimiter: '$delimiter'\n\n";

$headers = [];
$rowNumber = 0;
$imported = 0;
$errors = [];

while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
    $rowNumber++;

    try {
        // First row is header
        if ($rowNumber === 1) {
            $headers = array_map(function($header) {
                $cleaned = str_replace("\xEF\xBB\xBF", "", $header);
                $cleaned = preg_replace('/^\x{FEFF}/u', '', $cleaned);
                return trim($cleaned);
            }, $row);

            echo "Headers: " . implode(', ', $headers) . "\n\n";
            continue;
        }

        // Skip empty rows
        if (empty(array_filter($row))) {
            continue;
        }

        // Map row data
        $data = [];
        foreach ($headers as $index => $header) {
            $value = isset($row[$index]) ? trim($row[$index]) : '';
            $value = str_replace("\xEF\xBB\xBF", "", $value);
            $data[$header] = $value;
        }

        // Clean data
        $vendor = strtoupper(trim($data['vendor'] ?? 'DPE'));
        $nomor_kontainer = strtoupper(trim($data['nomor_kontainer'] ?? ''));
        $size = trim($data['size'] ?? '20');
        $tanggal_awal = $data['tanggal_awal'] ?? '';
        $tanggal_akhir = $data['tanggal_akhir'] ?? '';
        $status = strtolower(trim($data['status'] ?? 'ongoing'));
        $group = trim($data['group'] ?? '');

        // Get tarif TEXT from CSV
        $tarifText = trim($data['tarif'] ?? '');
        $tarifType = strtolower($tarifText);

        // Normalize tarif text
        if (in_array($tarifType, ['bulanan', 'monthly'])) {
            $tarifText = 'Bulanan';
        } else if (in_array($tarifType, ['harian', 'daily'])) {
            $tarifText = 'Harian';
        }

        // Map status
        if (in_array($status, ['tersedia', 'available'])) {
            $status = 'ongoing';
        }

        // Parse dates
        $startDate = \Carbon\Carbon::parse($tanggal_awal);
        $endDate = \Carbon\Carbon::parse($tanggal_akhir);

        // Calculate periode (jumlah hari)
        $jumlahHari = $startDate->diffInDays($endDate) + 1;
        $masa = $jumlahHari . ' Hari';

        // Determine tarif_nominal (numeric value)
        $tarifNominal = ($vendor === 'DPE')
            ? (($size == '20') ? 25000 : 35000)
            : (($size == '20') ? 20000 : 30000);

        // Calculate financial data
        $dpp = $tarifNominal * $jumlahHari;
        $ppn = $dpp * 0.11;
        $pph = $dpp * 0.02;
        $grand_total = $dpp + $ppn - $pph;

        echo "Baris $rowNumber: $vendor - $nomor_kontainer ($size ft) - $jumlahHari hari - Tarif: \"$tarifText\" - Nominal: Rp " . number_format($tarifNominal, 0, ',', '.') . "\n";

        // Check if already exists
        $existing = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomor_kontainer)
            ->where('periode', $jumlahHari)
            ->where('tanggal_awal', $startDate->format('Y-m-d'))
            ->first();

        if ($existing) {
            echo "  -> Sudah ada, skip\n";
            continue;
        }

        // Create record with TARIF as TEXT
        DaftarTagihanKontainerSewa::create([
            'vendor' => $vendor,
            'nomor_kontainer' => $nomor_kontainer,
            'size' => $size,
            'tanggal_awal' => $startDate->format('Y-m-d'),
            'tanggal_akhir' => $endDate->format('Y-m-d'),
            'group' => $group ?: null,
            'periode' => $jumlahHari,
            'masa' => $masa,
            'tarif' => $tarifText, // TEXT: "Bulanan" or "Harian"
            'tarif_nominal' => $tarifNominal, // NUMERIC: 25000 or 35000
            'status' => $status,
            'status_pranota' => null,
            'pranota_id' => null,
            'dpp' => $dpp,
            'adjustment' => 0,
            'dpp_nilai_lain' => 0,
            'ppn' => $ppn,
            'pph' => $pph,
            'grand_total' => $grand_total,
        ]);

        $imported++;

    } catch (\Exception $e) {
        $errors[] = [
            'row' => $rowNumber,
            'message' => $e->getMessage(),
            'data' => $row
        ];
        echo "  -> ERROR: " . $e->getMessage() . "\n";
    }
}

fclose($handle);

echo "\n=== Hasil Import ===\n";
echo "Total baris diproses: " . ($rowNumber - 1) . "\n";
echo "Berhasil diimport: $imported\n";
echo "Error: " . count($errors) . "\n";

if (count($errors) > 0) {
    echo "\nDetail Error:\n";
    foreach ($errors as $error) {
        echo "Baris {$error['row']}: {$error['message']}\n";
    }
}

// Show some imported records
echo "\n=== Sample Data yang Diimport (dengan TARIF TEXT) ===\n";
$samples = DaftarTagihanKontainerSewa::orderBy('id', 'desc')->limit(5)->get();
foreach ($samples as $sample) {
    echo "- {$sample->vendor} - {$sample->nomor_kontainer} ({$sample->size}ft)\n";
    echo "  Tarif (TEXT): \"{$sample->tarif}\"\n";
    echo "  Tarif Nominal: Rp " . number_format($sample->tarif_nominal, 0, ',', '.') . "/hari\n";
    echo "  Periode: {$sample->periode} hari\n";
    echo "  DPP: Rp " . number_format($sample->dpp, 0, ',', '.') . "\n\n";
}

echo "Total records in database: " . DaftarTagihanKontainerSewa::count() . "\n";

echo "\n=== Test selesai ===\n";
