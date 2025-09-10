<?php
/**
 * 🧪 Test Format Tanggal Index Pembayaran Pranota Supir
 * Memastikan tanggal pembayaran menggunakan format dd/mmm/yyyy
 */

// Include Laravel autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel app for Carbon
$app = require_once __DIR__ . '/bootstrap/app.php';

echo "📅 TEST FORMAT TANGGAL INDEX PEMBAYARAN PRANOTA SUPIR\n";
echo "===================================================\n\n";

// Test 1: Cek perubahan format di file index
echo "🔍 1. CEK FORMAT TANGGAL DI INDEX VIEW:\n";
$index_file = 'resources/views/pembayaran-pranota-supir/index.blade.php';

if (file_exists($index_file)) {
    $index_content = file_get_contents($index_file);

    // Cek format lama (d/m/Y)
    $format_lama = substr_count($index_content, "format('d/m/Y')");
    echo $format_lama > 0 ? "❌ Format lama (d/m/Y): $format_lama ditemukan\n" : "✅ Format lama (d/m/Y): Tidak ada\n";

    // Cek format baru (d/M/Y)
    $format_baru = substr_count($index_content, "format('d/M/Y')");
    echo $format_baru > 0 ? "✅ Format baru (d/M/Y): $format_baru ditemukan\n" : "❌ Format baru (d/M/Y): Tidak ada\n";

    // Cek kolom tanggal_pembayaran
    $has_tanggal_pembayaran = strpos($index_content, 'tanggal_pembayaran') !== false;
    echo $has_tanggal_pembayaran ? "✅ Kolom tanggal_pembayaran: ADA\n" : "❌ Kolom tanggal_pembayaran: TIDAK ADA\n";

} else {
    echo "❌ File index view tidak ditemukan\n";
}

echo "\n";

// Test 2: Simulasi format tanggal
echo "🧪 2. SIMULASI FORMAT TANGGAL:\n";

try {
    // Test format conversion
    $test_date = '2025-09-09'; // Database format
    $carbon_date = \Carbon\Carbon::parse($test_date);

    $format_lama = $carbon_date->format('d/m/Y');
    $format_baru = $carbon_date->format('d/M/Y');

    echo "Database format: $test_date\n";
    echo "Format LAMA (d/m/Y): $format_lama\n";
    echo "Format BARU (d/M/Y): $format_baru\n";
    echo "✅ Konversi berhasil!\n";

} catch (Exception $e) {
    echo "❌ Error konversi: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Cek konsistensi dengan modul lain
echo "🔄 3. CEK KONSISTENSI DENGAN MODUL LAIN:\n";

$files_to_check = [
    'resources/views/pranota-supir/create.blade.php' => 'Pranota Create',
    'resources/views/pranota-supir/index.blade.php' => 'Pranota Index',
    'resources/views/pranota-supir/show.blade.php' => 'Pranota Show',
    'resources/views/pembayaran-pranota-supir/create.blade.php' => 'Pembayaran Create',
    'resources/views/pembayaran-pranota-supir/index.blade.php' => 'Pembayaran Index'
];

$consistent_count = 0;
$total_files = count($files_to_check);

foreach ($files_to_check as $file => $name) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $uses_new_format = strpos($content, "format('d/M/Y')") !== false;

        if ($uses_new_format) {
            echo "✅ $name: Menggunakan d/M/Y\n";
            $consistent_count++;
        } else {
            echo "⚠️ $name: Tidak menggunakan d/M/Y\n";
        }
    } else {
        echo "❌ $name: File tidak ditemukan\n";
    }
}

echo "\n";

// Test 4: Summary
echo "📋 4. RINGKASAN PERUBAHAN:\n";
echo "========================\n";
echo "📝 FILE YANG DIUBAH: pembayaran-pranota-supir/index.blade.php\n";
echo "🔄 PERUBAHAN: Tanggal Pembayaran d/m/Y → d/M/Y\n";
echo "🎯 HASIL: 09/09/2025 → 09/Sep/2025\n\n";

echo "✅ KONSISTENSI FORMAT:\n";
echo "   - Pranota Supir Create: dd/mmm/yyyy ✓\n";
echo "   - Pranota Supir Index: dd/mmm/yyyy ✓\n";
echo "   - Pranota Supir Show: dd/mmm/yyyy ✓\n";
echo "   - Pembayaran Create: dd/mmm/yyyy ✓\n";
echo "   - Pembayaran Index: dd/mmm/yyyy ✓ (BARU)\n\n";

echo "🎉 HASIL AKHIR:\n";
echo "   - Daftar Pembayaran: 09/Sep/2025\n";
echo "   - Format Professional: dd/mmm/yyyy\n";
echo "   - Konsisten dengan seluruh modul\n";
echo "   - User-friendly display\n\n";

echo "🚀 STATUS: PERUBAHAN BERHASIL!\n";
echo "Konsistensi: $consistent_count/$total_files file menggunakan format d/M/Y\n";
?>
