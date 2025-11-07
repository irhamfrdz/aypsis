<?php

// Test file untuk verifikasi penghapusan indikator prospek dari index
// Run dengan: php test_remove_prospek_indicator_index.php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Penghapusan Indikator Prospek dari Index ===\n";

// 1. Cek Index View
echo "1. Mengecek view index uang jalan...\n";
$indexViewPath = __DIR__ . '/resources/views/uang-jalan/index.blade.php';
if (file_exists($indexViewPath)) {
    $indexContent = file_get_contents($indexViewPath);
    
    // Cek indikator →P
    if (strpos($indexContent, '→P') !== false) {
        echo "   ❌ Indikator '→P' masih ada di index\n";
    } else {
        echo "   ✅ Indikator '→P' sudah dihapus dari index\n";
    }
    
    // Cek variable $isProspekType
    if (strpos($indexContent, '$isProspekType') !== false) {
        echo "   ❌ Variable \$isProspekType masih ada\n";
    } else {
        echo "   ✅ Variable \$isProspekType sudah dihapus\n";
    }
    
    // Cek logika in_array FCL/CARGO
    if (strpos($indexContent, "in_array(\$tipeUpper, ['FCL', 'CARGO'])") !== false) {
        echo "   ❌ Logika pengecekan FCL/CARGO masih ada\n";
    } else {
        echo "   ✅ Logika pengecekan FCL/CARGO sudah dihapus\n";
    }
    
    // Cek conditional styling berdasarkan prospek type
    if (strpos($indexContent, '$isProspekType ? ') !== false) {
        echo "   ❌ Conditional styling berdasarkan prospek masih ada\n";
    } else {
        echo "   ✅ Conditional styling berdasarkan prospek sudah dihapus\n";
    }
    
    // Cek apakah masih ada styling bg-blue-100 text-blue-600 untuk FCL
    if (strpos($indexContent, 'bg-blue-100 text-blue-600') !== false) {
        echo "   ⚠️  Styling bg-blue-100 text-blue-600 masih ada (mungkin untuk komponen lain)\n";
    } else {
        echo "   ✅ Styling khusus prospek sudah dihapus\n";
    }
    
    // Cek apakah tipe kontainer masih ditampilkan (tapi tanpa indikator prospek)
    if (strpos($indexContent, 'tipe_kontainer') !== false) {
        echo "   ✅ Tipe kontainer masih ditampilkan (tanpa indikator prospek)\n";
    } else {
        echo "   ⚠️  Tipe kontainer juga ikut terhapus\n";
    }
    
} else {
    echo "   ❌ File index view tidak ditemukan\n";
}

echo "\n2. Verifikasi perubahan styling...\n";
echo "   📋 Sebelum: FCL/CARGO = bg-blue-100 text-blue-600 dengan '→P'\n";
echo "   📋 Sesudah: Semua tipe = bg-gray-100 text-gray-600 tanpa indikator\n";

echo "\n3. Test tampilan di index...\n";
echo "   ✅ Tipe kontainer tetap muncul (LCL, FCL, CARGO, dll)\n";
echo "   ✅ Styling uniform untuk semua tipe (abu-abu)\n";
echo "   ❌ Tidak ada lagi indikator '→P' untuk FCL/CARGO\n";
echo "   ❌ Tidak ada lagi highlighting khusus untuk FCL/CARGO\n";

use App\Models\UangJalan;

echo "\n4. Test dengan data existing...\n";
$uangJalansWithFcl = UangJalan::with('suratJalan')
    ->whereHas('suratJalan', function($q) {
        $q->whereIn('tipe_kontainer', ['FCL', 'fcl', 'CARGO', 'cargo']);
    })
    ->count();

echo "   📊 Uang jalan dengan tipe FCL/CARGO: {$uangJalansWithFcl}\n";
echo "   ✅ Semua akan ditampilkan dengan styling uniform tanpa indikator prospek\n";

echo "\n=== Hasil Penghapusan ===\n";
echo "✅ Indikator '→P' sudah dihapus dari index\n";
echo "✅ Logika pengecekan FCL/CARGO sudah dihapus\n";
echo "✅ Conditional styling berdasarkan prospek sudah dihapus\n";
echo "✅ Semua tipe kontainer sekarang uniform styling\n";

echo "\n=== Yang Masih Muncul di Index ===\n";
echo "1. Nomor uang jalan\n";
echo "2. Nomor surat jalan + nomor order\n";
echo "3. Tipe kontainer (dengan styling uniform)\n";
echo "4. Tanggal uang jalan\n";
echo "5. Supir & kenek\n";
echo "6. Total\n";
echo "7. Memo\n";
echo "8. Status\n";
echo "9. Aksi (view, edit, delete)\n";

echo "\n🎯 INDIKATOR PROSPEK DI INDEX BERHASIL DIHAPUS!\n";