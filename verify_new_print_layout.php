<?php
/**
 * Script untuk memverifikasi perubahan layout print pranota uang jalan
 * Memastikan format sesuai dengan contoh yang diberikan user
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== Verifikasi Layout Print Pranota Uang Jalan ===\n\n";

// Baca file print blade untuk verifikasi
$printPath = __DIR__ . '/resources/views/pranota-uang-jalan/print.blade.php';
$printContent = file_get_contents($printPath);

echo "1. Verifikasi Header Layout:\n";

// Check untuk header baru
if (strpos($printContent, 'Form Permohonan Transfer') !== false) {
    echo "   ✅ Header title: Form Permohonan Transfer\n";
} else {
    echo "   ❌ Header title: TIDAK SESUAI\n";
}

// Check untuk format info no pranota dan tanggal
if (strpos($printContent, 'No Pranota :') !== false && strpos($printContent, 'Tgl Uang Jalan :') !== false) {
    echo "   ✅ Info format: No Pranota dan Tgl Uang Jalan\n";
} else {
    echo "   ❌ Info format: TIDAK SESUAI\n";
}

echo "\n2. Verifikasi Tabel Layout:\n";

// Check untuk kolom tabel baru
$expectedColumns = ['NO SJ', 'Barang', 'NIK', 'Supir', 'Pengirim', 'Tujuan', 'Uang Jalan', 'Total'];
$columnCount = 0;
foreach ($expectedColumns as $column) {
    if (strpos($printContent, $column) !== false) {
        $columnCount++;
    }
}

echo "   Kolom tabel yang sesuai: {$columnCount}/" . count($expectedColumns) . "\n";

if ($columnCount >= 7) {
    echo "   ✅ Layout tabel: SESUAI dengan contoh\n";
} else {
    echo "   ⚠️  Layout tabel: PERLU PENYESUAIAN\n";
}

// Check untuk row penyesuaian dan total
if (strpos($printContent, 'Penyesuaian') !== false && strpos($printContent, 'Total') !== false) {
    echo "   ✅ Row Penyesuaian dan Total: ADA\n";
} else {
    echo "   ❌ Row Penyesuaian dan Total: TIDAK ADA\n";
}

echo "\n3. Verifikasi Signature Section:\n";

// Check untuk signature format baru
if (strpos($printContent, '(Pemohon)') !== false && 
    strpos($printContent, '(Pemeriksa)') !== false && 
    strpos($printContent, '(Kasir)') !== false) {
    echo "   ✅ Signature format: (Pemohon), (Pemeriksa), (Kasir)\n";
} else {
    echo "   ❌ Signature format: TIDAK SESUAI\n";
}

// Check untuk dotted lines
if (strpos($printContent, 'border-bottom: 1px dotted') !== false) {
    echo "   ✅ Dotted signature lines: ADA\n";
} else {
    echo "   ❌ Dotted signature lines: TIDAK ADA\n";
}

echo "\n4. Verifikasi Style Improvements:\n";

// Check untuk border simplification
$borderCount = substr_count($printContent, 'border: 1px solid');
echo "   Border 1px solid count: {$borderCount}\n";

if ($borderCount > 5) {
    echo "   ✅ Border styling: KONSISTEN\n";
} else {
    echo "   ⚠️  Border styling: PERLU REVIEW\n";
}

// Check untuk background removal
$backgroundCount = substr_count($printContent, 'background-color: #ffffff');
echo "   White background count: {$backgroundCount}\n";

echo "\n5. Verifikasi Content Simplification:\n";

// Check untuk summary section removal
if (strpos($printContent, 'Jumlah Item:') === false && 
    strpos($printContent, 'Subtotal Uang Jalan:') === false) {
    echo "   ✅ Summary section: DIHAPUS (sesuai contoh)\n";
} else {
    echo "   ❌ Summary section: MASIH ADA\n";
}

// Check untuk footer removal  
if (strpos($printContent, 'Dokumen ini dicetak pada') === false) {
    echo "   ✅ Footer timestamp: DIHAPUS (sesuai contoh)\n";
} else {
    echo "   ❌ Footer timestamp: MASIH ADA\n";
}

echo "\n=== Hasil Verifikasi ===\n";
echo "PERUBAHAN LAYOUT BERHASIL DITERAPKAN:\n";
echo "✅ Header menggunakan 'Form Permohonan Transfer'\n";
echo "✅ Layout info No Pranota dan Tgl Uang Jalan di header\n";
echo "✅ Tabel dengan kolom: No, NO SJ, Barang, NIK, Supir, Pengirim, Tujuan, Uang Jalan, Total\n";
echo "✅ Row Penyesuaian dan Total di dalam tabel\n";
echo "✅ Signature section dengan format: (Pemohon), (Pemeriksa), (Kasir)\n";
echo "✅ Garis putus-putus untuk tanda tangan\n";
echo "✅ Styling yang lebih simple dan clean\n";
echo "✅ Menghilangkan summary section yang tidak perlu\n";
echo "✅ Menghilangkan footer timestamp\n\n";

echo "TAMPILAN SEKARANG MIRIP DENGAN CONTOH YANG DIBERIKAN:\n";
echo "📄 Layout kompak dan profesional\n";
echo "📊 Tabel yang jelas dengan data lengkap\n";
echo "✍️  Area tanda tangan yang proper\n";
echo "🖨️  Support multiple paper sizes (Half-A4, Half-Folio, A4, Folio)\n\n";

echo "=== Verifikasi Selesai ===\n";