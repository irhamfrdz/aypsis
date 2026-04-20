<?php

use App\Models\PembayaranPranotaRitKenek;
use App\Models\PranotaUangRitKenek;
use App\Models\CoaTransaction;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$no_pranota = 'PURK-04-26-000012';
echo "Searching for Pranota: $no_pranota\n";

$pranota = PranotaUangRitKenek::where('no_pranota', $no_pranota)->first();
if (!$pranota) {
    die("Pranota $no_pranota not found\n");
}

echo "Pranota Status: {$pranota->status}, Tanggal Bayar: {$pranota->tanggal_bayar}\n";

$pembayaran = PembayaranPranotaRitKenek::whereHas('pranotaUangRitKeneks', function($q) use ($no_pranota) {
    $q->where('no_pranota', $no_pranota);
})->first();

if (!$pembayaran) {
    echo "NO Pembayaran record found for this pranota!\n";
} else {
    echo "Pembayaran Found:\n";
    echo " - ID: {$pembayaran->id}\n";
    echo " - Nomor Pembayaran: {$pembayaran->nomor_pembayaran}\n";
    echo " - Bank: {$pembayaran->bank}\n";
    echo " - Total: {$pembayaran->total_pembayaran}\n";
    
    $reference = $pembayaran->nomor_pembayaran;
    $transactions = DB::table('coa_transactions')->where('nomor_referensi', $reference)->get();
    
    if ($transactions->isEmpty()) {
        echo "COA Transactions: NONE found for reference $reference\n";
    } else {
        echo "COA Transactions Found (" . $transactions->count() . "):\n";
        foreach ($transactions as $tx) {
            echo " - Account: {$tx->coa_id}, Debit: {$tx->debit}, Credit: {$tx->kredit}, Date: {$tx->tanggal_transaksi}\n";
        }
    }
}
