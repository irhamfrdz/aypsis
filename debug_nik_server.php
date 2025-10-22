<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Karyawan;

echo "=== DEBUG NIK SERVER ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// 1. Check total karyawan count
$totalKaryawan = Karyawan::count();
echo "1. Total Karyawan in database: {$totalKaryawan}\n\n";

// 2. Check all NIKs in the system
echo "2. All NIKs in database:\n";
$allNiks = Karyawan::orderBy('nik')->pluck('nik')->toArray();
if (empty($allNiks)) {
    echo "   No NIKs found in database\n";
} else {
    foreach ($allNiks as $nik) {
        echo "   - {$nik}\n";
    }
}
echo "\n";

// 3. Check NIKs in our target range (1503-9999)
echo "3. NIKs in range 1503-9999:\n";
$niksInRange = Karyawan::whereRaw('nik REGEXP \'^[0-9]+$\'')
                      ->whereRaw('CAST(nik AS UNSIGNED) >= ? AND CAST(nik AS UNSIGNED) <= ?', [1503, 9999])
                      ->orderByRaw('CAST(nik AS UNSIGNED) ASC')
                      ->pluck('nik')
                      ->toArray();

if (empty($niksInRange)) {
    echo "   No NIKs found in range 1503-9999\n";
} else {
    foreach ($niksInRange as $nik) {
        echo "   - {$nik}\n";
    }
}
echo "\n";

// 4. Get highest NIK in range
echo "4. Highest NIK in range 1503-9999:\n";
$highestNik = Karyawan::whereRaw('nik REGEXP \'^[0-9]+$\'')
                     ->whereRaw('CAST(nik AS UNSIGNED) >= ? AND CAST(nik AS UNSIGNED) <= ?', [1503, 9999])
                     ->orderByRaw('CAST(nik AS UNSIGNED) DESC')
                     ->value('nik');

if ($highestNik) {
    echo "   Highest NIK: {$highestNik}\n";
    echo "   Next should be: " . ((int)$highestNik + 1) . "\n";
} else {
    echo "   No NIK found in range, should start from: 1503\n";
}
echo "\n";

// 5. Test generateNextNik method
echo "5. Testing generateNextNik() method:\n";
try {
    $nextNik = Karyawan::generateNextNik();
    echo "   Generated NIK: {$nextNik}\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 6. Check for invalid NIKs (non-numeric or out of range)
echo "6. Invalid NIKs (non-numeric or outside range 1503-9999):\n";
$invalidNiks = Karyawan::where(function($query) {
    $query->whereRaw('nik NOT REGEXP \'^[0-9]+$\'') // Non-numeric
          ->orWhereRaw('CAST(nik AS UNSIGNED) < 1503')  // Below range
          ->orWhereRaw('CAST(nik AS UNSIGNED) > 9999'); // Above range
})->pluck('nik')->toArray();

if (empty($invalidNiks)) {
    echo "   No invalid NIKs found\n";
} else {
    foreach ($invalidNiks as $nik) {
        echo "   - {$nik} (invalid)\n";
    }
}
echo "\n";

// 7. Show sample karyawan data
echo "7. Sample karyawan data (first 5):\n";
$sampleKaryawan = Karyawan::select('nik', 'nama_lengkap', 'created_at')
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();

if ($sampleKaryawan->isEmpty()) {
    echo "   No karyawan data found\n";
} else {
    foreach ($sampleKaryawan as $karyawan) {
        echo "   - NIK: {$karyawan->nik}, Nama: {$karyawan->nama_lengkap}, Created: {$karyawan->created_at}\n";
    }
}
echo "\n";

echo "=== END DEBUG ===\n";