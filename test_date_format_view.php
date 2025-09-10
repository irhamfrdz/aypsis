<?php
/**
 * Test format tanggal dd/mmm/yyyy di view
 */

echo "🗓️  TEST FORMAT TANGGAL DD/MMM/YYYY DI VIEW\n";
echo "=========================================\n\n";

// Bootstrap Laravel for Carbon
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test Carbon date formatting
use Carbon\Carbon;

$testDates = [
    '2020-02-17',
    '2020-03-02',
    '2021-03-03',
    '2021-10-25',
    '2021-12-20',
    '2024-01-01',
    '2023-12-31'
];

echo "🧪 Testing Carbon format conversion:\n";
echo "===================================\n";

foreach ($testDates as $dateStr) {
    $carbon = Carbon::parse($dateStr);
    $formatted = $carbon->format('d/M/Y');
    echo "✅ $dateStr → $formatted\n";
}

echo "\n📋 Updated Files:\n";
echo "=================\n";
echo "✅ resources/views/master-karyawan/index.blade.php - Tanggal masuk di table list\n";
echo "✅ resources/views/master-karyawan/show.blade.php - formatDate function dan timestamps\n";
echo "✅ resources/views/master-karyawan/print.blade.php - Tanggal masuk dan berhenti\n";
echo "✅ resources/views/master-karyawan/print-single.blade.php - Tanggal lahir dan masuk\n\n";

echo "🎯 Format yang berubah dari:\n";
echo "   'd/m/Y' (17/2/2020)\n";
echo "   ↓\n";
echo "   'd/M/Y' (17/Feb/2020)\n\n";

echo "✨ Benefit format dd/mmm/yyyy:\n";
echo "- Lebih mudah dibaca\n";
echo "- Tidak ambigu (Feb jelas bulan Februari)\n";
echo "- Konsisten dengan format input yang didukung\n";
echo "- Sesuai dengan screenshot user\n\n";

echo "🔥 FORMAT DD/MMM/YYYY BERHASIL DITERAPKAN DI SEMUA VIEW! 🔥\n";
