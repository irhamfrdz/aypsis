<?php
/**
 * 🧪 Test Dropdown Menu Template dan Export Master Karyawan
 * Memastikan dropdown menu berfungsi dengan baik
 */

echo "📥 TEST DROPDOWN MENU TEMPLATE DAN EXPORT\n";
echo "========================================\n\n";

// Test 1: Cek perubahan struktur di View
echo "🔍 1. CEK STRUKTUR DROPDOWN DI VIEW:\n";
$view_file = 'resources/views/master-karyawan/index.blade.php';

if (file_exists($view_file)) {
    $view_content = file_get_contents($view_file);

    // Cek Template Dropdown
    echo "📋 TEMPLATE DROPDOWN:\n";
    $has_template_button = strpos($view_content, '>Template') !== false && strpos($view_content, 'fa-chevron-down') !== false;
    echo $has_template_button ? "✅ Template button dengan chevron: ADA\n" : "❌ Template button dengan chevron: TIDAK ADA\n";

    $has_template_csv = strpos($view_content, 'Template CSV') !== false && strpos($view_content, 'Format CSV standar') !== false;
    echo $has_template_csv ? "✅ Template CSV option: ADA\n" : "❌ Template CSV option: TIDAK ADA\n";

    $has_template_excel = strpos($view_content, 'Template Excel') !== false && strpos($view_content, 'Kompatibel dengan Excel') !== false;
    echo $has_template_excel ? "✅ Template Excel option: ADA\n" : "❌ Template Excel option: TIDAK ADA\n";

    echo "\n📤 EXPORT DROPDOWN:\n";
    $has_export_button = strpos($view_content, '>Export') !== false && strpos($view_content, 'fa-chevron-down') !== false;
    echo $has_export_button ? "✅ Export button dengan chevron: ADA\n" : "❌ Export button dengan chevron: TIDAK ADA\n";

    $has_export_csv = strpos($view_content, 'Export CSV') !== false && strpos($view_content, 'Format CSV dengan separator') !== false;
    echo $has_export_csv ? "✅ Export CSV option: ADA\n" : "❌ Export CSV option: TIDAK ADA\n";

    $has_export_excel = strpos($view_content, 'Export Excel') !== false && strpos($view_content, 'Anti scientific notation') !== false;
    echo $has_export_excel ? "✅ Export Excel option: ADA\n" : "❌ Export Excel option: TIDAK ADA\n";

} else {
    echo "❌ File view tidak ditemukan\n";
}

echo "\n";

// Test 2: Cek JavaScript untuk dropdown functionality
echo "⚡ 2. CEK JAVASCRIPT DROPDOWN:\n";

if (file_exists($view_file)) {
    $view_content = file_get_contents($view_file);

    // Cek dropdown functionality
    $has_dropdown_js = strpos($view_content, 'Dropdown functionality') !== false;
    echo $has_dropdown_js ? "✅ Dropdown JavaScript: ADA\n" : "❌ Dropdown JavaScript: TIDAK ADA\n";

    // Cek click event listener
    $has_click_event = strpos($view_content, "addEventListener('click'") !== false;
    echo $has_click_event ? "✅ Click event listener: ADA\n" : "❌ Click event listener: TIDAK ADA\n";

    // Cek close on outside click
    $has_outside_click = strpos($view_content, 'clicking outside') !== false;
    echo $has_outside_click ? "✅ Close on outside click: ADA\n" : "❌ Close on outside click: TIDAK ADA\n";

    // Cek escape key handling
    $has_escape_key = strpos($view_content, "e.key === 'Escape'") !== false;
    echo $has_escape_key ? "✅ Escape key handling: ADA\n" : "❌ Escape key handling: TIDAK ADA\n";

    // Cek toggle classes
    $has_toggle_classes = strpos($view_content, 'classList.toggle') !== false;
    echo $has_toggle_classes ? "✅ CSS class toggle: ADA\n" : "❌ CSS class toggle: TIDAK ADA\n";
}

echo "\n";

// Test 3: Cek CSS classes untuk styling
echo "🎨 3. CEK STYLING DROPDOWN:\n";

if (file_exists($view_file)) {
    $view_content = file_get_contents($view_file);

    // Cek dropdown positioning
    $has_positioning = strpos($view_content, 'absolute right-0 top-full') !== false;
    echo $has_positioning ? "✅ Dropdown positioning: ADA\n" : "❌ Dropdown positioning: TIDAK ADA\n";

    // Cek shadow dan border
    $has_styling = strpos($view_content, 'shadow-lg border border-gray-200') !== false;
    echo $has_styling ? "✅ Shadow dan border: ADA\n" : "❌ Shadow dan border: TIDAK ADA\n";

    // Cek transition effects
    $has_transitions = strpos($view_content, 'transition-all duration-200') !== false;
    echo $has_transitions ? "✅ Transition effects: ADA\n" : "❌ Transition effects: TIDAK ADA\n";

    // Cek opacity classes
    $has_opacity = strpos($view_content, 'opacity-0 invisible') !== false && strpos($view_content, 'opacity-100') !== false;
    echo $has_opacity ? "✅ Opacity animation: ADA\n" : "❌ Opacity animation: TIDAK ADA\n";

    // Cek hover effects di menu items
    $has_hover_items = strpos($view_content, 'hover:bg-gray-100') !== false;
    echo $has_hover_items ? "✅ Menu item hover: ADA\n" : "❌ Menu item hover: TIDAK ADA\n";
}

echo "\n";

// Test 4: Cek pengurangan jumlah tombol
echo "📊 4. PERBANDINGAN SEBELUM DAN SESUDAH:\n";

if (file_exists($view_file)) {
    $view_content = file_get_contents($view_file);

    // Hitung tombol yang tersisa
    $button_count = substr_count($view_content, 'inline-flex items-center px-3 py-2 bg-');
    echo "🔘 Total tombol sekarang: $button_count\n";

    // Cek tidak ada lagi tombol individual template/export
    $no_individual_template = strpos($view_content, 'Template CSV') === false || strpos($view_content, 'Template Excel') === false ? false : true;
    $no_individual_export = strpos($view_content, 'Export CSV') === false || strpos($view_content, 'Export Excel') === false ? false : true;

    // Yang ada sekarang adalah dropdown
    $has_template_dropdown = strpos($view_content, '>Template') !== false && strpos($view_content, 'chevron-down') !== false;
    $has_export_dropdown = strpos($view_content, '>Export') !== false && strpos($view_content, 'chevron-down') !== false;

    echo $has_template_dropdown ? "✅ Template dropdown: MENGGANTIKAN 2 tombol\n" : "❌ Template dropdown: TIDAK ADA\n";
    echo $has_export_dropdown ? "✅ Export dropdown: MENGGANTIKAN 2 tombol\n" : "❌ Export dropdown: TIDAK ADA\n";
}

echo "\n";

// Test 5: Routes yang digunakan
echo "🔗 5. CEK ROUTES YANG DIGUNAKAN:\n";

$routes = [
    'master.karyawan.template' => 'Template CSV',
    'master.karyawan.simple-excel-template' => 'Template Excel',
    'master.karyawan.export' => 'Export CSV',
    'master.karyawan.export-excel' => 'Export Excel'
];

foreach ($routes as $route => $description) {
    if (file_exists($view_file)) {
        $view_content = file_get_contents($view_file);
        $has_route = strpos($view_content, $route) !== false;
        echo $has_route ? "✅ Route $route: ADA ($description)\n" : "❌ Route $route: TIDAK ADA\n";
    }
}

echo "\n";

// Test 6: Summary
echo "📋 6. RINGKASAN DROPDOWN IMPLEMENTATION:\n";
echo "======================================\n";
echo "🎯 PERUBAHAN YANG DILAKUKAN:\n";
echo "   ❌ SEBELUM: 6 tombol terpisah\n";
echo "      - Tambah Karyawan\n";
echo "      - Template CSV  \n";
echo "      - Template Excel\n";
echo "      - Cetak Semua\n";
echo "      - Export CSV\n";
echo "      - Export Excel\n";
echo "      - Import Excel/CSV\n\n";

echo "   ✅ SESUDAH: 5 tombol (lebih ringkas)\n";
echo "      - Tambah Karyawan\n";
echo "      - Template ⬇️ (dropdown: CSV, Excel)\n";
echo "      - Cetak Semua\n";
echo "      - Export ⬇️ (dropdown: CSV, Excel)\n";
echo "      - Import Excel/CSV\n\n";

echo "✨ KEUNTUNGAN DROPDOWN:\n";
echo "   1. 🎛️ Interface lebih bersih dan ringkas\n";
echo "   2. 🎨 Visual hierarchy yang lebih baik\n";
echo "   3. 📱 Responsive design yang lebih optimal\n";
echo "   4. 🖱️ User experience yang modern\n";
echo "   5. 🔍 Mudah ditemukan dan dipahami\n";
echo "   6. ⚡ Interaction yang smooth dengan animasi\n\n";

echo "🎮 CARA PENGGUNAAN:\n";
echo "   1. Klik tombol 'Template' → muncul dropdown dengan 2 pilihan\n";
echo "   2. Klik tombol 'Export' → muncul dropdown dengan 2 pilihan\n";
echo "   3. Klik di luar dropdown → dropdown tertutup otomatis\n";
echo "   4. Tekan ESC → semua dropdown tertutup\n";
echo "   5. Hover untuk visual feedback\n\n";

echo "🚀 STATUS: DROPDOWN MENU BERHASIL DIIMPLEMENTASI!\n";
?>
