<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "=== PERBAIKAN INKONSISTENSI TANGGAL ===\n\n";

$tagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'INKU6744298')
    ->where('periode', 6)
    ->first();

echo "📋 DATA SAAT INI:\n";
echo "Masa: {$tagihan->masa}\n";
echo "Tanggal Awal: {$tagihan->tanggal_awal}\n";
echo "Tanggal Akhir: {$tagihan->tanggal_akhir}\n";
echo "DPP: " . number_format((float)$tagihan->dpp, 0, ',', '.') . "\n\n";

echo "🤔 PERTANYAAN: Mana yang benar?\n";
echo "Opsi 1: Masa (5-23 Des = 19 hari) -> DPP = 798,798\n";
echo "Opsi 2: Database (5-24 Des = 20 hari) -> DPP = 840,840\n\n";

echo "Mari kita ubah tanggal_akhir sesuai dengan masa (23 Des):\n";

try {
    $tagihan->update([
        'tanggal_akhir' => '2024-12-23 00:00:00'
    ]);

    echo "✅ Tanggal akhir diubah ke 2024-12-23\n";
    echo "✅ Sekarang konsisten dengan field 'masa'\n";

    // Refresh data
    $tagihan->refresh();
    echo "\n📋 DATA SETELAH DIPERBAIKI:\n";
    echo "Masa: {$tagihan->masa}\n";
    echo "Tanggal Awal: {$tagihan->tanggal_awal}\n";
    echo "Tanggal Akhir: {$tagihan->tanggal_akhir}\n";
    echo "DPP: " . number_format((float)$tagihan->dpp, 0, ',', '.') . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";
