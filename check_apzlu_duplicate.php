<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Detail APZLU3960241 ===\n\n";

$tagihan = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', 'APZLU3960241')
    ->orderBy('periode')
    ->orderBy('id')
    ->get();

echo "Total records: " . $tagihan->count() . "\n\n";

foreach ($tagihan as $t) {
    $pranota = $t->status_pranota ?? 'NULL';
    $invoice = $t->invoice_id ?? 'NULL';
    echo "ID: {$t->id} | Periode {$t->periode} | {$t->masa}\n";
    echo "  Pranota: {$pranota} | Invoice: {$invoice}\n";
    echo "  Created: {$t->created_at}\n";
    echo "\n";
}
