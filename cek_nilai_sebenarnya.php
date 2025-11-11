<?php
// cek_nilai_sebenarnya.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK NILAI SEBENARNYA MSKU2218091 P4 ===\n\n";

$msku = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', 'MSKU2218091')
    ->where('periode', 4)
    ->first();

if ($msku) {
    echo "Raw Value dari Database:\n";
    echo "grand_total (raw): " . $msku->grand_total . "\n";
    echo "grand_total (float): " . (float)$msku->grand_total . "\n";
    echo "grand_total (formatted): Rp " . number_format($msku->grand_total, 2, ',', '.') . "\n";
    
    echo "\nPerhitungan yang Benar:\n";
    $dpp = 472973.13;
    $ppn = 52027.04;
    $pph = 9459.46;
    $grandSebelumBulat = $dpp + $ppn - $pph;
    $grandSesudahBulat = round($grandSebelumBulat);
    
    echo "DPP: " . number_format($dpp, 2, ',', '.') . "\n";
    echo "PPN: " . number_format($ppn, 2, ',', '.') . "\n";
    echo "PPH: " . number_format($pph, 2, ',', '.') . "\n";
    echo "Grand Total (sebelum bulat): " . number_format($grandSebelumBulat, 2, ',', '.') . "\n";
    echo "Grand Total (sesudah bulat): " . number_format($grandSesudahBulat, 0, ',', '.') . "\n";
    
    echo "\nApakah sudah benar?\n";
    if (abs($msku->grand_total - $grandSesudahBulat) < 0.01) {
        echo "✓ YA, grand_total sudah 515541 (dibulatkan dari 515540.71)\n";
    } else {
        echo "✗ TIDAK, grand_total masih: " . $msku->grand_total . "\n";
        echo "   Seharusnya: " . $grandSesudahBulat . "\n";
    }
}
