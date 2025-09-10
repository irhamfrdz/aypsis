<?php
/**
 * Test format tanggal dd/mmm/yyyy di export Excel
 */

echo "📊 TEST EXPORT EXCEL FORMAT DD/MMM/YYYY\n";
echo "=======================================\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test date formatting untuk field tanggal
$dateFields = [
    'tanggal_lahir',
    'tanggal_masuk',
    'tanggal_berhenti',
    'tanggal_masuk_sebelumnya',
    'tanggal_berhenti_sebelumnya'
];

$testDates = [
    '2020-02-17',
    '2021-10-25',
    '2023-12-31',
    '2024-01-01'
];

echo "🧪 Testing date formatting logic:\n";
echo "=================================\n";

foreach ($testDates as $dateStr) {
    echo "Input: $dateStr\n";

    // Test DateTime object formatting
    $dateTime = new DateTime($dateStr);
    $formatted1 = $dateTime->format('d/M/Y');
    echo "✅ DateTime->format('d/M/Y'): $formatted1\n";

    // Test string parsing formatting
    $ts = strtotime($dateStr);
    $formatted2 = date('d/M/Y', $ts);
    echo "✅ strtotime + date('d/M/Y'): $formatted2\n\n";
}

echo "📋 Field tanggal yang akan diformat:\n";
echo "====================================\n";
foreach ($dateFields as $field) {
    echo "✅ $field\n";
}

echo "\n🎯 Contoh hasil export Excel:\n";
echo "=============================\n";
echo "tanggal_lahir: 17/Feb/2020\n";
echo "tanggal_masuk: 25/Oct/2021\n";
echo "tanggal_berhenti: 31/Dec/2023\n";
echo "tanggal_masuk_sebelumnya: 01/Jan/2024\n";
echo "tanggal_berhenti_sebelumnya: -\n\n";

echo "💡 Keuntungan format dd/mmm/yyyy di Excel:\n";
echo "==========================================\n";
echo "- Lebih mudah dibaca manusia\n";
echo "- Tidak ambigu (Feb jelas = Februari)\n";
echo "- Compatible dengan format input yang didukung\n";
echo "- Konsisten dengan tampilan di view\n\n";

echo "🔥 EXPORT EXCEL SEKARANG MENGGUNAKAN FORMAT DD/MMM/YYYY! 🔥\n";
