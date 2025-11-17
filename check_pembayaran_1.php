<?php

/**
 * Script untuk mengecek data raw PembayaranUangMuka ID 1
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PembayaranUangMuka;

$pembayaran = PembayaranUangMuka::find(1);

echo "ID: " . $pembayaran->id . "\n";
echo "Nomor: " . $pembayaran->nomor_pembayaran . "\n";
echo "supir_ids (raw): " . $pembayaran->getRawOriginal('supir_ids') . "\n";
echo "supir_ids (cast): " . json_encode($pembayaran->supir_ids) . "\n";
echo "jumlah_per_supir (raw): " . $pembayaran->getRawOriginal('jumlah_per_supir') . "\n";
echo "jumlah_per_supir (cast): " . json_encode($pembayaran->jumlah_per_supir) . "\n";
