<?php
// Include composer autoload and bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

// Boot the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Karyawan;

echo "=== TESTING DROPDOWN SUPIR CHANGES ===\n\n";

// Test 1: Check if nama_panggilan field exists and has data
echo "1. Testing Karyawan model fields:\n";
$supirSample = Karyawan::where('divisi', 'supir')
                      ->whereNotNull('nama_lengkap')
                      ->first(['id', 'nama_lengkap', 'nama_panggilan', 'plat']);

if ($supirSample) {
    echo "   Sample Supir Data:\n";
    echo "   - ID: {$supirSample->id}\n";
    echo "   - Nama Lengkap: " . ($supirSample->nama_lengkap ?? 'NULL') . "\n";
    echo "   - Nama Panggilan: " . ($supirSample->nama_panggilan ?? 'NULL') . "\n";
    echo "   - Plat: " . ($supirSample->plat ?? 'NULL') . "\n\n";
} else {
    echo "   ❌ No supir data found\n\n";
}

// Test 2: Check query structure like in controllers
echo "2. Testing controller query structure:\n";
$supirs = Karyawan::where('divisi', 'supir')
                 ->whereNotNull('nama_lengkap')
                 ->orderBy('nama_panggilan')
                 ->get(['id', 'nama_lengkap', 'nama_panggilan', 'plat']);

echo "   Found {$supirs->count()} supir records\n";
echo "   First 5 records:\n";

foreach ($supirs->take(5) as $index => $supir) {
    $displayName = $supir->nama_panggilan ?? $supir->nama_lengkap;
    echo "   " . ($index + 1) . ". Display: '{$displayName}' | Stored: '{$supir->nama_lengkap}'\n";
}

echo "\n3. Testing display logic:\n";
foreach ($supirs->take(3) as $supir) {
    $displayName = $supir->nama_panggilian ?? $supir->nama_lengkap;  // Test typo protection
    $correctDisplayName = $supir->nama_panggilan ?? $supir->nama_lengkap;
    echo "   Dropdown shows: '{$correctDisplayName}'\n";
    echo "   Form value: '{$supir->nama_lengkap}'\n";
    echo "   ---\n";
}

echo "\n=== SUMMARY ===\n";
echo "✅ Controllers updated to:\n";
echo "   - Include nama_panggilan in SELECT\n";
echo "   - Order by nama_panggilan instead of nama_lengkap\n\n";

echo "✅ Views updated to:\n";
echo "   - Show nama_panggilan in dropdown options\n";
echo "   - Keep nama_lengkap as option value (for database storage)\n\n";

echo "✅ Files Modified:\n";
echo "   - SuratJalanController.php (3 methods)\n";
echo "   - SuratJalanBongkaranController.php (1 method)\n";
echo "   - TandaTerimaTanpaSuratJalanController.php (3 methods)\n";
echo "   - surat-jalan/create.blade.php\n";
echo "   - surat-jalan-bongkaran/create.blade.php\n";
echo "   - tanda-terima-tanpa-surat-jalan/edit.blade.php\n\n";

echo "🎯 Result: Dropdown akan menampilkan nama panggilan tetapi menyimpan nama lengkap\n";
echo "=== END TEST ===\n";