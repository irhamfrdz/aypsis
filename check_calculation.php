<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

$tagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'DNAU2622206')
    ->where('periode', '4')
    ->first();

echo "=== Komponen Perhitungan ===\n";
echo "DPP: Rp " . number_format($tagihan->dpp, 2, ',', '.') . "\n";
echo "Adjustment: Rp " . number_format($tagihan->adjustment, 2, ',', '.') . "\n";
echo "PPN: Rp " . number_format($tagihan->ppn, 2, ',', '.') . "\n";
echo "PPH: Rp " . number_format($tagihan->pph ?? 0, 2, ',', '.') . "\n";
echo "\n";
echo "=== Perhitungan Auto ===\n";
$calculated = ($tagihan->dpp + $tagihan->adjustment) + $tagihan->ppn - ($tagihan->pph ?? 0);
echo "({$tagihan->dpp} + {$tagihan->adjustment}) + {$tagihan->ppn} - " . ($tagihan->pph ?? 0) . "\n";
echo "= Rp " . number_format($calculated, 2, ',', '.') . "\n";
echo "\n";
echo "=== Nilai di Database ===\n";
echo "Grand Total: Rp " . number_format($tagihan->grand_total, 2, ',', '.') . "\n";
