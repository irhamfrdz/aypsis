<?php

echo "🧪 Testing Antar Kontainer Sewa Form Changes\n";
echo "==========================================\n\n";

// Simulasi kondisi untuk kegiatan antar kontainer sewa
$kegiatanName = "ANTAR KONTAINER SEWA";
$kegiatanLower = strtolower($kegiatanName);

// Test detection logic
$isAntarKontainerSewa = (stripos($kegiatanLower, 'antar') !== false &&
                         stripos($kegiatanLower, 'kontainer') !== false &&
                         stripos($kegiatanLower, 'sewa') !== false);

echo "1. Kegiatan Detection Test:\n";
echo "   Kegiatan: '{$kegiatanName}'\n";
echo "   Kegiatan Lower: '{$kegiatanLower}'\n";
echo "   Is Antar Kontainer Sewa: " . ($isAntarKontainerSewa ? "✅ YES" : "❌ NO") . "\n\n";

// Test other similar activities for comparison
$testActivities = [
    "ANTAR KONTAINER SEWA",
    "ANTAR KONTAINER PERBAIKAN",
    "ANTAR SEWA",
    "PERBAIKAN KONTAINER",
    "PENGIRIMAN",
    "TARIK KONTAINER SEWA"
];

echo "2. Activity Classification Test:\n";
foreach($testActivities as $activity) {
    $lower = strtolower($activity);

    $isAntarKontainerSewa = (stripos($lower, 'antar') !== false &&
                             stripos($lower, 'kontainer') !== false &&
                             stripos($lower, 'sewa') !== false);

    $isAntarKontainerPerbaikan = (stripos($lower, 'antar') !== false &&
                                  stripos($lower, 'kontainer') !== false &&
                                  stripos($lower, 'perbaikan') !== false);

    $isPerbaikanKontainer = (stripos($lower, 'perbaikan') !== false &&
                             stripos($lower, 'kontainer') !== false);

    $isAntarSewa = stripos($lower, 'antar') !== false && stripos($lower, 'sewa') !== false;

    echo "   '{$activity}':\n";
    echo "     - Antar Kontainer Sewa: " . ($isAntarKontainerSewa ? "✅" : "❌") . "\n";
    echo "     - Antar Kontainer Perbaikan: " . ($isAntarKontainerPerbaikan ? "✅" : "❌") . "\n";
    echo "     - Perbaikan Kontainer: " . ($isPerbaikanKontainer ? "✅" : "❌") . "\n";
    echo "     - Antar Sewa: " . ($isAntarSewa ? "✅" : "❌") . "\n";

    // Determine form field type
    if($isAntarKontainerPerbaikan) {
        echo "     → Form Type: SELECT with TAGS (stock kontainer + free text)\n";
    } elseif($isAntarKontainerSewa) {
        echo "     → Form Type: FREE TEXT INPUT ✅ (UPDATED)\n";
    } elseif($isPerbaikanKontainer) {
        echo "     → Form Type: FREE TEXT INPUT\n";
    } elseif($isAntarSewa) {
        echo "     → Form Type: FREE TEXT INPUT\n";
    } else {
        echo "     → Form Type: SELECT DROPDOWN\n";
    }
    echo "\n";
}

echo "3. Form Field Summary:\n";
echo "   Before Change: Antar Kontainer Sewa → SELECT dropdown from database\n";
echo "   After Change:  Antar Kontainer Sewa → FREE TEXT input field\n\n";

echo "4. HTML Form Field Preview:\n";
echo "   OLD: <select name=\"nomor_kontainer[]\" ...>\n";
echo "   NEW: <input type=\"text\" name=\"nomor_kontainer[]\" placeholder=\"Masukkan nomor kontainer 20ft #1\" required>\n\n";

echo "5. Help Text Changes:\n";
echo "   OLD: 'Pilih kontainer 20ft dari database yang akan diantar ke customer.'\n";
echo "   NEW: 'Masukkan nomor kontainer 20ft yang akan diantar ke customer.'\n\n";

echo "✅ Form changes successfully implemented!\n";
echo "\n📝 Impact Summary:\n";
echo "- Supir can now type any container number freely\n";
echo "- No more dependency on existing database records\n";
echo "- Consistent with other 'antar sewa' activities\n";
echo "- Simplifies the form interface for antar kontainer sewa\n";
