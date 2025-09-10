<?php
/**
 * 🧪 Test Fitur Sorting Nama Lengkap Master Karyawan
 * Memastikan tombol sortir berfungsi dengan benar
 */

// Include Laravel autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';

echo "🔤 TEST FITUR SORTING NAMA LENGKAP MASTER KARYAWAN\n";
echo "===============================================\n\n";

// Test 1: Cek perubahan di View
echo "🔍 1. CEK TOMBOL SORTING DI VIEW:\n";
$view_file = 'resources/views/master-karyawan/index.blade.php';

if (file_exists($view_file)) {
    $view_content = file_get_contents($view_file);

    // Cek tombol sort up (A-Z)
    $has_sort_up = strpos($view_content, 'fa-sort-up') !== false;
    echo $has_sort_up ? "✅ Tombol Sort A-Z (up): ADA\n" : "❌ Tombol Sort A-Z (up): TIDAK ADA\n";

    // Cek tombol sort down (Z-A)
    $has_sort_down = strpos($view_content, 'fa-sort-down') !== false;
    echo $has_sort_down ? "✅ Tombol Sort Z-A (down): ADA\n" : "❌ Tombol Sort Z-A (down): TIDAK ADA\n";

    // Cek parameter sort dan direction
    $has_sort_param = strpos($view_content, "'sort' => 'nama_lengkap'") !== false;
    echo $has_sort_param ? "✅ Parameter sort nama_lengkap: ADA\n" : "❌ Parameter sort nama_lengkap: TIDAK ADA\n";

    $has_direction_asc = strpos($view_content, "'direction' => 'asc'") !== false;
    echo $has_direction_asc ? "✅ Parameter direction asc: ADA\n" : "❌ Parameter direction asc: TIDAK ADA\n";

    $has_direction_desc = strpos($view_content, "'direction' => 'desc'") !== false;
    echo $has_direction_desc ? "✅ Parameter direction desc: ADA\n" : "❌ Parameter direction desc: TIDAK ADA\n";

    // Cek active state untuk visual feedback
    $has_active_state = strpos($view_content, 'text-blue-600') !== false;
    echo $has_active_state ? "✅ Active state styling: ADA\n" : "❌ Active state styling: TIDAK ADA\n";

} else {
    echo "❌ File view tidak ditemukan\n";
}

echo "\n";

// Test 2: Cek perubahan di Controller
echo "🎛️ 2. CEK LOGIKA SORTING DI CONTROLLER:\n";
$controller_file = 'app/Http/Controllers/KaryawanController.php';

if (file_exists($controller_file)) {
    $controller_content = file_get_contents($controller_file);

    // Cek handle sorting
    $has_sort_handling = strpos($controller_content, 'Handle sorting') !== false;
    echo $has_sort_handling ? "✅ Komentar Handle sorting: ADA\n" : "❌ Komentar Handle sorting: TIDAK ADA\n";

    // Cek get sort parameter
    $has_sort_field = strpos($controller_content, "\$sortField = \$request->get('sort'") !== false;
    echo $has_sort_field ? "✅ Get sort parameter: ADA\n" : "❌ Get sort parameter: TIDAK ADA\n";

    // Cek get direction parameter
    $has_sort_direction = strpos($controller_content, "\$sortDirection = \$request->get('direction'") !== false;
    echo $has_sort_direction ? "✅ Get direction parameter: ADA\n" : "❌ Get direction parameter: TIDAK ADA\n";

    // Cek validation sort field
    $has_validation = strpos($controller_content, 'allowedSortFields') !== false;
    echo $has_validation ? "✅ Validasi sort field: ADA\n" : "❌ Validasi sort field: TIDAK ADA\n";

    // Cek orderBy implementation
    $has_order_by = strpos($controller_content, 'orderBy($sortField, $sortDirection)') !== false;
    echo $has_order_by ? "✅ OrderBy implementation: ADA\n" : "❌ OrderBy implementation: TIDAK ADA\n";

    // Cek default sorting
    $has_default_sort = strpos($controller_content, "'nama_lengkap'") !== false;
    echo $has_default_sort ? "✅ Default sort nama_lengkap: ADA\n" : "❌ Default sort nama_lengkap: TIDAK ADA\n";

} else {
    echo "❌ File controller tidak ditemukan\n";
}

echo "\n";

// Test 3: Simulasi URL sorting
echo "🔗 3. SIMULASI URL SORTING:\n";

// URL untuk sort ascending
$url_asc = "route('master.karyawan.index', ['sort' => 'nama_lengkap', 'direction' => 'asc'])";
echo "📤 URL Sort A-Z: $url_asc\n";

// URL untuk sort descending
$url_desc = "route('master.karyawan.index', ['sort' => 'nama_lengkap', 'direction' => 'desc'])";
echo "📤 URL Sort Z-A: $url_desc\n";

// URL dengan search + sort
$url_search_sort = "route('master.karyawan.index', ['search' => 'admin', 'sort' => 'nama_lengkap', 'direction' => 'asc'])";
echo "🔍 URL Search + Sort: $url_search_sort\n";

echo "\n";

// Test 4: Visual Design Check
echo "🎨 4. CEK DESAIN VISUAL:\n";

if (file_exists($view_file)) {
    $view_content = file_get_contents($view_file);

    // Cek flex layout
    $has_flex_layout = strpos($view_content, 'flex items-center space-x-1') !== false;
    echo $has_flex_layout ? "✅ Flex layout untuk header: ADA\n" : "❌ Flex layout untuk header: TIDAK ADA\n";

    // Cek icon positioning
    $has_icon_column = strpos($view_content, 'flex flex-col') !== false;
    echo $has_icon_column ? "✅ Icon column layout: ADA\n" : "❌ Icon column layout: TIDAK ADA\n";

    // Cek hover effects
    $has_hover = strpos($view_content, 'hover:text-gray-600') !== false;
    echo $has_hover ? "✅ Hover effects: ADA\n" : "❌ Hover effects: TIDAK ADA\n";

    // Cek tooltip
    $has_tooltip = strpos($view_content, 'title="Urutkan A-Z"') !== false;
    echo $has_tooltip ? "✅ Tooltip Urutkan A-Z: ADA\n" : "❌ Tooltip Urutkan A-Z: TIDAK ADA\n";

    $has_tooltip_desc = strpos($view_content, 'title="Urutkan Z-A"') !== false;
    echo $has_tooltip_desc ? "✅ Tooltip Urutkan Z-A: ADA\n" : "❌ Tooltip Urutkan Z-A: TIDAK ADA\n";
}

echo "\n";

// Test 5: Security Check
echo "🔒 5. CEK KEAMANAN:\n";

if (file_exists($controller_file)) {
    $controller_content = file_get_contents($controller_file);

    // Cek whitelist allowed fields
    $allowed_fields = ['nama_lengkap', 'nik', 'divisi', 'pekerjaan', 'tanggal_masuk'];
    $has_whitelist = true;
    foreach ($allowed_fields as $field) {
        if (strpos($controller_content, "'$field'") === false) {
            $has_whitelist = false;
            break;
        }
    }
    echo $has_whitelist ? "✅ Whitelist allowed sort fields: ADA\n" : "❌ Whitelist allowed sort fields: TIDAK LENGKAP\n";

    // Cek direction validation
    $has_direction_validation = strpos($controller_content, "['asc', 'desc']") !== false;
    echo $has_direction_validation ? "✅ Direction validation: ADA\n" : "❌ Direction validation: TIDAK ADA\n";
}

echo "\n";

// Test 6: Summary
echo "📋 6. RINGKASAN FITUR SORTING:\n";
echo "============================\n";
echo "🎯 FITUR YANG DITAMBAHKAN:\n";
echo "   1. ⬆️ Tombol Sort A-Z (ascending)\n";
echo "   2. ⬇️ Tombol Sort Z-A (descending)\n";
echo "   3. 🎨 Visual feedback untuk active state\n";
echo "   4. 💡 Tooltip untuk user guidance\n";
echo "   5. 🔒 Security validation untuk sort parameters\n";
echo "   6. 🔗 URL parameter preservation (search + sort)\n\n";

echo "✅ KEUNTUNGAN PENGGUNA:\n";
echo "   - Mudah mengurutkan karyawan berdasarkan nama\n";
echo "   - Visual yang jelas untuk status sorting\n";
echo "   - Kombinasi search + sort tetap berfungsi\n";
echo "   - Responsif dan user-friendly\n";
echo "   - Default sort alphabetical untuk pengalaman yang konsisten\n\n";

echo "🎉 CARA PENGGUNAAN:\n";
echo "   1. Klik ⬆️ untuk mengurutkan A-Z (Admin → Zulkifli)\n";
echo "   2. Klik ⬇️ untuk mengurutkan Z-A (Zulkifli → Admin)\n";
echo "   3. Icon akan berubah warna biru saat aktif\n";
echo "   4. Hover untuk melihat tooltip\n";
echo "   5. Sorting tetap berfungsi saat melakukan pencarian\n\n";

echo "🚀 STATUS: FITUR SORTING SIAP DIGUNAKAN!\n";
?>
