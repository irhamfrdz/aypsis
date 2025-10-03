<?php

// Test import dengan format CSV yang diperbaiki

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "=== Testing Fixed Import Logic ===\n\n";

// Simulate the cleanDpeFormatData logic
function getValue($key, $data) {
    if (isset($data[$key])) {
        return $data[$key];
    }

    foreach (array_keys($data) as $dataKey) {
        $cleanKey = preg_replace('/^[\x{FEFF}\x{EF}\x{BB}\x{BF}]+/u', '', $dataKey);
        if ($cleanKey === $key) {
            return $data[$dataKey];
        }
    }

    return '';
}

function cleanSize($size) {
    $size = trim($size);
    if (in_array($size, ['20', '40'])) {
        return $size;
    }
    if (preg_match('/(\d{2})/', $size, $matches)) {
        return $matches[1];
    }
    return '20'; // default
}

function parseDpeDate($date) {
    if (empty($date) || trim($date) === '') {
        return null;
    }

    try {
        $date = trim($date);

        // Format: "21-01-2025"
        if (preg_match('/(\d{1,2})-(\d{1,2})-(\d{4})/', $date, $matches)) {
            return Carbon::createFromDate($matches[3], $matches[2], $matches[1])->format('Y-m-d');
        }

        // Format standard: "2025-01-21"
        return Carbon::parse($date)->format('Y-m-d');
    } catch (Exception $e) {
        return null;
    }
}

function cleanDpeNumber($value) {
    $value = str_replace([',', '.', ' ', 'Rp'], '', $value);
    return (int)$value;
}

// Test data from your CSV
$testRows = [
    ['vendor' => 'DPE', 'nomor_kontainer' => 'CCLU3836629', 'size' => '20', 'group' => '', 'tanggal_awal' => '2025-01-21', 'tanggal_akhir' => '2025-02-20', 'periode' => '1', 'tarif' => 'Bulanan', 'status' => 'Tersedia'],
    ['vendor' => 'DPE', 'nomor_kontainer' => 'DPEU4869769', 'size' => '20', 'group' => '', 'tanggal_awal' => '2025-03-22', 'tanggal_akhir' => '2025-04-08', 'periode' => '3', 'tarif' => 'Harian', 'status' => 'Tersedia'],
    ['vendor' => 'DPE', 'nomor_kontainer' => 'RXTU4540180', 'size' => '40', 'group' => '', 'tanggal_awal' => '2025-03-04', 'tanggal_akhir' => '2025-04-03', 'periode' => '1', 'tarif' => 'Bulanan', 'status' => 'Tersedia'],
];

foreach ($testRows as $index => $data) {
    echo "Row " . ($index + 1) . ":\n";
    echo str_repeat("-", 80) . "\n";

    // Simulate cleanDpeFormatData logic
    $vendor = getValue('vendor', $data) ?: getValue('Vendor', $data) ?: 'DPE';
    $size = cleanSize(getValue('Ukuran', $data) ?: getValue('size', $data));

    // Try to get tarif (harga)
    $tarifValue = getValue('Harga', $data) ?: getValue('harga', $data);

    // If no 'Harga' column, use default
    if (empty($tarifValue) || !is_numeric($tarifValue)) {
        if ($vendor === 'DPE') {
            $tarifValue = ($size == '20') ? 25000 : 35000;
        } else {
            $tarifValue = ($size == '20') ? 20000 : 30000;
        }
        echo "  Tarif not found in CSV, using default: {$tarifValue}\n";
    } else {
        $tarifValue = cleanDpeNumber($tarifValue);
        echo "  Tarif from CSV: {$tarifValue}\n";
    }

    $cleaned = [
        'vendor' => strtoupper(trim($vendor)),
        'nomor_kontainer' => strtoupper(trim(getValue('Kontainer', $data) ?: getValue('nomor_kontainer', $data))),
        'size' => $size,
        'tanggal_awal' => parseDpeDate(getValue('Awal', $data) ?: getValue('tanggal_awal', $data)),
        'tanggal_akhir' => parseDpeDate(getValue('Akhir', $data) ?: getValue('tanggal_akhir', $data)),
        'tarif' => $tarifValue,
        'group' => trim(getValue('Group', $data) ?: getValue('group', $data)),
        'status' => strtolower(getValue('Status', $data) ?: getValue('status', $data) ?: 'ongoing'),
    ];

    // Calculate periode
    $startDate = Carbon::parse($cleaned['tanggal_awal']);
    $endDate = Carbon::parse($cleaned['tanggal_akhir']);
    $cleaned['periode'] = $startDate->diffInDays($endDate) + 1;
    $cleaned['masa'] = $cleaned['periode'] . ' Hari';

    echo "  Cleaned data:\n";
    foreach ($cleaned as $key => $value) {
        echo "    {$key} = " . (is_null($value) ? 'NULL' : "'{$value}'") . "\n";
    }

    // Calculate financial data
    $hari = $cleaned['periode'];
    $tarif = $cleaned['tarif'];

    $dpp = round($hari * $tarif, 2);
    $dpp_nilai_lain = round($dpp * 11/12, 2);
    $ppn = round($dpp * 0.11, 2);
    $grand_total = $dpp + $ppn;

    echo "\n  Financial calculations:\n";
    echo "    Hari: {$hari}\n";
    echo "    Tarif per hari: {$tarif}\n";
    echo "    DPP: {$dpp}\n";
    echo "    DPP Nilai Lain (11/12): {$dpp_nilai_lain}\n";
    echo "    PPN (11%): {$ppn}\n";
    echo "    Grand Total: {$grand_total}\n";

    // Try to save
    echo "\n  Attempting to save to database...\n";
    try {
        DB::beginTransaction();

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
            'group' => $cleaned['group'] ?: null,
            'status_pranota' => null,
            'pranota_id' => null,
        ]);

        echo "  ✓ SUCCESS! Record ID: {$record->id}\n";

        DB::rollBack(); // Rollback untuk testing
        echo "  (Rolled back for testing)\n";

    } catch (Exception $e) {
        DB::rollBack();
        echo "  ✗ ERROR: " . $e->getMessage() . "\n";
    }

    echo "\n\n";
}

echo "=== TEST COMPLETE ===\n";
echo "\nIf all rows show SUCCESS, the fix is working!\n";
echo "Data should now be imported correctly from your CSV format.\n";
