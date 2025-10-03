<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;

echo "=== Test Import dengan Periode dari CSV ===\n\n";

$csvPath = 'C:/Users/amanda/Downloads/template_import_dpe_auto_group.csv';

if (!file_exists($csvPath)) {
    echo "ERROR: File tidak ditemukan: $csvPath\n";
    exit(1);
}

$handle = fopen($csvPath, 'r');
if (!$handle) {
    echo "ERROR: Tidak dapat membaca file\n";
    exit(1);
}

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
        if ($rowNumber === 1) {
            $headers = array_map(function($header) {
                $cleaned = str_replace("\xEF\xBB\xBF", "", $header);
                $cleaned = preg_replace('/^\x{FEFF}/u', '', $cleaned);
                return trim($cleaned);
            }, $row);

            echo "Headers: " . implode(', ', $headers) . "\n\n";
            continue;
        }

        if (empty(array_filter($row))) {
            continue;
        }

        $data = [];
        foreach ($headers as $index => $header) {
            $value = isset($row[$index]) ? trim($row[$index]) : '';
            $value = str_replace("\xEF\xBB\xBF", "", $value);
            $data[$header] = $value;
        }

        $vendor = strtoupper(trim($data['vendor'] ?? 'DPE'));
        $nomor_kontainer = strtoupper(trim($data['nomor_kontainer'] ?? ''));
        $size = trim($data['size'] ?? '20');
        $tanggal_awal = $data['tanggal_awal'] ?? '';
        $tanggal_akhir = $data['tanggal_akhir'] ?? '';
        $status = strtolower(trim($data['status'] ?? 'ongoing'));
        $group = trim($data['group'] ?? '');

        // Ambil periode dari CSV (bukan hitung dari tanggal)
        $periode = isset($data['periode']) ? (int)trim($data['periode']) : 0;

        // Get tarif TEXT from CSV
        $tarifText = trim($data['tarif'] ?? '');
        $tarifType = strtolower($tarifText);

        if (in_array($tarifType, ['bulanan', 'monthly'])) {
            $tarifText = 'Bulanan';
        } else if (in_array($tarifType, ['harian', 'daily'])) {
            $tarifText = 'Harian';
        }

        if (in_array($status, ['tersedia', 'available'])) {
            $status = 'ongoing';
        }

        $startDate = \Carbon\Carbon::parse($tanggal_awal);
        $endDate = \Carbon\Carbon::parse($tanggal_akhir);

        // Hitung jumlah hari dari tanggal (untuk informasi)
        $jumlahHari = $startDate->diffInDays($endDate) + 1;

        // Format masa
        $masa = ($periode <= 12) ? "Periode $periode" : "$periode Hari";

        // Calculate tarif untuk perhitungan
        $tarifForCalc = ($vendor === 'DPE')
            ? (($size == '20') ? 25000 : 35000)
            : (($size == '20') ? 20000 : 30000);

        // Calculate financial data menggunakan PERIODE dari CSV
        $dpp = $tarifForCalc * $periode;
        $ppn = $dpp * 0.11;
        $pph = $dpp * 0.02;
        $grand_total = $dpp + $ppn - $pph;

        if ($rowNumber <= 10) {
            echo "Baris $rowNumber: $vendor - $nomor_kontainer ($size ft)\n";
            echo "  Periode CSV: $periode\n";
            echo "  Jumlah Hari (dari tanggal): $jumlahHari hari\n";
            echo "  Tarif: \"$tarifText\"\n";
            echo "  Masa: $masa\n";
            echo "  DPP: Rp " . number_format($dpp, 0, ',', '.') . " (dari periode CSV: $periode)\n\n";
        }

        $existing = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomor_kontainer)
            ->where('periode', $periode)
            ->where('tanggal_awal', $startDate->format('Y-m-d'))
            ->first();

        if ($existing) {
            continue;
        }

        DaftarTagihanKontainerSewa::create([
            'vendor' => $vendor,
            'nomor_kontainer' => $nomor_kontainer,
            'size' => $size,
            'tanggal_awal' => $startDate->format('Y-m-d'),
            'tanggal_akhir' => $endDate->format('Y-m-d'),
            'group' => $group ?: null,
            'periode' => $periode,  // DARI CSV
            'masa' => $masa,
            'tarif' => $tarifText,
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
        echo "  -> ERROR Baris $rowNumber: " . $e->getMessage() . "\n";
    }
}

fclose($handle);

echo "\n=== Hasil Import ===\n";
echo "Total baris diproses: " . ($rowNumber - 1) . "\n";
echo "Berhasil diimport: $imported\n";
echo "Error: " . count($errors) . "\n";

echo "\n=== Sample Data (PERIODE dari CSV) ===\n";
$samples = DaftarTagihanKontainerSewa::orderBy('id', 'asc')->limit(10)->get();
foreach ($samples as $sample) {
    $start = \Carbon\Carbon::parse($sample->tanggal_awal);
    $end = \Carbon\Carbon::parse($sample->tanggal_akhir);
    $actualDays = $start->diffInDays($end) + 1;

    echo "- {$sample->vendor} - {$sample->nomor_kontainer} ({$sample->size}ft)\n";
    echo "  Periode DB: {$sample->periode} ← Dari CSV\n";
    echo "  Jumlah Hari: {$actualDays} hari ← Dari tanggal\n";
    echo "  Masa: {$sample->masa}\n";
    echo "  Tarif: \"{$sample->tarif}\"\n";
    echo "  DPP: Rp " . number_format($sample->dpp, 0, ',', '.') . "\n\n";
}

echo "Total records: " . DaftarTagihanKontainerSewa::count() . "\n";

echo "\n=== Test selesai ===\n";
