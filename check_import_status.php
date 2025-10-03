<?php

// Check why data is not being saved

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;

echo "=== Checking Why Import Not Saving ===\n\n";

// 1. Check current data count
echo "1. Current data count in database:\n";
$count = DaftarTagihanKontainerSewa::count();
echo "   Total records: {$count}\n\n";

// 2. Check if there were any recent imports
echo "2. Last 5 records in database:\n";
$recent = DaftarTagihanKontainerSewa::orderBy('created_at', 'desc')->take(5)->get();
if ($recent->count() > 0) {
    foreach ($recent as $record) {
        echo "   - ID: {$record->id}, Kontainer: {$record->nomor_kontainer}, Created: {$record->created_at}\n";
    }
} else {
    echo "   No records found\n";
}

echo "\n3. Checking Laravel log for errors...\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -30); // Last 30 lines

    $hasImportLog = false;
    foreach ($lastLines as $line) {
        if (stripos($line, 'import') !== false || stripos($line, 'error') !== false) {
            echo "   " . trim($line) . "\n";
            $hasImportLog = true;
        }
    }

    if (!$hasImportLog) {
        echo "   No import-related logs found in last 30 lines\n";
    }
} else {
    echo "   Log file not found\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "CHECKLIST - Pastikan Ini Sudah Dilakukan:\n";
echo str_repeat("=", 80) . "\n";
echo "1. [ ] Checkbox 'Hanya validasi' TIDAK TERCENTANG\n";
echo "2. [ ] File CSV sudah di-upload dengan benar\n";
echo "3. [ ] Tidak ada error message muncul di browser\n";
echo "4. [ ] Check browser console (F12) untuk error JavaScript\n";
echo "5. [ ] Import button sudah di-klik sampai selesai (tidak cancel di tengah)\n\n";

echo "DEBUGGING STEPS:\n";
echo "1. Coba import ulang via web browser\n";
echo "2. Perhatikan response yang muncul di browser\n";
echo "3. Check browser Network tab (F12 > Network) untuk melihat response dari server\n";
echo "4. Kalau ada error, copy paste error message ke sini\n";
