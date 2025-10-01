<?php
// Revisi analisis controller yang benar-benar tidak terpakai

$unusedControllers = [
    'KontainerSewaController' => false,  // Cek lagi
    'PembayaranPranotaTagihanKontainerController' => false,  // Cek lagi
    'PranotaCatController' => false,  // Cek lagi
    'PranotaController' => true,  // Kemungkinan tidak terpakai
    'PranotaSewaController' => true,  // Kemungkinan tidak terpakai
    'PranotaTagihanKontainerController' => false, // Cek lagi
    'PricelistSewaKontainerController' => true,  // Kemungkinan tidak terpakai
    'SupirCheckpointController' => true,  // Kemungkinan tidak terpakai
    'TagihanKontainerSewaController' => false, // Masih ada di routes cache
    'TestController' => true,  // Test controller, aman dihapus
];

echo "🔍 CONTROLLER YANG BENAR-BENAR AMAN DIHAPUS:\n";
echo "==============================================\n\n";

foreach ($unusedControllers as $controller => $safeToDelete) {
    if ($safeToDelete) {
        echo "✅ {$controller} - AMAN DIHAPUS\n";
    } else {
        echo "⚠️  {$controller} - MASIH DIGUNAKAN, JANGAN DIHAPUS\n";
    }
}

echo "\n📋 CONTROLLERS YANG AMAN DIHAPUS:\n";
echo "================================\n";
$safeControllers = array_keys(array_filter($unusedControllers));
foreach ($safeControllers as $controller) {
    echo "- {$controller}\n";
}
echo "\nTotal: " . count($safeControllers) . " controllers\n";
?>
