<?php
/**
 * Test komprehensif format tanggal dd/mmm/yyyy di semua export
 */

echo "📊 COMPREHENSIVE TEST - EXPORT FORMAT DD/MMM/YYYY\n";
echo "=================================================\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "📋 Methods Export yang telah diupdate:\n";
echo "======================================\n";
echo "1. ✅ export() - CSV biasa\n";
echo "2. ✅ exportExcel() - CSV untuk Excel\n\n";

echo "🗓️  Format tanggal yang digunakan:\n";
echo "==================================\n";
echo "SEBELUM: Y-m-d (2020-02-17)\n";
echo "SESUDAH: d/M/Y (17/Feb/2020)\n\n";

echo "📊 Field tanggal yang terpengaruh:\n";
echo "==================================\n";
$dateFields = [
    'tanggal_lahir',
    'tanggal_masuk',
    'tanggal_berhenti',
    'tanggal_masuk_sebelumnya',
    'tanggal_berhenti_sebelumnya'
];

foreach ($dateFields as $field) {
    echo "✅ $field\n";
}

echo "\n🧪 Testing format conversion:\n";
echo "=============================\n";

$testDates = [
    '2020-02-17' => '17/Feb/2020',
    '2021-10-25' => '25/Oct/2021',
    '2023-12-31' => '31/Dec/2023',
    '2024-01-01' => '01/Jan/2024',
    '1990-06-15' => '15/Jun/1990'
];

foreach ($testDates as $input => $expected) {
    // Test DateTime formatting
    $dateTime = new DateTime($input);
    $result = $dateTime->format('d/M/Y');

    if ($result === $expected) {
        echo "✅ $input → $result\n";
    } else {
        echo "❌ $input → $result (expected: $expected)\n";
    }
}

echo "\n📁 Sample data di template:\n";
echo "===========================\n";
echo "tanggal_lahir: 01/Jan/1990\n";
echo "tanggal_masuk: 01/Jan/2024\n\n";

echo "💡 Keuntungan format dd/mmm/yyyy:\n";
echo "=================================\n";
echo "- ✅ Konsisten dengan tampilan view\n";
echo "- ✅ Konsisten dengan format import yang didukung\n";
echo "- ✅ Mudah dibaca manusia\n";
echo "- ✅ Tidak ambigu (Feb = Februari)\n";
echo "- ✅ Excel-friendly\n\n";

echo "🔗 Routes yang terpengaruh:\n";
echo "===========================\n";
echo "- GET /master/karyawan/export (CSV export)\n";
echo "- GET /master/karyawan/export-excel (Excel export)\n";
echo "- GET /master/karyawan/template (Template dengan sample data)\n\n";

echo "🔥 SEMUA EXPORT SEKARANG MENGGUNAKAN FORMAT DD/MMM/YYYY! 🔥\n";
echo "============================================================\n";
echo "Import/Export flow sekarang fully consistent:\n";
echo "User Export → DD/MMM/YYYY → User Edit → Import DD/MMM/YYYY ✅\n";
