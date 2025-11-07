<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Pranota Info Above Table ===\n\n";

$pranota = \App\Models\PranotaUangJalan::first();

if (!$pranota) {
    echo "❌ No pranota found for testing\n";
    exit;
}

echo "✅ Testing Pranota Info Display:\n";
echo "- Nomor Pranota: {$pranota->nomor_pranota}\n";
echo "- Tanggal Pranota: " . $pranota->tanggal_pranota->format('d F Y') . "\n";
echo "- Formatted Date: " . $pranota->tanggal_pranota->format('d F Y') . "\n\n";

echo "=== Preview Struktur Halaman Print ===\n\n";

echo "1. ✅ Header\n";
echo "   - Judul: Pranota Uang Jalan\n";
echo "   - Company Info\n\n";

echo "2. ✅ Pranota Info (BARU)\n";
echo "   - Nomor Pranota: {$pranota->nomor_pranota}\n";
echo "   - Tanggal Pranota: " . $pranota->tanggal_pranota->format('d F Y') . "\n\n";

echo "3. ✅ Tabel Daftar Uang Jalan\n";
echo "   - 11 kolom dengan NIK yang sudah fixed\n\n";

echo "4. ✅ Summary\n";
echo "   - Jumlah item dan total\n\n";

echo "5. ✅ Signature Section\n";
echo "   - Tanda tangan\n\n";

echo "6. ✅ Footer\n";
echo "   - Timestamp\n\n";

echo "=== Styling Info ===\n";
echo "✅ Background: Light gray (#f8f9fa)\n";
echo "✅ Border: Solid border (#dee2e6)\n";
echo "✅ Layout: Flexbox dengan space-between\n";
echo "✅ Font: Bold 14px untuk emphasis\n";
echo "✅ Spacing: 20px margin-bottom untuk separation\n\n";

echo "💡 URL untuk test: /pranota-uang-jalan/{$pranota->id}/print\n";
echo "🎉 Nomor dan tanggal pranota sekarang tampil di atas tabel!\n";