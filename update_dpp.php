<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

// Baca data yang perlu diupdate
if (!file_exists('data_need_update.json')) {
    echo "❌ File data_need_update.json tidak ditemukan!" . PHP_EOL;
    echo "Jalankan cek_semua_dpp.php terlebih dahulu." . PHP_EOL;
    exit(1);
}

$needUpdate = json_decode(file_get_contents('data_need_update.json'), true);

echo "=== UPDATE DPP YANG SALAH ===" . PHP_EOL;
echo "Total data yang akan diupdate: " . count($needUpdate) . PHP_EOL;
echo "Auto-updating..." . PHP_EOL;
echo PHP_EOL;
echo "=== MEMULAI UPDATE ===" . PHP_EOL;

$success = 0;
$failed = 0;
$errors = [];

foreach ($needUpdate as $index => $data) {
    try {
        $tagihan = DaftarTagihanKontainerSewa::find($data['id']);
        
        if (!$tagihan) {
            throw new \Exception("Tagihan tidak ditemukan");
        }
        
        // Update DPP, PPN, PPH, Grand Total
        $tagihan->dpp = $data['dpp_baru'];
        $tagihan->ppn = $data['ppn_baru'];
        $tagihan->pph = $data['pph_baru'];
        $tagihan->grand_total = $data['grand_total_baru'];
        
        // Update tarif type jika berbeda
        if ($tagihan->tarif !== $data['tarif_type']) {
            $tagihan->tarif = $data['tarif_type'];
        }
        
        $tagihan->save();
        
        $success++;
        
        // Progress indicator
        if (($index + 1) % 10 == 0) {
            echo "Progress: " . ($index + 1) . "/" . count($needUpdate) . " (" . round(($index + 1) / count($needUpdate) * 100, 1) . "%)" . PHP_EOL;
        }
        
    } catch (\Exception $e) {
        $failed++;
        $errors[] = [
            'id' => $data['id'],
            'kontainer' => $data['kontainer'],
            'error' => $e->getMessage()
        ];
        
        echo "❌ Error pada ID " . $data['id'] . " (" . $data['kontainer'] . "): " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL;
echo "=== HASIL UPDATE ===" . PHP_EOL;
echo "✅ Berhasil: " . $success . " data" . PHP_EOL;
echo "❌ Gagal: " . $failed . " data" . PHP_EOL;

if ($failed > 0) {
    echo PHP_EOL;
    echo "=== ERROR LOG ===" . PHP_EOL;
    foreach ($errors as $error) {
        echo "ID " . $error['id'] . " (" . $error['kontainer'] . "): " . $error['error'] . PHP_EOL;
    }
}

echo PHP_EOL;
echo "=== SELESAI ===" . PHP_EOL;

// Tampilkan contoh sebelum dan sesudah update untuk kontainer MSKU2218091
echo PHP_EOL;
echo "=== CONTOH UPDATE (MSKU2218091 Periode 4) ===" . PHP_EOL;
$tagihan = DaftarTagihanKontainerSewa::find(5058);
if ($tagihan) {
    echo "DPP: Rp " . number_format($tagihan->dpp, 2, '.', ',') . PHP_EOL;
    echo "PPN: Rp " . number_format($tagihan->ppn, 2, '.', ',') . PHP_EOL;
    echo "PPH: Rp " . number_format($tagihan->pph, 2, '.', ',') . PHP_EOL;
    echo "Grand Total: Rp " . number_format($tagihan->grand_total, 2, '.', ',') . PHP_EOL;
    echo "Tarif Type: " . $tagihan->tarif . PHP_EOL;
}
