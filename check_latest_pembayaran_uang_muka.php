<?php

/**
 * Script untuk mengecek data PembayaranUangMuka terbaru
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PembayaranUangMuka;

echo "=== Checking Latest PembayaranUangMuka ===\n\n";

$latest = PembayaranUangMuka::orderBy('id', 'desc')->take(5)->get();

foreach ($latest as $pum) {
    echo "ID: {$pum->id}\n";
    echo "Nomor: {$pum->nomor_pembayaran}\n";
    echo "Tanggal: {$pum->tanggal_pembayaran}\n";
    echo "supir_ids (raw): " . $pum->getRawOriginal('supir_ids') . "\n";
    echo "supir_ids (cast): " . json_encode($pum->supir_ids) . "\n";
    echo "jumlah_per_supir (raw): " . $pum->getRawOriginal('jumlah_per_supir') . "\n";
    echo "jumlah_per_supir (cast): " . json_encode($pum->jumlah_per_supir) . "\n";
    echo "Status: {$pum->status}\n";
    echo "---\n\n";
}
