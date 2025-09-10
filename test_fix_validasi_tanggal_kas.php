<?php
/**
 * 🧪 Test Fix Validasi Tanggal Kas Pembayaran Pranota Supir
 * Memastikan format dd/mmm/yyyy tidak menghasilkan error validasi
 */

require_once 'vendor/autoload.php';

echo "📅 TEST FIX VALIDASI TANGGAL KAS PEMBAYARAN PRANOTA\n";
echo "================================================\n\n";

// Test 1: Cek perubahan di Controller
echo "🔍 1. CEK PERUBAHAN DI CONTROLLER:\n";
$controller_file = 'app/Http/Controllers/PembayaranPranotaSupirController.php';

if (file_exists($controller_file)) {
    $controller_content = file_get_contents($controller_file);

    // Cek import Carbon
    $has_carbon_import = strpos($controller_content, 'use Carbon\Carbon;') !== false;
    echo $has_carbon_import ? "✅ Carbon import: ADA\n" : "❌ Carbon import: TIDAK ADA\n";

    // Cek validasi tanggal_kas berubah ke string
    $has_string_validation = strpos($controller_content, "'tanggal_kas' => 'required|string'") !== false;
    echo $has_string_validation ? "✅ Validasi tanggal_kas: STRING (bukan date)\n" : "❌ Validasi tanggal_kas: MASIH DATE\n";

    // Cek konversi format tanggal
    $has_format_conversion = strpos($controller_content, "createFromFormat('d/M/Y', \$validated['tanggal_kas'])") !== false;
    echo $has_format_conversion ? "✅ Konversi format d/M/Y: ADA\n" : "❌ Konversi format d/M/Y: TIDAK ADA\n";

} else {
    echo "❌ File controller tidak ditemukan\n";
}

echo "\n";

// Test 2: Simulasi format tanggal
echo "🧪 2. SIMULASI FORMAT TANGGAL:\n";

try {
    // Test Carbon format conversion
    $test_date = '09/Sep/2025';
    $carbon_date = \Carbon\Carbon::createFromFormat('d/M/Y', $test_date);
    $db_format = $carbon_date->format('Y-m-d');

    echo "Input format (d/M/Y): $test_date\n";
    echo "Database format (Y-m-d): $db_format\n";
    echo "✅ Konversi berhasil!\n";

} catch (Exception $e) {
    echo "❌ Error konversi: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Cek file view
echo "🎨 3. CEK VIEW PEMBAYARAN:\n";
$view_file = 'resources/views/pembayaran-pranota-supir/create.blade.php';

if (file_exists($view_file)) {
    $view_content = file_get_contents($view_file);

    // Cek input tanggal_kas adalah text readonly
    $has_readonly_text = strpos($view_content, 'type="text" name="tanggal_kas"') !== false &&
                        strpos($view_content, 'readonly') !== false;
    echo $has_readonly_text ? "✅ Input tanggal_kas: TEXT READONLY\n" : "❌ Input tanggal_kas: BUKAN TEXT READONLY\n";

    // Cek format d/M/Y di value
    $has_dmy_format = strpos($view_content, "now()->format('d/M/Y')") !== false;
    echo $has_dmy_format ? "✅ Format tanggal: d/M/Y\n" : "❌ Format tanggal: BUKAN d/M/Y\n";

    // Cek hidden field untuk validation
    $has_hidden_field = strpos($view_content, 'name="tanggal_pembayaran"') !== false &&
                       strpos($view_content, 'type="hidden"') !== false;
    echo $has_hidden_field ? "✅ Hidden field validation: ADA\n" : "❌ Hidden field validation: TIDAK ADA\n";

} else {
    echo "❌ File view tidak ditemukan\n";
}

echo "\n";

// Test 4: Summary
echo "📋 4. RINGKASAN PERBAIKAN:\n";
echo "========================\n";
echo "❗ MASALAH SEBELUMNYA:\n";
echo "   - Input tanggal_kas format d/M/Y (09/Sep/2025)\n";
echo "   - Validasi controller mengharapkan 'date' format (Y-m-d)\n";
echo "   - Error: 'tanggal kas field must be a valid date'\n\n";

echo "✅ SOLUSI YANG DITERAPKAN:\n";
echo "   1. Ubah validasi dari 'required|date' ke 'required|string'\n";
echo "   2. Tambah import Carbon di controller\n";
echo "   3. Konversi d/M/Y ke Y-m-d sebelum simpan ke database\n";
echo "   4. Tetap gunakan hidden field Y-m-d untuk backup validation\n\n";

echo "🎯 HASIL AKHIR:\n";
echo "   - User melihat: 09/Sep/2025 (readonly)\n";
echo "   - Database menyimpan: 2025-09-09\n";
echo "   - Tidak ada error validasi\n";
echo "   - Format konsisten dengan modul lain\n\n";

echo "🚀 STATUS: SIAP UNTUK TESTING!\n";
?>
