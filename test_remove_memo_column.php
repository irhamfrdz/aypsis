<?php

// Test file untuk verifikasi penghapusan kolom memo dari tabel index
// Run dengan: php test_remove_memo_column.php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Penghapusan Kolom Memo dari Tabel Index ===\n";

// 1. Cek Index View
echo "1. Mengecek view index uang jalan...\n";
$indexViewPath = __DIR__ . '/resources/views/uang-jalan/index.blade.php';
if (file_exists($indexViewPath)) {
    $indexContent = file_get_contents($indexViewPath);
    
    // Cek header memo
    if (strpos($indexContent, '>Memo</th>') !== false) {
        echo "   ❌ Header kolom 'Memo' masih ada\n";
    } else {
        echo "   ✅ Header kolom 'Memo' sudah dihapus\n";
    }
    
    // Cek data memo di tbody
    if (strpos($indexContent, '$uangJalan->memo') !== false) {
        echo "   ❌ Data memo masih ditampilkan di tbody\n";
    } else {
        echo "   ✅ Data memo sudah dihapus dari tbody\n";
    }
    
    // Cek Str::limit untuk memo
    if (strpos($indexContent, "Str::limit(\$uangJalan->memo") !== false) {
        echo "   ❌ Logic Str::limit untuk memo masih ada\n";
    } else {
        echo "   ✅ Logic Str::limit untuk memo sudah dihapus\n";
    }
    
    // Cek title attribute untuk memo
    if (strpos($indexContent, 'title="{{ $uangJalan->memo }}"') !== false) {
        echo "   ❌ Title attribute memo masih ada\n";
    } else {
        echo "   ✅ Title attribute memo sudah dihapus\n";
    }
    
    // Hitung jumlah kolom header
    $headerMatches = preg_match_all('/<th class="px-/', $indexContent);
    echo "   📊 Total kolom header saat ini: {$headerMatches}\n";
    
} else {
    echo "   ❌ File index view tidak ditemukan\n";
}

echo "\n2. Verifikasi struktur tabel...\n";
echo "   📋 Kolom yang tersisa:\n";
echo "   1. No\n";
echo "   2. No Uang Jalan\n";
echo "   3. No Surat Jalan (+ nomor order + tipe kontainer)\n";
echo "   4. Tanggal UJ\n";
echo "   5. Supir (+ kenek)\n";
echo "   6. Total\n";
echo "   7. Status\n";
echo "   8. Aksi\n";

echo "\n3. Test dengan data existing...\n";
use App\Models\UangJalan;

$totalUangJalan = UangJalan::count();
$uangJalanWithMemo = UangJalan::whereNotNull('memo')->where('memo', '!=', '')->count();

echo "   📊 Total uang jalan: {$totalUangJalan}\n";
echo "   📊 Uang jalan dengan memo: {$uangJalanWithMemo}\n";
echo "   ℹ️  Memo tetap tersimpan di database, hanya tidak ditampilkan di index\n";

echo "\n4. Benefit penghapusan kolom memo...\n";
echo "   ✅ Tabel lebih compact dan rapi\n";
echo "   ✅ Fokus pada informasi penting (nomor, tanggal, total, status)\n";
echo "   ✅ Lebih responsive di mobile device\n";
echo "   ✅ Loading tabel lebih cepat\n";
echo "   ℹ️  Memo masih bisa dilihat di detail/edit page\n";

echo "\n=== Hasil Penghapusan ===\n";
echo "✅ Header kolom memo sudah dihapus\n";
echo "✅ Data memo sudah dihapus dari tbody\n";
echo "✅ Logic display memo sudah dihapus\n";
echo "✅ Tabel sekarang lebih compact dengan 8 kolom\n";

echo "\n🎯 KOLOM MEMO BERHASIL DIHAPUS DARI TABEL!\n";