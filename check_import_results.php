<?php
/**
 * Check hasil import Excel test
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Karyawan;

echo "🔍 Checking Import Test Results\n";
echo "===============================\n";

// Check for test data
$testEmails = ['john.doe@test.com', 'jane.smith@test.com', 'test.xlsx@example.com'];

echo "📊 Current database status:\n";
echo "Total karyawan: " . Karyawan::count() . "\n\n";

echo "🎯 Looking for test data:\n";
foreach ($testEmails as $email) {
    $karyawan = Karyawan::where('email', $email)->first();
    if ($karyawan) {
        echo "✅ Found: {$karyawan->nama_lengkap} ({$email})\n";
        echo "   NIK: {$karyawan->nik}\n";
        echo "   Divisi: {$karyawan->divisi}\n";
        echo "   Created: {$karyawan->created_at}\n\n";
    } else {
        echo "❌ Not found: $email\n";
    }
}

// Show recent imports (last 10)
echo "📋 Recent karyawan (last 10):\n";
$recent = Karyawan::orderBy('created_at', 'desc')->take(10)->get();
foreach ($recent as $k) {
    echo "- {$k->nama_lengkap} ({$k->email}) - {$k->created_at}\n";
}

echo "\n🧪 Test Files Status:\n";
$testFiles = [
    'test_karyawan_excel_import.csv',
    'test_simple.xlsx'
];

foreach ($testFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file (" . filesize($file) . " bytes)\n";
    } else {
        echo "❌ $file (not found)\n";
    }
}
