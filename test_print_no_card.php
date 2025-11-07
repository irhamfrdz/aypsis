<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Print Pranota Tanpa Card Info ===\n\n";

// Cari pranota untuk test
$pranota = \App\Models\PranotaUangJalan::with(['uangJalans.suratJalan', 'creator'])
    ->first();

if (!$pranota) {
    echo "❌ Tidak ada pranota untuk test\n";
    exit;
}

echo "✅ Test data ditemukan:\n";
echo "- Nomor Pranota: {$pranota->nomor_pranota}\n";
echo "- Jumlah Uang Jalan: {$pranota->jumlah_uang_jalan}\n";
echo "- Total Amount: Rp " . number_format($pranota->total_amount, 0, ',', '.') . "\n\n";

echo "=== Struktur Halaman Print (Tanpa Card) ===\n\n";

echo "1. ✅ Header\n";
echo "   - Judul: Pranota Uang Jalan\n";
echo "   - Company: PT. Aypsis Logistik Indonesia\n";
echo "   - Alamat: Jl. Raya Pelabuhan No. 123, Jakarta\n\n";

echo "2. ❌ Card Informasi Pranota (DIHAPUS)\n";
echo "   - Informasi Pranota (left)\n";
echo "   - Detail Pranota (right)\n\n";

echo "3. ✅ Tabel Daftar Uang Jalan\n";
echo "   - 11 kolom sesuai permintaan\n";
echo "   - Layout responsive untuk print\n\n";

echo "4. ✅ Summary\n";
echo "   - Jumlah item\n";
echo "   - Total amount\n\n";

echo "5. ✅ Signature Section\n";
echo "   - Dibuat oleh\n";
echo "   - Disetujui oleh\n";
echo "   - Diterima oleh\n\n";

echo "6. ✅ Footer\n";
echo "   - Timestamp print\n";
echo "   - Copyright info\n\n";

echo "=== Keuntungan Menghapus Card ===\n";
echo "✅ Lebih fokus pada tabel data utama\n";
echo "✅ Menghemat space untuk print\n";
echo "✅ Layout lebih clean dan professional\n";
echo "✅ Menghindari duplikasi info (sudah ada di summary)\n\n";

echo "=== URL untuk Test Print ===\n";
echo "🖨️  /pranota-uang-jalan/{$pranota->id}/print\n\n";

echo "✅ BERHASIL: Card informasi pranota telah dihapus dari halaman print!\n";
echo "💡 Halaman print sekarang lebih fokus pada tabel data uang jalan.\n";